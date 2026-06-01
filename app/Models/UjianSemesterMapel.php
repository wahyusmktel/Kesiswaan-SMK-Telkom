<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UjianSemesterMapel extends Model
{
    protected $fillable = [
        'ujian_semester_id',
        'mata_pelajaran_id',
        'nama_mapel',
        'jumlah_soal',
    ];

    public function ujianSemester()
    {
        return $this->belongsTo(UjianSemester::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }
}
