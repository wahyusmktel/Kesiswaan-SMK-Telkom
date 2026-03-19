<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortUrl extends Model
{
    protected $fillable = [
        'user_id',
        'original_url',
        'short_code',
        'clicks',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
