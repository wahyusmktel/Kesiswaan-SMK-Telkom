<?php

namespace App\Http\Controllers;

use App\Models\HappinessMetric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class HappinessMetricController extends Controller
{
    /**
     * Check if device already submitted today
     */
    public function checkStatus(Request $request)
    {
        $fingerprint = $request->input('fingerprint');
        $today = Carbon::today()->toDateString();

        $exists = HappinessMetric::where('device_fingerprint', $fingerprint)
            ->where('submitted_date', $today)
            ->exists();

        return response()->json([
            'already_submitted' => $exists,
            'date' => $today,
        ]);
    }

    /**
     * Store happiness metric
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fingerprint' => 'required|string|max:64',
            'mood_level' => 'required|in:sangat_bahagia,bahagia,netral,sedih,sangat_sedih',
            'mood_score' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors(),
            ], 422);
        }

        $fingerprint = $request->input('fingerprint');
        $today = Carbon::today()->toDateString();

        // Check if already submitted today
        $exists = HappinessMetric::where('device_fingerprint', $fingerprint)
            ->where('submitted_date', $today)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah mengisi survei hari ini. Silakan coba lagi besok! 🙏',
                'already_submitted' => true,
            ], 429);
        }

        // Create new metric
        $metric = HappinessMetric::create([
            'device_fingerprint' => $fingerprint,
            'ip_address' => $request->ip(),
            'mood_level' => $request->input('mood_level'),
            'mood_score' => $request->input('mood_score'),
            'user_agent' => $request->userAgent(),
            'submitted_date' => $today,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih telah berbagi perasaan Anda! 💖',
            'data' => [
                'mood' => $metric->mood_label,
                'emoji' => $metric->mood_emoji,
            ],
        ]);
    }

    /**
     * Get today's happiness statistics (public)
     */
    public function getStats()
    {
        $today = Carbon::today()->toDateString();

        $todayMetrics = HappinessMetric::where('submitted_date', $today)->get();
        $totalToday = $todayMetrics->count();

        if ($totalToday === 0) {
            return response()->json([
                'total_today' => 0,
                'average_score' => 0,
                'mood_distribution' => [],
                'dominant_mood' => null,
            ]);
        }

        $avgScore = round($todayMetrics->avg('mood_score'), 1);

        $distribution = $todayMetrics->groupBy('mood_level')->map(fn($items) => $items->count());

        $dominantMood = $distribution->sortDesc()->keys()->first();

        return response()->json([
            'total_today' => $totalToday,
            'average_score' => $avgScore,
            'mood_distribution' => $distribution,
            'dominant_mood' => $dominantMood,
        ]);
    }
}
