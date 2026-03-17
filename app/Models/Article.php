<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    protected $fillable = [
        'title', 'slug', 'meta_title', 'meta_description',
        'excerpt', 'image_url', 'alt_image', 'country',
        'read_time', 'article_contents', 'status', 'admin_id', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'read_time'    => 'integer',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
