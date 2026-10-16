<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OkrUnit extends Model
{
    protected $fillable = ['code', 'name', 'role_names', 'sort_order', 'is_active', 'parent_id'];

    protected $casts = [
        'role_names' => 'array',
        'is_active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function plans(): HasMany
    {
        return $this->hasMany(OkrPlan::class);
    }

    public function weeklyReports(): HasMany
    {
        return $this->hasMany(OkrWeeklyReport::class);
    }
}
