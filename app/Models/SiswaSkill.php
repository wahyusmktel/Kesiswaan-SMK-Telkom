<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiswaSkill extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'percentage',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
