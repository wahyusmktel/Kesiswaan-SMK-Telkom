<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiswaKelulusan extends Model
{
    protected $fillable = [
        'pengumuman_kelulusan_id',
        'master_siswa_id',
        'status',
        'catatan',
    ];

    public function pengumumanKelulusan()
    {
        return $this->belongsTo(PengumumanKelulusan::class, 'pengumuman_kelulusan_id');
    }

    public function siswa()
    {
        return $this->belongsTo(MasterSiswa::class, 'master_siswa_id');
    }
}
