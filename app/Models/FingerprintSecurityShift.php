<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FingerprintSecurityShift extends Model
{
    protected $fillable = [
        'code',
        'name',
        'starts_at',
        'ends_at',
        'is_overnight',
    ];

    protected $casts = [
        'is_overnight' => 'boolean',
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(FingerprintSecurityShiftAssignment::class, 'fingerprint_security_shift_id');
    }
}
