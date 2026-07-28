<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranscriptReprintCorrectionHistory extends Model
{
    protected $fillable = [
        'transcript_reprint_correction_id',
        'old_data',
        'new_data',
        'reason',
        'changed_by',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    public function correction()
    {
        return $this->belongsTo(TranscriptReprintCorrection::class, 'transcript_reprint_correction_id');
    }

    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
