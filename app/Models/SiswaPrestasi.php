<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiswaPrestasi extends Model
{
    protected $fillable = ['master_siswa_id', 'nama_prestasi', 'tanggal', 'poin_bonus', 'keterangan'];

    public function siswa()
    {
        return $this->belongsTo(MasterSiswa::class, 'master_siswa_id');
    }
}
