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

    public function shares()
    {
        return $this->hasMany(SurveyShare::class);
    }

    public function collaborators()
    {
        return $this->belongsToMany(User::class, 'survey_shares')
            ->withPivot(['role', 'shared_by'])
            ->withTimestamps();
    }

    public function isOwner(?User $user): bool
    {
        return $user && (int) $this->created_by === (int) $user->id;
    }

    public function canManageShares(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->isOwner($user) || $user->hasRole(['Super Admin', 'Operator']);
    }

    public function canEdit(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($this->isOwner($user) || $user->hasRole(['Super Admin', 'Operator'])) {
            return true;
        }

        return $this->shares()
            ->where('user_id', $user->id)
            ->where('role', 'editor')
            ->exists();
    }

    public function canViewResults(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($this->isOwner($user) || $user->hasRole(['Super Admin', 'Operator'])) {
            return true;
        }

        return $this->shares()
            ->where('user_id', $user->id)
            ->whereIn('role', ['editor', 'viewer'])
            ->exists();
    }

    public function getCollaboratorRole(?User $user): ?string
    {
        if (!$user) {
            return null;
        }

        if ($this->isOwner($user)) {
            return 'owner';
        }

        $share = $this->relationLoaded('shares')
            ? $this->shares->firstWhere('user_id', $user->id)
            : $this->shares()->where('user_id', $user->id)->first();

        return $share?->role;
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
