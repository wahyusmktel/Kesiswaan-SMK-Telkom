<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = [
        'school_name',
        'logo',
        'favicon',
        'phone',
        'email',
        'address',
        'allow_registration'
    ];

    protected $casts = [
        'allow_registration' => 'boolean',
    ];
}
