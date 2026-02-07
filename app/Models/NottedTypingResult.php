<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NottedTypingResult extends Model
{
    protected $fillable = [
        'user_id',
        'kpm',
        'accuracy',
        'correct_words',
        'wrong_words',
        'total_chars',
        'language',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
