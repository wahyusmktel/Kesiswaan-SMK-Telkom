<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class SpmbTesBacaQuranSesi extends Model
{
    use HasFactory;

    protected $table = 'spmb_tes_baca_quran_sesis';

    protected $fillable = [
        'spmb_peserta_seleksi_id',
        'spmb_piket_tanggal_id',
        'tahun_ajaran',
        'status',
        'nilai_tajwid',
        'nilai_makharijul_huruf',
        'nilai_kelancaran',
        'rekapitulasi_hasil',
        'skor_angka',
        'file_rekaman',
        'catatan',
        'penguji_user_id',
        'penguji_nama',
        'waktu_tes',
    ];

    protected $casts = [
        'skor_angka' => 'decimal:2',
        'waktu_tes' => 'datetime',
    ];

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(SpmbPesertaSeleksi::class, 'spmb_peserta_seleksi_id');
    }

    public function tanggal(): BelongsTo
    {
        return $this->belongsTo(SpmbPiketTanggal::class, 'spmb_piket_tanggal_id');
    }

    public function penguji(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penguji_user_id');
    }

    public function jawabans(): HasMany
    {
        return $this->hasMany(SpmbTesBacaQuranJawaban::class, 'spmb_tes_baca_quran_sesi_id');
    }

    public function getFileRekamanUrlAttribute(): ?string
    {
        if (empty($this->file_rekaman)) {
            return null;
        }

        if (str_starts_with($this->file_rekaman, 'http://') || str_starts_with($this->file_rekaman, 'https://')) {
            return $this->file_rekaman;
        }

        if (Storage::disk('public')->exists($this->file_rekaman)) {
            return Storage::disk('public')->url($this->file_rekaman);
        }

        return asset('storage/' . $this->file_rekaman);
    }
}
