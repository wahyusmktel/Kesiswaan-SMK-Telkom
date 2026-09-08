<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FingerprintAutoSyncSetting extends Model
{
    protected $fillable = [
        'is_enabled',
        'run_time',
        'second_run_time',
        'notifications_enabled',
        'notification_time',
        'notification_channel',
        'telegram_bot_id',
        'last_notification_dispatched_at',
        'range_type',
        'device_ids',
        'last_dispatched_at',
        'last_progress_ids',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'notifications_enabled' => 'boolean',
        'device_ids' => 'array',
        'last_dispatched_at' => 'datetime',
        'last_notification_dispatched_at' => 'datetime',
        'last_progress_ids' => 'array',
    ];

    public static function getSetting(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'is_enabled' => false,
                'run_time' => '23:30:00',
                'notifications_enabled' => true,
                'notification_time' => '18:00:00',
                'notification_channel' => 'whatsapp',
                'range_type' => '1_day',
                'device_ids' => null,
            ]
        );
    }
}
