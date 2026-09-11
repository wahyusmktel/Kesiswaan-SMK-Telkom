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
    ];

    protected $casts = [
        'completion_percent' => 'decimal:2',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(OkrWeeklyReport::class, 'okr_weekly_report_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(OkrPlan::class, 'okr_plan_id');
    }
}
