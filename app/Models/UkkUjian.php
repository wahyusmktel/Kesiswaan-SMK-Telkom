<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UkkUjian extends Model
{
    protected $fillable = [
        'nama_ujian',
        'tahun_pelajaran_id',
        'jurusan',
        'nama_project',
        'tanggal_pelaksanaan',
    ];

    protected $casts = [
        'tanggal_pelaksanaan' => 'date',
    ];

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahun_pelajaran_id');
    }

    public function rombels()
    {
        return $this->belongsToMany(Rombel::class, 'ukk_rombel_mappings');
    }

    public function instrumens()
    {
        return $this->hasMany(UkkInstrumen::class, 'ukk_ujian_id');
    }

    public function penguji()
    {
        return $this->belongsToMany(User::class, 'ukk_penguji');
    }
}
