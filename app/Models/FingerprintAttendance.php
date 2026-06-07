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
        'entry_source',
        'original_timestamp',
        'corrected_by',
        'correction_note',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'original_timestamp' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(FingerprintDevice::class, 'fingerprint_device_id');
    }

    public function appUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'app_user_id');
    }

    public function correctedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'corrected_by');
    }
}
