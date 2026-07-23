<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone_number',
        'session_id',
        'provider',
        'server_url',
        'api_key',
        'status',
        'qr_code_data',
        'is_active',
        'is_default',
        'last_connected_at',
        'webhook_url',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'last_connected_at' => 'datetime',
        'settings' => 'array',
    ];

    public function logs()
    {
        return $this->hasMany(WhatsappLog::class);
    }
}
