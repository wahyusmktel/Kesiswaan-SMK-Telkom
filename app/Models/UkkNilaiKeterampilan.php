<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UkkNilaiKeterampilan extends Model
{
    protected $table = 'ukk_nilai_keterampilan';

    protected $fillable = ['master_siswa_id', 'indikator_id', 'penguji_id', 'nilai'];

    public function siswa()
    {
        return $this->belongsTo(MasterSiswa::class, 'master_siswa_id');
    }

    public function indikator()
    {
        return $this->belongsTo(UkkInstrumenIndikator::class, 'indikator_id');
    }

    public function penguji()
    {
        return $this->belongsTo(User::class, 'penguji_id');
    }
}
