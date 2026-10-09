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

    public function getFormattedReviewNotesAttribute(): ?string
    {
        if (empty($this->review_notes)) {
            return null;
        }

        $raw = (string) $this->review_notes;
        $hasHtml = $raw !== strip_tags($raw);

        if ($hasHtml) {
            $allowed = '<p><br><b><strong><i><em><u><s><ul><ol><li><blockquote><h1><h2><h3><h4><h5><h6><a><span><div>';
            $cleaned = strip_tags($raw, $allowed);
            $cleaned = preg_replace('/\s+on\w+="[^"]*"/i', '', $cleaned);
            $cleaned = preg_replace('/\s+on\w+=\'[^\']*\'/i', '', $cleaned);
            $cleaned = preg_replace('/href="javascript:[^"]*"/i', 'href="#"', $cleaned);
            $cleaned = preg_replace('/href=\'javascript:[^\']*\'/i', 'href="#"', $cleaned);

            return $cleaned;
        }

        return nl2br(e($raw));
    }
}
