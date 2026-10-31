<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class SpmbWawancaraSesi extends Model
{
    use HasFactory;

    protected $table = 'spmb_wawancara_sesis';

    protected $fillable = [
        'spmb_peserta_seleksi_id',
        'spmb_piket_tanggal_id',
        'tahun_ajaran',
        'pewawancara_siswa_user_id',
        'pewawancara_siswa_nama',
        'waktu_wawancara_siswa',
        'status_wawancara_siswa',
        'skor_siswa',
        'hasil_wawancara_siswa',
        'catatan_pewawancara_siswa',
        'foto_siswa',
        'pewawancara_ortu_user_id',
        'pewawancara_ortu_nama',
        'waktu_wawancara_ortu',
        'status_wawancara_ortu',
        'skor_ortu',
        'hasil_wawancara_ortu',
        'catatan_pewawancara_ortu',
        'foto_ortu',
        'rekomendasi_akhir',
        'catatan_akhir',
    ];

    protected $casts = [
        'waktu_wawancara_siswa' => 'datetime',
        'waktu_wawancara_ortu' => 'datetime',
        'skor_siswa' => 'decimal:2',
        'skor_ortu' => 'decimal:2',
    ];

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(SpmbPesertaSeleksi::class, 'spmb_peserta_seleksi_id');
    }

    public function tanggal(): BelongsTo
    {
        return $this->belongsTo(SpmbPiketTanggal::class, 'spmb_piket_tanggal_id');
    }

    public function pewawancaraSiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pewawancara_siswa_user_id');
    }

    public function pewawancaraOrtu(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pewawancara_ortu_user_id');
    }

    public function jawabans(): HasMany
    {
        return $this->hasMany(SpmbWawancaraJawaban::class, 'spmb_wawancara_sesi_id');
    }

    public function getFotoSiswaUrlAttribute(): ?string
    {
        if (! $this->foto_siswa) {
            return null;
        }

        return Storage::disk('public')->url($this->foto_siswa);
    }

    public function getFotoOrtuUrlAttribute(): ?string
    {
        if (! $this->foto_ortu) {
            return null;
        }

        return Storage::disk('public')->url($this->foto_ortu);
    }
}
