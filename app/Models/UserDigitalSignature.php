<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UserDigitalSignature extends Model
{
    protected $fillable = [
        'user_id',
        'ttd_image_path',
        'pin_hash',
        'is_active',
        'auto_sign_izin_keluar',
        'auto_sign_perizinan',
        'auto_sign_izin_guru',
        'auto_sign_penilaian_ukk',
        'auto_sign_uks',
        'auto_sign_lesson_plan',
    ];

    protected $hidden = ['pin_hash'];

    protected $casts = [
        'is_active'                => 'boolean',
        'auto_sign_izin_keluar'    => 'boolean',
        'auto_sign_perizinan'      => 'boolean',
        'auto_sign_izin_guru'      => 'boolean',
        'auto_sign_penilaian_ukk'  => 'boolean',
        'auto_sign_uks'            => 'boolean',
        'auto_sign_lesson_plan'    => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifyPin(string $pin): bool
    {
        return $this->pin_hash && Hash::check($pin, $this->pin_hash);
    }

    public function isReady(): bool
    {
        return $this->is_active && $this->pin_hash !== null;
    }
}
