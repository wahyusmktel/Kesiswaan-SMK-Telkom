<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rombel extends Model
{
    // Jangan lupa tambahkan $fillable
    protected $fillable = ['tahun_ajaran', 'kelas_id', 'wali_kelas_id'];

    // Relasi dari Rombel ke data kelasnya
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
    // Relasi dari Rombel ke user wali kelasnya
    public function waliKelas()
    {
        return $this->belongsTo(User::class, 'wali_kelas_id');
    }
    // Relasi dari Rombel ke banyak siswa (many-to-many)
    public function siswa()
    {
        return $this->belongsToMany(MasterSiswa::class, 'rombel_siswa');
    }
    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahun_pelajaran_id');
    }
}
