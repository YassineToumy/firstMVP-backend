<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class PropertyType extends Model
{
    use HasTranslations;

    protected $fillable = ['code', 'name', 'variants'];
    public array $translatable = ['name'];
    protected $casts = ['variants' => 'array'];
}
