<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeterlambatanBKCoaching extends Model
{
    protected $table = 'keterlambatan_bk_coachings';

    protected $fillable = [
        'keterlambatan_id',
        'tanggal_konseling',
        'evaluasi_sebelumnya',
        'faktor_penghambat',
        'analisis_dampak',
        'jam_bangun',
        'jam_berangkat',
        'durasi_perjalanan',
        'strategi_pendukung',
        'hp_limit_time',
        'sanksi_disepakati',
        'pencatat_id',
    ];

    protected $casts = [
        'strategi_pendukung' => 'array',
        'tanggal_konseling' => 'date',
    ];

    public function keterlambatan()
    {
        return $this->belongsTo(Keterlambatan::class);
    }

    public function pencatat()
    {
        return $this->belongsTo(User::class, 'pencatat_id');
    }
}
