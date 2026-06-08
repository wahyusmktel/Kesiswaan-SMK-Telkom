<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FingerprintSecurityShiftAssignment extends Model
{
    protected $fillable = [
        'app_user_id',
        'fingerprint_security_shift_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'app_user_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(FingerprintSecurityShift::class, 'fingerprint_security_shift_id');
    }
}
