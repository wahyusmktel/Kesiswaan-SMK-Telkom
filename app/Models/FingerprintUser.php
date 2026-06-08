<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FingerprintUser extends Model
{
    protected $fillable = [
        'fingerprint_device_id',
        'uid',
        'user_id',
        'app_user_id',
        'name',
        'role',
        'password',
        'cardno',
        'machine_registered_at',
        'last_synced_at',
    ];

    protected $casts = [
        'machine_registered_at' => 'datetime',
        'last_synced_at' => 'datetime',
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
