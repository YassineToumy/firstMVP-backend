<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BieniciListing extends Model
{
    protected $table = 'bienici_listings';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $casts = [
        'price' => 'float',
        'surface_area' => 'float',
        'rooms_quantity' => 'integer',
        'bedrooms_quantity' => 'integer',
        'bathrooms_quantity' => 'integer',
        'is_furnished' => 'boolean',
        'new_property' => 'boolean',
        'has_elevator' => 'boolean',
        'price_has_decreased' => 'boolean',
        'ad_created_by_pro' => 'boolean',
        'charges' => 'float',
        'latitude' => 'float',
        'longitude' => 'float',
        'photos_count' => 'integer',
        'equipment_score' => 'integer',
        'energy_numeric' => 'integer',
        'created_at' => 'datetime',
    ];
}