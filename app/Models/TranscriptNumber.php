<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranscriptNumber extends Model
{
    protected $fillable = ['master_siswa_id', 'number', 'locked_at'];

    protected $casts = [
        'locked_at' => 'datetime',
    ];

    public function siswa()
    {
        return $this->belongsTo(MasterSiswa::class, 'master_siswa_id');
    }
}
