<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'whatsapp_device_id',
        'recipient_user_id',
        'recipient',
        'recipient_name',
        'message',
        'type',
        'event_key',
        'notification_date',
        'status',
        'error_message',
        'response_data',
        'sent_at',
    ];

    protected $casts = [
        'response_data' => 'array',
        'sent_at' => 'datetime',
        'notification_date' => 'date',
    ];

    public function device()
    {
        return $this->belongsTo(WhatsappDevice::class, 'whatsapp_device_id');
    }

    public function recipientUser()
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }
}
