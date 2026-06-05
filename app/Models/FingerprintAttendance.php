<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FingerprintAttendance extends Model
{
    protected $fillable = [
        'fingerprint_device_id',
        'uid',
        'user_id',
        'app_user_id',
        'timestamp',
        'status',
        'punch',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(FingerprintDevice::class, 'fingerprint_device_id');
    }

    public function appUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'app_user_id');
    }
}
