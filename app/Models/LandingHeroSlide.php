<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingHeroSlide extends Model
{
    protected $fillable = [
        'eyebrow',
        'title',
        'description',
        'image_path',
        'cta_label',
        'cta_url',
        'sort_order',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): string
    {
        return asset('storage/'.ltrim($this->image_path, '/'));
    }
}
