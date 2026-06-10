<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrakerinSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal_mulai',
        'tanggal_selesai',
        'jam_check_in_mulai',
        'jam_check_in_selesai',
        'jam_check_out_mulai',
        'jam_check_out_selesai',
        'instruksi_jurnal',
        'wajib_foto_absensi',
        'wajib_lokasi',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'wajib_foto_absensi' => 'boolean',
        'wajib_lokasi' => 'boolean',
    ];
}
