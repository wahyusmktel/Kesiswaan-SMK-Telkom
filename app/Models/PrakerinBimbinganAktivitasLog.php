<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrakerinBimbinganAktivitasLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'prakerin_bimbingan_aktivitas_logs';

    public $timestamps = false;

    protected $fillable = [
        'prakerin_bimbingan_laporan_id',
        'prakerin_bimbingan_tahap_id',
        'user_id',
        'aksi',
        'keterangan',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function laporan()
    {
        return $this->belongsTo(PrakerinBimbinganLaporan::class, 'prakerin_bimbingan_laporan_id');
    }

    public function tahap()
    {
        return $this->belongsTo(PrakerinBimbinganTahap::class, 'prakerin_bimbingan_tahap_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Catat log aktivitas bimbingan.
     */
    public static function catat(
        int $laporanId,
        int $userId,
        string $aksi,
        string $keterangan,
        ?int $tahapId = null
    ): self {
        return self::create([
            'prakerin_bimbingan_laporan_id' => $laporanId,
            'prakerin_bimbingan_tahap_id' => $tahapId,
            'user_id' => $userId,
            'aksi' => $aksi,
            'keterangan' => $keterangan,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}
