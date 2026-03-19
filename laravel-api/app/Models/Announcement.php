<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Announcement extends Model
{
    protected $table = 'announcements';

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
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(AnnouncementTranslation::class);
    }

    public function translation(string $locale): HasOne
    {
        return $this->hasOne(AnnouncementTranslation::class)
            ->where('locale', $locale);
    }

    /**
     * Safely decode a JSON column that may contain Arabic commas (،) instead of standard commas.
     * Falls back to empty array on any parse failure.
     */
    private function safeJsonDecode(?string $value): array
    {
        if (empty($value)) return [];
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }
        // Replace Arabic comma (U+060C) with standard comma and retry
        $fixed = str_replace('،', ',', $value);
        $decoded = json_decode($fixed, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }
        return [];
    }

    public function getInteriorFeaturesAttribute(mixed $value): array
    {
        return is_string($value) ? $this->safeJsonDecode($value) : (is_array($value) ? $value : []);
    }

    public function getExteriorFeaturesAttribute(mixed $value): array
    {
        return is_string($value) ? $this->safeJsonDecode($value) : (is_array($value) ? $value : []);
    }

    public function getOtherFeaturesAttribute(mixed $value): array
    {
        return is_string($value) ? $this->safeJsonDecode($value) : (is_array($value) ? $value : []);
    }

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