<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\AnnouncementTranslation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranslationService
{
    private const SUPPORTED_LOCALES = ['fr', 'en', 'ar', 'es'];

    private const LOCALE_NAMES = [
        'fr' => 'French',
        'en' => 'English',
        'ar' => 'Arabic',
        'es' => 'Spanish',
    ];

    private const API_URL = 'https://api.anthropic.com/v1/messages';

    // Translate a single announcement into the given locale.
    // Returns true on success, false on failure.
    public function translate(Announcement $announcement, string $locale): bool
    {
        if (!in_array($locale, self::SUPPORTED_LOCALES)) {
            return false;
        }

        $apiKey = config('services.anthropic.key');
        if (!$apiKey) {
            Log::error('TranslationService: ANTHROPIC_API_KEY not set');
            return false;
        }

        $features = $this->extractFeatures($announcement);

        $payload = json_encode([
            'title'       => $announcement->title ?? '',
            'description' => $announcement->description ?? '',
            'features'    => $features,
        ], JSON_UNESCAPED_UNICODE);

        $language = self::LOCALE_NAMES[$locale];

        $prompt = <<<PROMPT
You are a real estate listing translator. Translate the following JSON fields to {$language}.
Return ONLY valid JSON with exactly these keys: "title", "description", "features" (array of strings).
Keep numbers, proper nouns, addresses, and brand names untranslated.
If a field is empty, return an empty string or empty array for that field.

Input JSON:
{$payload}
PROMPT;

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'x-api-key'         => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type'      => 'application/json',
                ])
                ->post(self::API_URL, [
                    'model'      => 'claude-haiku-4-5-20251001',
                    'max_tokens' => 1024,
                    'messages'   => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

            if (!$response->successful()) {
                Log::warning('TranslationService: API error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return false;
            }

            $content = $response->json('content.0.text', '');
            $translated = $this->parseJson($content);

            if (!$translated) {
                Log::warning('TranslationService: failed to parse response', ['content' => $content]);
                return false;
            }

            AnnouncementTranslation::updateOrCreate(
                ['announcement_id' => $announcement->id, 'locale' => $locale],
                [
                    'title'               => $translated['title'] ?? null,
                    'description'         => $translated['description'] ?? null,
                    'features_translated' => !empty($translated['features'])
                        ? array_values(array_filter((array) $translated['features'], fn($f) => is_string($f) && strlen(trim($f)) >= 2 && ltrim($f)[0] !== '{' && ltrim($f)[0] !== '['))
                        : null,
                    'translated_at'       => now(),
                ]
            );

            return true;

        } catch (\Throwable $e) {
            Log::error('TranslationService: exception', ['message' => $e->getMessage()]);
            return false;
        }
    }

    // Extract feature strings from other_features.features array
    private function extractFeatures(Announcement $announcement): array
    {
        $other = $announcement->other_features;
        if (is_array($other) && isset($other['features']) && is_array($other['features'])) {
            return array_values(array_filter($other['features'], function ($f) {
                if (!is_string($f) || strlen(trim($f)) < 2) return false;
                $t = ltrim($f);
                // Skip raw JSON objects/arrays
                return $t[0] !== '{' && $t[0] !== '[';
            }));
        }
        return [];
    }

    // Parse the JSON block from Claude's response (handles markdown code fences)
    private function parseJson(string $text): ?array
    {
        // Strip ```json ... ``` fences if present
        $text = preg_replace('/```(?:json)?\s*([\s\S]*?)```/', '$1', $text);
        $text = trim($text);

        $decoded = json_decode($text, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // Try to extract a JSON object from the text
        if (preg_match('/\{[\s\S]*\}/m', $text, $matches)) {
            $decoded = json_decode($matches[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }
}
