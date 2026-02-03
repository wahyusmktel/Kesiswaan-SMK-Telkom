<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyAnswer extends Model
{
    protected $fillable = ['response_id', 'question_id', 'answer_value'];

    public function response()
    {
        return $this->belongsTo(SurveyResponse::class);
    }

    public function question()
    {
        return $this->belongsTo(SurveyQuestion::class);
    }
}
