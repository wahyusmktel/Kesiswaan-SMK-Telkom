<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranscriptReprintCorrection extends Model
{
    protected $fillable = [
        'master_siswa_id',
        'rombel_id',
        'corrected_name',
        'corrected_birth_place',
        'corrected_birth_date',
        'correction_reason',
        'updated_by',
    ];

    protected $casts = [
        'corrected_birth_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(MasterSiswa::class, 'master_siswa_id');
    }

    public function rombel()
    {
        return $this->belongsTo(Rombel::class);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function histories()
    {
        return $this->hasMany(TranscriptReprintCorrectionHistory::class)
            ->latest();
    }
}
