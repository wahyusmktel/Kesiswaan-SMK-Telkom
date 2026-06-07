<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = [
        'school_name',
        'logo',
        'kop_surat_ukk',
        'favicon',
        'phone',
        'email',
        'address',
        'allow_registration',
        'theme',
        'transformasi_slider_images',
    ];

    protected $casts = [
        'allow_registration' => 'boolean',
        'transformasi_slider_images' => 'array',
    ];
}
