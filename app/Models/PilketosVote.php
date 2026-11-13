<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PilketosVote extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'voted_at' => 'datetime',
    ];

    public function periode(): BelongsTo
    {
        return $this->belongsTo(PilketosPeriode::class, 'pilketos_periode_id');
    }

    public function kandidat(): BelongsTo
    {
        return $this->belongsTo(PilketosKandidat::class, 'pilketos_kandidat_id');
    }
}
