<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpmbPiketTanggal extends Model
{
    use HasFactory;

    protected $table = 'spmb_piket_tanggals';

    protected $fillable = [
        'spmb_piket_kelompok_id',
        'tanggal',
        'hari',
        'waktu',
        'status',
        'keterangan',
        'foto_eviden_absen_siswa',
        'foto_eviden_absen_ortu',
        'catatan_petugas',
        'laporan_petugas_by',
        'laporan_petugas_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'laporan_petugas_at' => 'datetime',
    ];

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(SpmbPiketKelompok::class, 'spmb_piket_kelompok_id');
    }

    public function absensis(): HasMany
    {
        return $this->hasMany(SpmbPiketAbsensi::class, 'spmb_piket_tanggal_id');
    }

    public function beritaAcaras(): HasMany
    {
        return $this->hasMany(SpmbBeritaAcara::class, 'spmb_piket_tanggal_id');
    }

    public function beritaAcara()
    {
        return $this->hasOne(SpmbBeritaAcara::class, 'spmb_piket_tanggal_id')->latestOfMany();
    }

    public function pesertaSeleksis(): HasMany
    {
        return $this->hasMany(SpmbPesertaSeleksi::class, 'spmb_piket_tanggal_id');
    }

    public function penggantis(): HasMany
    {
        return $this->hasMany(SpmbPiketPengganti::class, 'spmb_piket_tanggal_id');
    }

    public function laporanPetugasBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'laporan_petugas_by');
    }

    public function getFormattedTanggalAttribute(): string
    {
        if (! $this->tanggal) {
            return '-';
        }

        return Carbon::parse($this->tanggal)->locale('id')->translatedFormat('d M Y');
    }

    public function getFotoEvidenAbsenSiswaUrlAttribute(): ?string
    {
        if (! $this->foto_eviden_absen_siswa) {
            return null;
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->foto_eviden_absen_siswa);
    }

    public function getFotoEvidenAbsenOrtuUrlAttribute(): ?string
    {
        if (! $this->foto_eviden_absen_ortu) {
            return null;
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->foto_eviden_absen_ortu);
    }
}
