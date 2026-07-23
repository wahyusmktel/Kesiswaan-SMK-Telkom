<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_key',
        'title',
        'category',
        'is_enabled',
        'template_text',
        'variables',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'variables' => 'array',
    ];
}
