<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiswaPelanggaran extends Model
{
    protected $fillable = ['master_siswa_id', 'poin_peraturan_id', 'tanggal', 'catatan', 'pelapor_id'];

    public function siswa()
    {
        return $this->belongsTo(MasterSiswa::class, 'master_siswa_id');
    }

    public function peraturan()
    {
        return $this->belongsTo(PoinPeraturan::class, 'poin_peraturan_id');
    }

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }
}
