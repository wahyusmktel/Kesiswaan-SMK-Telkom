<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramLog extends Model
{
    protected $fillable = ['telegram_bot_id', 'recipient_user_id', 'chat_id', 'recipient_name', 'message', 'type', 'event_key', 'notification_date', 'status', 'error_message', 'response_data', 'sent_at'];

    protected $casts = ['notification_date' => 'date', 'response_data' => 'array', 'sent_at' => 'datetime'];

    public function bot()
    {
        return $this->belongsTo(TelegramBot::class, 'telegram_bot_id');
    }
}
