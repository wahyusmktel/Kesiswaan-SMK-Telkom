<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SpmbPiketAbsensi extends Model
{
    use HasFactory;

    protected $table = 'spmb_piket_absensis';

    protected $fillable = [
        'spmb_piket_petugas_id',
        'spmb_piket_tanggal_id',
        'user_id',
        'status',
        'jam_absen',
        'foto_eviden',
        'keterangan',
        'metode_input',
        'created_by_user_id',
    ];

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(SpmbPiketPetugas::class, 'spmb_piket_petugas_id');
    }

    public function tanggal(): BelongsTo
    {
        return $this->belongsTo(SpmbPiketTanggal::class, 'spmb_piket_tanggal_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function getFotoEvidenUrlAttribute(): ?string
    {
        if (! $this->foto_eviden) {
            return null;
        }

        return Storage::disk('public')->url($this->foto_eviden);
    }

    public function getFormattedJamAbsenAttribute(): string
    {
        if (! $this->jam_absen) {
            return '-';
        }

        return Carbon::parse($this->jam_absen)->format('H:i');
    }
}
