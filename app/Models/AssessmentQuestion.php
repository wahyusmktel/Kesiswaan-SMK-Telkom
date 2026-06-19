<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentQuestion extends Model
{
    public const ANSWER_TYPES = [
        'yes_no' => 'Ya / Tidak',
        'single_choice' => 'Pilihan Tunggal',
        'multiple_choice' => 'Pilihan Multi',
        'text' => 'Teks / Saran',
    ];

    protected $fillable = [
        'assessment_instrument_id',
        'question_text',
        'answer_type',
        'options',
        'max_score',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function instrument()
    {
        return $this->belongsTo(AssessmentInstrument::class, 'assessment_instrument_id');
    }

    public function isScored(): bool
    {
        return $this->answer_type !== 'text';
    }
}
