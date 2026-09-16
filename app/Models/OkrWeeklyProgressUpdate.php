<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OkrWeeklyProgressUpdate extends Model
{
    protected $fillable = [
        'okr_weekly_report_item_id', 'progress_percent', 'status', 'note', 'blockers',
        'evidence_path', 'recorded_by', 'recorded_at',
    ];

    protected $casts = [
        'progress_percent' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(OkrWeeklyReportItem::class, 'okr_weekly_report_item_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
