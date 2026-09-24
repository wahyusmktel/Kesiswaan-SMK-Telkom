<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrakerinBimbinganBeritaAcara extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'prakerin_bimbingan_berita_acaras';

    protected $fillable = [
        'prakerin_bimbingan_tahap_id',
        'prakerin_bimbingan_laporan_id',
        'user_id',
        'nomor_berita_acara',
        'jenis',
        'revisi_ke',
        'judul_tahap',
        'catatan_pembimbing',
        'total_anotasi',
        'file_pdf_path',
        'diterbitkan_at',
    ];

    protected $casts = [
        'diterbitkan_at' => 'datetime',
        'revisi_ke' => 'integer',
        'total_anotasi' => 'integer',
    ];

    public function tahap()
    {
        return $this->belongsTo(PrakerinBimbinganTahap::class, 'prakerin_bimbingan_tahap_id');
    }

    public function laporan()
    {
        return $this->belongsTo(PrakerinBimbinganLaporan::class, 'prakerin_bimbingan_laporan_id');
    }

    public function pembimbing()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getJudulDokumenAttribute(): string
    {
        if ($this->jenis === 'disetujui') {
            return 'Berita Acara Pengesahan (ACC)';
        }

        return 'Berita Acara Revisi' . ($this->revisi_ke ? ' (Revisi ke-' . $this->revisi_ke . ')' : '');
    }

    public function getIsAccAttribute(): bool
    {
        return $this->jenis === 'disetujui';
    }
}
