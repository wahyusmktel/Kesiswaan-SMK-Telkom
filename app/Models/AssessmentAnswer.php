<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentAnswer extends Model
{
    protected $fillable = [
        'assessment_response_id',
        'assessment_question_id',
        'answer_value',
        'score',
    ];

    protected $casts = [
        'answer_value' => 'array',
        'score' => 'decimal:2',
    ];

    public function response()
    {
        return $this->belongsTo(AssessmentResponse::class, 'assessment_response_id');
    }

    public function question()
    {
        return $this->belongsTo(AssessmentQuestion::class, 'assessment_question_id');
    }
}
