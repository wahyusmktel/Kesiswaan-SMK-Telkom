<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramConversation extends Model
{
    protected $fillable = [
        'telegram_bot_id',
        'telegram_user_link_id',
        'flow',
        'step',
        'payload',
        'expires_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'expires_at' => 'datetime',
    ];

    public function bot()
    {
        return $this->belongsTo(TelegramBot::class, 'telegram_bot_id');
    }

    public function link()
    {
        return $this->belongsTo(TelegramUserLink::class, 'telegram_user_link_id');
    }
}
