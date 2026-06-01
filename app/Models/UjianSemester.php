<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UjianSemester extends Model
{
    protected $fillable = [
        'tahun_pelajaran_id',
        'semester',
        'nama_ujian',
        'kode_ujian',
        'tanggal_ujian',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_ujian' => 'date',
    ];

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahun_pelajaran_id');
    }

    public function nilai()
    {
        return $this->hasMany(NilaiUjianSemester::class);
    }

    public function ujianMapels()
    {
        return $this->hasMany(UjianSemesterMapel::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsToMany(MataPelajaran::class, 'ujian_semester_mapels')
            ->withPivot('jumlah_soal')
            ->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
