<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $fillable = ['title', 'description', 'created_by', 'is_active', 'start_at', 'end_at'];

    protected $casts = [
        'is_active' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    /**
     * Get the survey status based on time.
     */
    public function getScheduleStatusAttribute(): string
    {
        $now = now();

        if ($this->start_at && $now->lt($this->start_at)) {
            return 'upcoming';
        }

        if ($this->end_at && $now->gt($this->end_at)) {
            return 'expired';
        }

        return 'ongoing';
    }

    /**
     * Check if the survey is within its scheduled window and active.
     */
    public function isOpen(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        $startOk = $this->start_at ? $now->gte($this->start_at) : true;
        $endOk = $this->end_at ? $now->lte($this->end_at) : true;

        return $startOk && $endOk;
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function questions()
    {
        return $this->hasMany(SurveyQuestion::class)->orderBy('order');
    }

    public function responses()
    {
        return $this->hasMany(SurveyResponse::class);
    }

    public function targets()
    {
        return $this->belongsToMany(User::class, 'survey_targets');
    }

    /**
     * Scope untuk survei yang aktif dan dalam rentang waktu yang dibuka.
     */
    public function scopeOpen($query)
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_at')->orWhere('start_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_at')->orWhere('end_at', '>=', $now);
            });
    }

    /**
     * Dapatkan daftar survei aktif yang ditargetkan kepada user dan belum diisi.
     *
     * @param \App\Models\User|null $user
     * @return \Illuminate\Database\Eloquent\Collection<int, static>
     */
    public static function getPendingSurveysForUser(?User $user)
    {
        if (! $user) {
            return collect();
        }

        return static::query()
            ->open()
            ->whereHas('targets', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->whereDoesntHave('responses', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->latest()
            ->get();
    }
}
