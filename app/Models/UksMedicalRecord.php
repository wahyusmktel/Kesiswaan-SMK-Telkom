<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UksMedicalRecord extends Model
{
    protected $fillable = [
        'master_siswa_id',
        'handled_by',
        'visited_at',
        'complaint',
        'symptoms',
        'anamnesis',
        'diagnosis',
        'treatment',
        'medicine',
        'temperature',
        'blood_pressure',
        'pulse',
        'oxygen_saturation',
        'condition',
        'disposition',
        'rest_until',
        'referral_facility_type',
        'referral_facility_name',
        'referral_reason',
        'parent_notification',
        'notes',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
        'rest_until' => 'datetime',
        'symptoms' => 'array',
        'temperature' => 'decimal:1',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(MasterSiswa::class, 'master_siswa_id');
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function getDispositionLabelAttribute(): string
    {
        return match ($this->disposition) {
            'istirahat_uks' => 'Istirahat di UKS',
            'pulang' => 'Dipulangkan',
            'rujukan' => 'Rujukan',
            default => 'Kembali ke kelas',
        };
    }

    public function getConditionLabelAttribute(): string
    {
        return ucfirst($this->condition);
    }
}
