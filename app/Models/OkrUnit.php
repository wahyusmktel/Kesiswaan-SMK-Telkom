<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OkrUnit extends Model
{
    protected $fillable = ['code', 'name', 'role_names', 'sort_order', 'is_active'];

    protected $casts = [
        'role_names' => 'array',
        'is_active' => 'boolean',
    ];

    public function plans(): HasMany
    {
        return $this->hasMany(OkrPlan::class);
    }
}
