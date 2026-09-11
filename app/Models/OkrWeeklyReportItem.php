<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OkrWeeklyReportItem extends Model
{
    protected $fillable = [
        'okr_weekly_report_id', 'okr_plan_id', 'priority_order', 'commitment', 'measurable_target',
        'cross_unit_dependencies', 'approval_needs', 'actual_result', 'completion_percent',
        'final_status', 'blockers', 'next_follow_up', 'evidence_path',
        'okr_progress_update_id', 'progress_applied_by', 'progress_applied_at',
    ];

    protected $casts = [
        'completion_percent' => 'decimal:2',
        'progress_applied_at' => 'datetime',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(OkrWeeklyReport::class, 'okr_weekly_report_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(OkrPlan::class, 'okr_plan_id');
    }

    public function progressUpdate(): BelongsTo
    {
        return $this->belongsTo(OkrProgressUpdate::class, 'okr_progress_update_id');
    }

    public function progressAppliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'progress_applied_by');
    }
}
