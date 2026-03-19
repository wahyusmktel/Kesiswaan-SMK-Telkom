<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IqQuestion extends Model
{
    protected $fillable = [
        'question_text',
        'image_path',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_option',
    ];
}
