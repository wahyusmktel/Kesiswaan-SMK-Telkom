<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentResponse extends Model
{
    protected $fillable = [
        'assessment_period_id',
        'assessment_instrument_id',
        'assessor_user_id',
        'assessable_type',
        'assessable_id',
        'score',
        'submitted_at',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'submitted_at' => 'datetime',
    ];

    public function period()
    {
        return $this->belongsTo(AssessmentPeriod::class, 'assessment_period_id');
    }

    public function instrument()
    {
        return $this->belongsTo(AssessmentInstrument::class, 'assessment_instrument_id');
    }

    public function assessor()
    {
        return $this->belongsTo(User::class, 'assessor_user_id');
    }

    public function assessable()
    {
        return $this->morphTo();
    }

    public function answers()
    {
        return $this->hasMany(AssessmentAnswer::class);
    }
}
