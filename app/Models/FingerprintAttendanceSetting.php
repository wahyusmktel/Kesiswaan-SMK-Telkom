<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FingerprintAttendanceSetting extends Model
{
    protected $fillable = [
        'checkin_start',
        'checkin_end',
        'checkout_start',
        'checkout_end',
    ];

    public static function getSetting(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'checkin_start' => '06:00:00',
                'checkin_end' => '07:30:00',
                'checkout_start' => '15:30:00',
                'checkout_end' => '18:00:00',
            ]
        );
    }
}
