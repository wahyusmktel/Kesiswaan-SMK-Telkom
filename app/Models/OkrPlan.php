<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OkrPlan extends Model
{
    protected $fillable = [
        'okr_key_result_id', 'okr_unit_id', 'parent_id', 'owner_id', 'level', 'title',
        'description', 'starts_at', 'ends_at', 'target_value', 'current_value', 'metric_unit',
        'weight', 'progress_percent', 'status', 'success_indicator', 'latest_evaluation',
        'completed_at', 'created_by',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'target_value' => 'decimal:2',
        'current_value' => 'decimal:2',
        'weight' => 'decimal:2',
        'progress_percent' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    public function keyResult(): BelongsTo
    {
        return $this->belongsTo(OkrKeyResult::class, 'okr_key_result_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(OkrUnit::class, 'okr_unit_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('starts_at');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(OkrProgressUpdate::class)->latest('recorded_at')->latest('id');
    }
}
