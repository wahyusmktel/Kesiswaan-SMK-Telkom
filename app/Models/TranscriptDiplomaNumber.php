<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranscriptDiplomaNumber extends Model
{
    protected $fillable = ['master_siswa_id', 'diploma_number'];

    public function siswa()
    {
        return $this->belongsTo(MasterSiswa::class, 'master_siswa_id');
    }
}
