<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UkkNilaiPengetahuan extends Model
{
    protected $table = 'ukk_nilai_pengetahuan';

    protected $fillable = ['master_siswa_id', 'soal_id', 'penguji_id', 'nilai'];

    public function siswa()
    {
        return $this->belongsTo(MasterSiswa::class, 'master_siswa_id');
    }

    public function soal()
    {
        return $this->belongsTo(UkkInstrumenSoal::class, 'soal_id');
    }

    public function penguji()
    {
        return $this->belongsTo(User::class, 'penguji_id');
    }
}
