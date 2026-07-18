<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OkrPeriod extends Model
{
    protected $fillable = [
        'tahun_pelajaran_id', 'title', 'vision', 'starts_at', 'ends_at', 'status', 'created_by',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahun_pelajaran_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function objectives(): HasMany
    {
        return $this->hasMany(OkrObjective::class)->orderBy('sort_order');
    }
}
