<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiswaPemutihan extends Model
{
    protected $fillable = ['master_siswa_id', 'tanggal', 'poin_dikurangi', 'keterangan'];

    public function siswa()
    {
        return $this->belongsTo(MasterSiswa::class, 'master_siswa_id');
    }
}
