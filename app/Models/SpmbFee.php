<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpmbFee extends Model
{
    protected $fillable = [
        'name',
        'amount',
        'sort_order',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
