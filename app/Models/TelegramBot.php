<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramBot extends Model
{
    protected $fillable = ['name', 'slug', 'purpose', 'bot_username', 'bot_token', 'webhook_secret', 'status', 'last_error', 'is_active', 'last_verified_at'];

    protected $hidden = ['bot_token', 'webhook_secret'];

    protected $casts = [
        'bot_token' => 'encrypted',
        'webhook_secret' => 'encrypted',
        'is_active' => 'boolean',
        'last_verified_at' => 'datetime',
    ];

    public function userLinks()
    {
        return $this->hasMany(TelegramUserLink::class);
    }

    public function logs()
    {
        return $this->hasMany(TelegramLog::class);
    }
}
