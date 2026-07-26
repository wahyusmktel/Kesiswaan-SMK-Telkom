<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingTicker extends Model
{
    protected $fillable = [
        'text',
        'url',
        'sort_order',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
