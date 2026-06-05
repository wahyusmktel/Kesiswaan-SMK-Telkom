<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FingerprintDevice extends Model
{
    protected $fillable = [
        'name',
        'ip_address',
        'port',
        'serial_number',
        'location',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'port' => 'integer',
    ];

    public function fingerprintUsers(): HasMany
    {
        return $this->hasMany(FingerprintUser::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(FingerprintAttendance::class);
    }
}
