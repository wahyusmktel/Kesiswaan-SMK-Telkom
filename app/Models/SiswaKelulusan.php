<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SiswaKelulusan extends Model
{
    protected $fillable = [
        'pengumuman_kelulusan_id',
        'master_siswa_id',
        'status',
        'catatan',
        'verification_token',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->verification_token)) {
                $model->verification_token = Str::random(32);
            }
        });
    }

    public function pengumumanKelulusan()
    {
        return $this->belongsTo(PengumumanKelulusan::class, 'pengumuman_kelulusan_id');
    }

    public function siswa()
    {
        return $this->belongsTo(MasterSiswa::class, 'master_siswa_id');
    }
}
