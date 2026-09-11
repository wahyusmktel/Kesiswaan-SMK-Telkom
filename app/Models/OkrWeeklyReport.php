<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OkrWeeklyReport extends Model
{
    protected $fillable = [
        'okr_period_id', 'okr_unit_id', 'week_start', 'week_end', 'weekly_focus', 'support_needed',
        'status', 'created_by', 'submitted_by', 'submitted_at', 'reviewed_by', 'reviewed_at', 'review_notes',
    ];

    protected $casts = [
        'week_start' => 'date',
        'week_end' => 'date',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(OkrPeriod::class, 'okr_period_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(OkrUnit::class, 'okr_unit_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OkrWeeklyReportItem::class)->orderBy('priority_order');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
