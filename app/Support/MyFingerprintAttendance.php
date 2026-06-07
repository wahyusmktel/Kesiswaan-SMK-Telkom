<?php

namespace App\Support;

use App\Models\FingerprintAttendance;
use App\Models\FingerprintUser;
use App\Models\User;
use Carbon\Carbon;

class MyFingerprintAttendance
{
    public static function today(User $user): array
    {
        $logs = FingerprintAttendance::with('device')
            ->where('app_user_id', $user->id)
            ->whereDate('timestamp', today())
            ->orderBy('timestamp')
            ->get();

        return [
            'is_mapped' => FingerprintUser::where('app_user_id', $user->id)->exists(),
            'first_scan' => $logs->first()?->timestamp,
            'last_scan' => $logs->count() > 1 ? $logs->last()?->timestamp : null,
            'total_scan' => $logs->count(),
            'device' => $logs->last()?->device,
        ];
    }

    public static function dailyRecaps(User $user, ?Carbon $dateFrom = null, ?Carbon $dateTo = null)
    {
        return FingerprintAttendance::query()
            ->selectRaw('DATE(timestamp) as tanggal, MIN(timestamp) as scan_masuk, MAX(timestamp) as scan_keluar, COUNT(*) as total_scan')
            ->where('app_user_id', $user->id)
            ->when($dateFrom, fn ($query) => $query->whereDate('timestamp', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('timestamp', '<=', $dateTo))
            ->groupByRaw('DATE(timestamp)')
            ->orderByDesc('tanggal')
            ->get();
    }
}
