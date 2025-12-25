<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiswaPemutihan extends Model
{
    protected $fillable = ['master_siswa_id', 'tanggal', 'poin_dikurangi', 'keterangan', 'status', 'diajukan_oleh', 'disetujui_oleh'];

    public function siswa()
    {
        return $this->belongsTo(MasterSiswa::class, 'master_siswa_id');
    }

    public function pengaju()
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }

    public function penyetuju()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
}
