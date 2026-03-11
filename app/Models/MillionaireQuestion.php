<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MillionaireQuestion extends Model
{
    protected $fillable = [
        'set_id',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
        'level',
    ];

    /**
     * Get the set that owns the question.
     */
    public function set(): BelongsTo
    {
        return $this->belongsTo(MillionaireSet::class, 'set_id');
    }
}
