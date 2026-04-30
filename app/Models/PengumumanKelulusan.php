<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PengumumanKelulusan extends Model
{
    protected $fillable = [
        'judul',
        'keterangan',
        'waktu_publikasi',
        'skl_aktif',
        'tahun_pelajaran_id',
        'created_by',
    ];

    protected $casts = [
        'waktu_publikasi' => 'datetime',
        'skl_aktif'       => 'boolean',
    ];

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahun_pelajaran_id');
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function siswaKelulusans()
    {
        return $this->hasMany(SiswaKelulusan::class, 'pengumuman_kelulusan_id');
    }

    public function sudahDipublikasikan(): bool
    {
        return Carbon::now()->gte($this->waktu_publikasi);
    }

    public function getSekondsMenujuPublikasiAttribute(): int
    {
        $diff = Carbon::now()->diffInSeconds($this->waktu_publikasi, false);
        return max(0, $diff);
    }
}
