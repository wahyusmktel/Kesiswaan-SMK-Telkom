<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranscriptGrade extends Model
{
    protected $fillable = ['master_siswa_id', 'transcript_subject_id', 'score'];

    protected $casts = [
        'score' => 'decimal:2',
    ];

    public function siswa()
    {
        return $this->belongsTo(MasterSiswa::class, 'master_siswa_id');
    }

    public function subject()
    {
        return $this->belongsTo(TranscriptSubject::class, 'transcript_subject_id');
    }
}
