<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Announcement extends Model
{
    use HasTranslations;

    protected $table = 'announcements';

    /**
     * Fields stored as {"fr": "...", "en": "...", "ar": "..."}.
     * Spatie returns the value for the current app locale automatically.
     * If data is plain text (scraper hasn't migrated yet), falls back to raw value.
     */
    public array $translatable = [
        'title', 'description', 'property_type', 'property_typology', 'location',
    ];

    public function getTranslation(string $key, string $locale, bool $useFallbackLocale = true): mixed
    {
        $raw = $this->getRawOriginal($key) ?? $this->attributes[$key] ?? null;

        // Plain text — not JSON, return as-is
        if ($raw !== null && !str_starts_with(ltrim((string) $raw), '{')) {
            return $raw;
        }

        return parent::getTranslation($key, $locale, $useFallbackLocale);
    }
    protected $fillable = [
        'source', 'source_id', 'title', 'price', 'description',
        'property_typology', 'property_type', 'price_per_m2', 'url',
        'photos', 'interior_features', 'exterior_features', 'other_features',
        'location', 'longitude', 'latitude', 'bedrooms', 'bathrooms',
        'seller_name', 'seller_phone', 'currency', 'country', 'extra_data',
    ];

    protected $casts = [
        'price'       => 'float',
        'price_per_m2'=> 'float',
        'latitude'    => 'float',
        'longitude'   => 'float',
        'bedrooms'    => 'integer',
        'bathrooms'   => 'integer',
        'extra_data'          => 'array',
        'interior_features'   => 'array',
        'exterior_features'   => 'array',
        'other_features'      => 'array',
    ];

    /**
     * photos is a PostgreSQL TEXT[] column.
     * PDO returns it as "{url1,url2,...}" — parse it manually.
     */
    public function getPhotosAttribute(?string $value): array
    {
        if (empty($value)) {
            return [];
        }
        // Already a JSON array (fallback)
        if (str_starts_with($value, '[')) {
            return json_decode($value, true) ?? [];
        }
        // PostgreSQL array literal: {"url1","url2"} or {url1,url2}
        $inner = trim($value, '{}');
        if ($inner === '') {
            return [];
        }
        // Split respecting quoted values
        preg_match_all('/"(?:[^"\\\\]|\\\\.)*"|[^,]+/', $inner, $matches);
        return array_map(fn($v) => trim($v, '"'), $matches[0]);
    }
}