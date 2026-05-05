<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KantinMenu extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'category',
        'is_available',
        'images',
    ];

    protected $casts = [
        'images' => 'array',
        'is_available' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
