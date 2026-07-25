<?php

namespace App\Models\Erapor;

use App\Models\MataPelajaran;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class EraporSubjectMapping extends Model
{
    protected $fillable = [
        'mata_pelajaran_id',
        'erapor_ref_subject_id',
        'confidence',
        'notes',
        'mapped_by',
        'mapped_at',
    ];

    protected $casts = [
        'confidence' => 'decimal:2',
        'mapped_at' => 'datetime',
    ];

    public function localSubject()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    public function referenceSubject()
    {
        return $this->belongsTo(EraporRefSubject::class, 'erapor_ref_subject_id');
    }

    public function mapper()
    {
        return $this->belongsTo(User::class, 'mapped_by');
    }
}
