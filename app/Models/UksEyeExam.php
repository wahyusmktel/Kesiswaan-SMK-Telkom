<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UksEyeExam extends Model
{
    protected $fillable = [
        'handled_by',
        'examinee_type',
        'master_siswa_id',
        'user_id',
        'examined_at',
        'color_blind_result',
        'color_blind_notes',
        'visual_acuity_right',
        'visual_acuity_left',
        'eye_health_findings',
        'recommendation',
        'conclusion',
        'notes',
    ];

    protected $casts = [
        'examined_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(MasterSiswa::class, 'master_siswa_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function getExamineeNameAttribute(): string
    {
        return $this->examinee_type === 'siswa'
            ? ($this->student?->nama_lengkap ?? 'Siswa')
            : ($this->employee?->name ?? 'Pegawai');
    }

    public function getExamineeIdentityAttribute(): string
    {
        if ($this->examinee_type === 'siswa') {
            $className = $this->student?->rombels->first()?->kelas?->nama_kelas ?? '-';
            return trim(($this->student?->nis ?? '-') . ' / ' . $className);
        }

        return $this->employee?->email ?? '-';
    }

    public function getColorBlindLabelAttribute(): string
    {
        return match ($this->color_blind_result) {
            'normal' => 'Normal',
            'partial' => 'Indikasi buta warna parsial',
            'total' => 'Indikasi buta warna total',
            default => 'Perlu pemeriksaan ulang',
        };
    }

    public function getConclusionLabelAttribute(): string
    {
        return match ($this->conclusion) {
            'baik' => 'Kesehatan mata baik',
            'perlu_observasi' => 'Perlu observasi',
            'perlu_rujukan' => 'Perlu rujukan',
            default => 'Selesai diperiksa',
        };
    }
}
