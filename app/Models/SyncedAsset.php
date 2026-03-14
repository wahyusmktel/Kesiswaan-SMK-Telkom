<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncedAsset extends Model
{
    protected $primaryKey = 'local_id';

    protected $fillable = [
        'asset_id', 'asset_code_ypt', 'name', 'category', 'condition',
        'current_status', 'institution', 'building', 'room', 'faculty',
        'department', 'person_in_charge', 'asset_function', 'funding_source',
        'sequence_number', 'status', 'purchase_cost', 'salvage_value',
        'useful_life', 'book_value', 'disposal_date', 'disposal_method',
        'disposal_reason', 'last_synced_at'
    ];

    protected $casts = [
        'purchase_cost'  => 'decimal:2',
        'salvage_value'  => 'decimal:2',
        'book_value'     => 'decimal:2',
        'disposal_date'  => 'date',
        'last_synced_at' => 'datetime',
    ];
}
