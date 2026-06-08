<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\FingerprintAttendance;
use App\Support\MyFingerprintAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MyFingerprintAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        [$dateFrom, $dateTo] = $this->resolveRange($request);

        $today = MyFingerprintAttendance::today($user);
        $dailyRecaps = MyFingerprintAttendance::dailyRecaps($user, $dateFrom, $dateTo);
        $chartData = MyFingerprintAttendance::chartData($dailyRecaps);
        $appreciation = MyFingerprintAttendance::appreciation($dailyRecaps);
        $logs = FingerprintAttendance::with('device')
            ->where('app_user_id', $user->id)
            ->when($dateFrom, fn ($query) => $query->whereDate('timestamp', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('timestamp', '<=', $dateTo))
            ->latest('timestamp')
            ->paginate(30)
            ->withQueryString();

        return view('pages.shared.fingerprint-saya.index', compact('today', 'dailyRecaps', 'chartData', 'appreciation', 'logs', 'dateFrom', 'dateTo'));
    }

    private function resolveRange(Request $request): array
    {
        $range = $request->input('range', '1_month');

        return match ($range) {
            '1_week' => [now()->subWeek()->startOfDay(), now()->endOfDay()],
            'all' => [null, null],
            default => [now()->subMonthNoOverflow()->startOfDay(), now()->endOfDay()],
        };
    }
}
