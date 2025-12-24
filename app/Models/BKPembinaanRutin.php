<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BKPembinaanRutin extends Model
{
    use HasFactory;

    protected $table = 'bk_pembinaan_rutins';

    protected $fillable = [
        'master_siswa_id',
        'guru_bk_id',
        'semester',
        'tahun_pelajaran_id',
        'tanggal',
        'kondisi_siswa',
        'catatan_pembinaan',
    ];

    public function siswa()
    {
        return $this->belongsTo(MasterSiswa::class, 'master_siswa_id');
    }

    public function guruBK()
    {
        return $this->belongsTo(User::class, 'guru_bk_id');
    }

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahun_pelajaran_id');
    }
}
