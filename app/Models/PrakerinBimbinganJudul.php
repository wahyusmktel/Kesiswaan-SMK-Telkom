<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrakerinBimbinganJudul extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'prakerin_bimbingan_juduls';

    protected $fillable = [
        'prakerin_bimbingan_laporan_id',
        'pengajuan_ke',
        'judul_laporan',
        'deskripsi_judul',
        'status',
        'catatan_pembimbing',
        'nomor_berita_acara',
        'diajukan_at',
        'reviewed_at',
        'reviewed_by',
    ];

    protected $casts = [
        'pengajuan_ke' => 'integer',
        'diajukan_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function laporan()
    {
        return $this->belongsTo(PrakerinBimbinganLaporan::class, 'prakerin_bimbingan_laporan_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getIsDisetujuiAttribute(): bool
    {
        return $this->status === 'disetujui';
    }

    public function getIsDitolakAttribute(): bool
    {
        return $this->status === 'ditolak';
    }

    public function getIsDiajukanAttribute(): bool
    {
        return $this->status === 'diajukan';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'disetujui' => 'Disetujui (ACC)',
            'ditolak' => 'Ditolak (Perlu Revisi)',
            'diajukan' => 'Menunggu Review',
            default => ucfirst($this->status),
        };
    }
}
