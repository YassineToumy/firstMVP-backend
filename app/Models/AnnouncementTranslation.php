<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnouncementTranslation extends Model
{
    protected $table = 'announcement_translations';

    public $timestamps = false;

    protected $fillable = [
        'announcement_id',
        'locale',
        'title',
        'description',
        'features_translated',
        'translated_at',
    ];

    protected $casts = [
        'features_translated' => 'array',
        'translated_at'       => 'datetime',
    ];

    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class);
    }
}
