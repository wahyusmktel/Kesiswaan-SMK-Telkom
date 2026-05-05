<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KantinProfile extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'phone_number',
        'is_open',
        'banner_image',
    ];

    protected $casts = [
        'is_open' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
