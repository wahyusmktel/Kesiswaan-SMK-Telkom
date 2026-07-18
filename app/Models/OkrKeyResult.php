<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OkrKeyResult extends Model
{
    protected $fillable = [
        'okr_objective_id', 'code', 'title', 'description', 'metric_type', 'baseline_value',
        'target_value', 'metric_unit', 'due_date', 'weight', 'sort_order',
    ];

    protected $casts = [
        'baseline_value' => 'decimal:2',
        'target_value' => 'decimal:2',
        'weight' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function objective(): BelongsTo
    {
        return $this->belongsTo(OkrObjective::class, 'okr_objective_id');
    }

    public function plans(): HasMany
    {
        return $this->hasMany(OkrPlan::class);
    }
}
