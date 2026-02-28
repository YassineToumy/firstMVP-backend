<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MubawabListing extends Model
{
    protected $table = 'mubawab_listings';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'price' => 'float',
        'area_m2' => 'float',
        'rooms' => 'integer',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'features' => 'array',
        'main_features' => 'array',
        'images' => 'array',
        'created_at' => 'datetime',
    ];
}