<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PilketosTimeline extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'urutan' => 'integer',
    ];

    public function periode(): BelongsTo
    {
        return $this->belongsTo(PilketosPeriode::class, 'pilketos_periode_id');
    }
}
