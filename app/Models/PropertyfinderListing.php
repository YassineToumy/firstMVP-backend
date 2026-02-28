<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyfinderListing extends Model
{
    protected $table = 'propertyfinder_listings';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'price_value' => 'float',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'property_size' => 'array',
        'amenities' => 'array',
        'images' => 'array',
        'price_insights' => 'array',
        'created_at' => 'datetime',
    ];
}