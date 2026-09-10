<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramUserLink extends Model
{
    protected $fillable = ['telegram_bot_id', 'user_id', 'chat_id', 'telegram_user_id', 'telegram_username', 'telegram_name', 'linked_at', 'last_interaction_at', 'commands_hash'];

    protected $casts = ['linked_at' => 'datetime', 'last_interaction_at' => 'datetime'];

    public function bot()
    {
        return $this->belongsTo(TelegramBot::class, 'telegram_bot_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function conversations()
    {
        return $this->hasMany(TelegramConversation::class);
    }
}
