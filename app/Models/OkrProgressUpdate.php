<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OkrProgressUpdate extends Model
{
    protected $fillable = [
        'okr_plan_id', 'user_id', 'progress_before', 'progress_after', 'current_value',
        'status', 'note', 'evidence_path', 'recorded_at',
    ];

    protected $casts = [
        'progress_before' => 'decimal:2',
        'progress_after' => 'decimal:2',
        'current_value' => 'decimal:2',
        'recorded_at' => 'date',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(OkrPlan::class, 'okr_plan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
