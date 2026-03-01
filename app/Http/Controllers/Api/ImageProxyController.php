<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ImageProxyController extends Controller
{
    private array $allowedDomains = [
        'static.shared.propertyfinder.com',
        'images.weserv.nl',
        'ddfcdn.realtor.ca',
        'mubawab-media.com',
        'www.mubawab.tn',
        'bienici.com',
        'photos.bienici.com',
    ];

    /**
     * GET /api/v1/image-proxy?url=...
     */
    public function proxy(Request $request)
    {
        $url = $request->query('url');

        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json(['error' => 'Invalid URL'], 400);
        }

        $host = parse_url($url, PHP_URL_HOST);
        $allowed = false;
        foreach ($this->allowedDomains as $domain) {
            if (str_contains($host, $domain)) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            return response()->json(['error' => 'Domain not allowed'], 403);
        }

        // Cache for 24h
        $cacheKey = 'img_' . md5($url);
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return response($cached['body'])
                ->header('Content-Type', $cached['content_type'])
                ->header('Cache-Control', 'public, max-age=86400')
                ->header('X-Cache', 'HIT');
        }

        // Determine referer based on domain
        $referer = 'https://www.propertyfinder.eg/';
        if (str_contains($url, 'mubawab')) {
            $referer = 'https://www.mubawab.tn/';
        } elseif (str_contains($url, 'bienici')) {
            $referer = 'https://www.bienici.com/';
        } elseif (str_contains($url, 'realtor.ca') || str_contains($url, 'weserv.nl')) {
            $referer = 'https://www.mktlist.ca/';
        }

        // Try multiple approaches
        $approaches = [
            // Approach 1: Full browser-like headers with Referer
            [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
                'Referer' => $referer,
                'Origin' => rtrim($referer, '/'),
                'Accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.9',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Sec-Ch-Ua' => '"Chromium";v="131", "Not_A Brand";v="24"',
                'Sec-Ch-Ua-Mobile' => '?0',
                'Sec-Ch-Ua-Platform' => '"Windows"',
                'Sec-Fetch-Dest' => 'image',
                'Sec-Fetch-Mode' => 'no-cors',
                'Sec-Fetch-Site' => 'cross-site',
            ],
            // Approach 2: Same-origin fetch (pretend we're the site itself)
            [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
                'Referer' => $referer,
                'Accept' => 'image/*,*/*;q=0.8',
                'Sec-Fetch-Dest' => 'image',
                'Sec-Fetch-Mode' => 'no-cors',
                'Sec-Fetch-Site' => 'same-site',
            ],
            // Approach 3: Minimal headers (some CDNs block over-specified requests)
            [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
                'Accept' => '*/*',
            ],
            // Approach 4: No referer, just curl-like
            [
                'User-Agent' => 'curl/8.0',
                'Accept' => '*/*',
            ],
        ];

        foreach ($approaches as $i => $headers) {
            try {
                $response = Http::withHeaders($headers)
                    ->withOptions([
                        'allow_redirects' => ['max' => 5, 'track_redirects' => true],
                        'verify' => false,
                    ])
                    ->timeout(15)
                    ->get($url);

                if ($response->successful()) {
                    $contentType = $response->header('Content-Type') ?? 'image/jpeg';

                    // Make sure it's actually an image
                    if (!str_starts_with($contentType, 'image/') && !str_contains($contentType, 'octet-stream')) {
                        continue;
                    }

                    $body = $response->body();

                    // Cache for 24h
                    Cache::put($cacheKey, [
                        'body' => $body,
                        'content_type' => $contentType,
                    ], 86400);

                    return response($body)
                        ->header('Content-Type', $contentType)
                        ->header('Cache-Control', 'public, max-age=86400')
                        ->header('X-Cache', 'MISS')
                        ->header('X-Approach', (string)$i);
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // All approaches failed — try weserv.nl as last resort (free image proxy)
        try {
            $weservUrl = 'https://images.weserv.nl/?url=' . urlencode($url) . '&default=1';
            $response = Http::timeout(10)->get($weservUrl);

            if ($response->successful()) {
                $contentType = $response->header('Content-Type') ?? 'image/jpeg';
                $body = $response->body();

                Cache::put($cacheKey, [
                    'body' => $body,
                    'content_type' => $contentType,
                ], 86400);

                return response($body)
                    ->header('Content-Type', $contentType)
                    ->header('Cache-Control', 'public, max-age=86400')
                    ->header('X-Cache', 'MISS')
                    ->header('X-Approach', 'weserv');
            }
        } catch (\Exception $e) {
            // weserv also failed
        }

        return response()->json(['error' => 'All fetch approaches failed'], 502);
    }
}