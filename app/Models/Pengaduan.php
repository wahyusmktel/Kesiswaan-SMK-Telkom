<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    protected $fillable = [
        'nama_pelapor',
        'hubungan',
        'nomor_wa',
        'nama_siswa',
        'kelas_siswa',
        'kategori',
        'isi_pengaduan',
        'status',
        'catatan_petugas'
    ];
}
