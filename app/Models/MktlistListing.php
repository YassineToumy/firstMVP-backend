<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MktlistListing extends Model
{
    protected $table = 'mktlist_listings';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'rooms' => 'array',
        'features' => 'array',
        'images' => 'array',
        'property_details' => 'array',
        'created_at' => 'datetime',
    ];
}