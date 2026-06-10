<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrakerinPenempatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'master_siswa_id',
        'prakerin_rombel_id',
        'prakerin_industri_id',
        'master_guru_id',
        'nama_pembimbing_industri',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    public function siswa()
    {
        return $this->belongsTo(MasterSiswa::class, 'master_siswa_id');
    }
    public function industri()
    {
        return $this->belongsTo(PrakerinIndustri::class, 'prakerin_industri_id');
    }
    public function guruPembimbing()
    {
        return $this->belongsTo(MasterGuru::class, 'master_guru_id');
    }

    public function rombelPkl()
    {
        return $this->belongsTo(PrakerinRombel::class, 'prakerin_rombel_id');
    }

    public function jurnals()
    {
        return $this->hasMany(PrakerinJurnal::class);
    }

    public function absensis()
    {
        return $this->hasMany(PrakerinAbsensi::class);
    }
}
