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
        'landing_popup_enabled',
        'landing_popup_type',
        'landing_popup_title',
        'landing_popup_description',
        'landing_popup_cta_text',
        'landing_popup_cta_url',
        'landing_popup_frequency',
        'stella_ai_base_url',
        'stella_ai_api_key',
        'stella_ai_chat_model',
        'stella_ai_models',
        'stella_ai_image_model',
        'stella_ai_enabled',
    ];

    protected $casts = [
        'allow_registration' => 'boolean',
        'transformasi_slider_images' => 'array',
        'landing_popup_enabled' => 'boolean',
        'stella_ai_enabled' => 'boolean',
        'stella_ai_api_key' => 'encrypted',
        'stella_ai_models' => 'array',
    ];

    protected $hidden = [
        'stella_ai_api_key',
    ];
}
