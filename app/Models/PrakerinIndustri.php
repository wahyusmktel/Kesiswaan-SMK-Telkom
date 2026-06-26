<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrakerinIndustri extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_industri',
        'alamat',
        'kota',
        'provinsi_code',
        'provinsi_name',
        'kabupaten_code',
        'kabupaten_name',
        'kecamatan_code',
        'kecamatan_name',
        'desa_code',
        'desa_name',
        'telepon',
        'email_pic',
        'nama_pic',
        'nomor_mou',
        'tanggal_mou',
        'tanggal_akhir_mou',
        'is_mou_active',
        'latitude',
        'longitude',
        'catatan_mou',
    ];

    protected $casts = [
        'tanggal_mou' => 'date',
        'tanggal_akhir_mou' => 'date',
        'is_mou_active' => 'boolean',
    ];

    public function pembimbings()
    {
        return $this->hasMany(PrakerinPembimbing::class);
    }

    public function rombels()
    {
        return $this->hasMany(PrakerinRombel::class);
    }
}
