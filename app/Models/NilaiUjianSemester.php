<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiUjianSemester extends Model
{
    protected $fillable = [
        'ujian_semester_id',
        'ujian_semester_mapel_id',
        'mata_pelajaran_id',
        'nama_mapel',
        'master_siswa_id',
        'rombel_id',
        'nomor_urut',
        'kode_peserta',
        'nama_lengkap',
        'kelas',
        'nilai',
        'jumlah_benar',
        'jumlah_soal',
        'nilai_akhir',
        'baris_excel',
        'nama_file',
        'imported_by',
        'imported_at',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'nilai_akhir' => 'decimal:2',
        'imported_at' => 'datetime',
    ];

    public function ujianSemester()
    {
        return $this->belongsTo(UjianSemester::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function ujianMapel()
    {
        return $this->belongsTo(UjianSemesterMapel::class, 'ujian_semester_mapel_id');
    }

    public function siswa()
    {
        return $this->belongsTo(MasterSiswa::class, 'master_siswa_id');
    }

    public function rombel()
    {
        return $this->belongsTo(Rombel::class);
    }
}
