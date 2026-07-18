<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OkrObjective extends Model
{
    protected $fillable = ['okr_period_id', 'code', 'title', 'sort_order'];

    public function period(): BelongsTo
    {
        return $this->belongsTo(OkrPeriod::class, 'okr_period_id');
    }

    public function keyResults(): HasMany
    {
        return $this->hasMany(OkrKeyResult::class)->orderBy('sort_order');
    }
}
