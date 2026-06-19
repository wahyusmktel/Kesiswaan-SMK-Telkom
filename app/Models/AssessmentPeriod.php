<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentPeriod extends Model
{
    protected $fillable = [
        'tahun_pelajaran_id',
        'title',
        'semester',
        'start_at',
        'end_at',
        'is_active',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahun_pelajaran_id');
    }

    public function instruments()
    {
        return $this->hasMany(AssessmentInstrument::class);
    }

    public function responses()
    {
        return $this->hasMany(AssessmentResponse::class);
    }

    public function isOpen(): bool
    {
        return $this->is_active && now()->between($this->start_at, $this->end_at);
    }
}
