<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PilketosPeriode extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'tgl_mulai_voting' => 'datetime',
        'tgl_selesai_voting' => 'datetime',
        'is_active' => 'boolean',
    ];

    public static function active(): ?self
    {
        return static::where('is_active', true)->latest()->first();
    }

    public function timelines(): HasMany
    {
        return $this->hasMany(PilketosTimeline::class)->orderBy('urutan')->orderBy('tanggal_mulai');
    }

    public function kandidats(): HasMany
    {
        return $this->hasMany(PilketosKandidat::class)->orderBy('nomor_urut');
    }

    public function dpts(): HasMany
    {
        return $this->hasMany(PilketosDpt::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PilketosVote::class);
    }

    public function getTotalDptAttribute(): int
    {
        return $this->dpts()->count();
    }

    public function getTotalVotesAttribute(): int
    {
        return $this->votes()->count();
    }

    public function getTurnoutPercentageAttribute(): float
    {
        $total = $this->total_dpt;
        if ($total === 0) {
            return 0.0;
        }

        return round(($this->total_votes / $total) * 100, 1);
    }
}
