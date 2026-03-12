<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $table = 'announcements';
    public $timestamps = false;

    protected $casts = [
        'price'             => 'float',
        'price_per_m2'      => 'float',
        'latitude'          => 'float',
        'longitude'         => 'float',
        'bedrooms'          => 'integer',
        'bathrooms'         => 'integer',
        'interior_features' => 'array',
        'exterior_features' => 'array',
        'other_features'    => 'array',
        'extra_data'        => 'array',
        'created_at'        => 'datetime',
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