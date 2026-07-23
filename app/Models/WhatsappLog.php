<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'whatsapp_device_id',
        'recipient',
        'recipient_name',
        'message',
        'type',
        'status',
        'error_message',
        'response_data',
        'sent_at',
    ];

    protected $casts = [
        'response_data' => 'array',
        'sent_at' => 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(WhatsappDevice::class, 'whatsapp_device_id');
    }
}
