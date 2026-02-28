<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for the `all_listings` unified SQL VIEW.
 * This VIEW normalizes common fields across all 4 sources.
 */
class AllListing extends Model
{
    protected $table = 'all_listings';
    public $timestamps = false;
    public $incrementing = false;

    protected $casts = [
        'price' => 'float',
        'surface_m2' => 'float',
        'rooms' => 'integer',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'is_furnished' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'photos_count' => 'integer',
        'created_at' => 'datetime',
    ];
}