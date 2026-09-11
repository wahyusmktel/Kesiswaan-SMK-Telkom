<?php

namespace App\Services;

use App\Models\GuruPiketSchedule;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;

class GuruPiketDutyService
{
    private const WEEKDAY_MAP = [
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
    ];

    public function users(?CarbonInterface $date = null): Collection
    {
        $weekday = self::WEEKDAY_MAP[($date ?? now())->format('l')] ?? null;
        if (! $weekday) {
            return new Collection;
        }

        return User::query()
            ->role('Guru Piket')
            ->whereHas('guruPiketSchedules', fn ($query) => $query->where('weekday', $weekday))
            ->get();
    }

    public function isOnDuty(User $user, ?CarbonInterface $date = null): bool
    {
        if (! $user->hasRole('Guru Piket')) {
            return false;
        }

        $weekday = self::WEEKDAY_MAP[($date ?? now())->format('l')] ?? null;

        return $weekday !== null && GuruPiketSchedule::query()
            ->where('weekday', $weekday)
            ->where('user_id', $user->id)
            ->exists();
    }
}
