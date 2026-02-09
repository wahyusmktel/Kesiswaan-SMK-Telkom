<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MillionaireSet extends Model
{
    protected $fillable = [
        'name',
        'description',
        'user_id',
        'is_active',
    ];

    /**
     * Get the user that created the set.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the questions for the set.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(MillionaireQuestion::class, 'set_id');
    }
}
