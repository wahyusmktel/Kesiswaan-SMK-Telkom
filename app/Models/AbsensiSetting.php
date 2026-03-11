<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiSetting extends Model
{
    use HasFactory;

    protected $table = 'absensi_settings';

    protected $fillable = [
        'jam_masuk_batas',
        'jam_keluar_batas',
        'latitude_sekolah',
        'longitude_sekolah',
        'radius_meter',
    ];

    /**
     * Get the singleton setting record (always ID=1)
     */
    public static function getSetting(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'jam_masuk_batas'   => '07:30:00',
                'jam_keluar_batas'  => '16:00:00',
                'latitude_sekolah'  => -5.3971449,
                'longitude_sekolah' => 105.2663993,
                'radius_meter'      => 200,
            ]
        );
    }
}
