<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpmbTpaSesi extends Model
{
    use HasFactory;

    protected $table = 'spmb_tpa_sesis';

    protected $fillable = [
        'spmb_peserta_seleksi_id',
        'spmb_piket_tanggal_id',
        'tahun_ajaran',
        'status',
        'waktu_mulai',
        'waktu_selesai',
        'total_soal',
        'total_benar',
        'total_salah',
        'total_kosong',
        'skor_bindo',
        'skor_mtk',
        'skor_bing',
        'skor_tik',
        'skor_total',
        'hasil_evaluasi',
        'catatan',
        'pengawas_user_id',
        'pengawas_nama',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'total_soal' => 'integer',
        'total_benar' => 'integer',
        'total_salah' => 'integer',
        'total_kosong' => 'integer',
        'skor_bindo' => 'decimal:2',
        'skor_mtk' => 'decimal:2',
        'skor_bing' => 'decimal:2',
        'skor_tik' => 'decimal:2',
        'skor_total' => 'decimal:2',
    ];

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(SpmbPesertaSeleksi::class, 'spmb_peserta_seleksi_id');
    }

    public function tanggal(): BelongsTo
    {
        return $this->belongsTo(SpmbPiketTanggal::class, 'spmb_piket_tanggal_id');
    }

    public function pengawas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengawas_user_id');
    }

    public function jawabans(): HasMany
    {
        return $this->hasMany(SpmbTpaJawaban::class, 'spmb_tpa_sesi_id');
    }
}
