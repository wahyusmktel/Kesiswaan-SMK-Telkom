<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BKKonsultasiJadwal extends Model
{
    use HasFactory;

    protected $table = 'bk_konsultasi_jadwals';

    protected $fillable = [
        'master_siswa_id',
        'guru_bk_id',
        'perihal',
        'tanggal_rencana',
        'jam_rencana',
        'tempat',
        'status',
        'catatan_bk',
    ];

    public function siswa()
    {
        return $this->belongsTo(MasterSiswa::class, 'master_siswa_id');
    }

    public function guruBK()
    {
        return $this->belongsTo(User::class, 'guru_bk_id');
    }
}
