<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpmbTesButaWarnaSesi extends Model
{
    use HasFactory;

    protected $table = 'spmb_tes_buta_warna_sesis';

    protected $fillable = [
        'spmb_peserta_seleksi_id',
        'spmb_piket_tanggal_id',
        'tahun_ajaran',
        'status',
        'waktu_mulai',
        'waktu_selesai',
        'total_soal',
        'jawaban_benar',
        'jawaban_salah',
        'skor',
        'hasil_evaluasi',
        'catatan_petugas',
        'penguji_user_id',
        'penguji_nama',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'total_soal' => 'integer',
        'jawaban_benar' => 'integer',
        'jawaban_salah' => 'integer',
        'skor' => 'decimal:2',
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
        return $this->hasMany(SpmbTesButaWarnaJawaban::class, 'spmb_tes_buta_warna_sesi_id');
    }
}
