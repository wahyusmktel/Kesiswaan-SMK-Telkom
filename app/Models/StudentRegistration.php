<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StudentRegistration extends Model
{
    protected $fillable = [
        'source',
        'status',
        'nama_lengkap',
        'nisn',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'nomor_hp',
        'email',
        'sekolah_asal',
        'nama_orang_tua',
        'nomor_hp_orang_tua',
        'master_siswa_id',
        'dapodik_siswa_id',
        'reviewed_by',
        'reviewed_at',
        'mapped_by',
        'mapped_at',
        'notes',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'reviewed_at' => 'datetime',
        'mapped_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (StudentRegistration $registration) {
            $registration->public_token ??= (string) Str::uuid();
            $registration->registration_number ??= 'REG-' . now()->format('ymd') . '-' . Str::upper(Str::random(6));
        });
    }

    public function masterSiswa()
    {
        return $this->belongsTo(MasterSiswa::class);
    }

    public function dapodikSiswa()
    {
        return $this->belongsTo(DapodikSiswa::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function mapper()
    {
        return $this->belongsTo(User::class, 'mapped_by');
    }
}
