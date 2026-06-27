<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrakerinRombel extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_rombel',
        'prakerin_industri_id',
        'pembimbing_internal_id',
        'pembimbing_external_id',
        'gunakan_periode_kustom',
        'tanggal_mulai',
        'tanggal_selesai',
        'gunakan_waktu_absensi_kustom',
        'jam_check_in_mulai',
        'jam_check_in_selesai',
        'jam_check_out_mulai',
        'jam_check_out_selesai',
        'status',
    ];

    protected $casts = [
        'gunakan_periode_kustom' => 'boolean',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'gunakan_waktu_absensi_kustom' => 'boolean',
    ];

    public function industri()
    {
        return $this->belongsTo(PrakerinIndustri::class, 'prakerin_industri_id');
    }

    public function pembimbingInternal()
    {
        return $this->belongsTo(PrakerinPembimbing::class, 'pembimbing_internal_id');
    }

    public function pembimbingExternal()
    {
        return $this->belongsTo(PrakerinPembimbing::class, 'pembimbing_external_id');
    }

    public function penempatans()
    {
        return $this->hasMany(PrakerinPenempatan::class, 'prakerin_rombel_id');
    }
}
