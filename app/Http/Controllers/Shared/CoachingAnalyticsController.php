<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Keterlambatan;
use App\Models\KeterlambatanCoaching;
use App\Models\KeterlambatanBKCoaching;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CoachingAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Keterlambatan::with(['siswa.rombels.kelas', 'coaching', 'bkCoaching']);

        // Role Scoping
        if ($user->hasRole('Wali Kelas')) {
            $query->whereHas('siswa.rombels', function($q) use ($user) {
                $q->where('wali_kelas_id', $user->id);
            });
        } elseif ($user->hasRole('Guru BK')) {
            // BK usually handles 3+ lateness which has status 'pembinaan_bk' or is completed after BK
            $query->where(function($q) {
                $q->where('status', 'pembinaan_bk')
                  ->orWhereNotNull('waktu_pembinaan_bk');
            });
        }

        // Only show records that have had some coaching or reached that stage
        $query->where(function($q) {
            $q->whereNotNull('waktu_pendampingan_wali_kelas')
              ->orWhereNotNull('waktu_pembinaan_bk')
              ->orWhereIn('status', ['pendampingan_wali_kelas', 'pembinaan_bk', 'selesai']);
        });

        $activities = (clone $query)->latest()->paginate(15);

        // --- Analytics Data ---
        
        // 1. Coaching Trends (Last 6 Months)
        $trendsQuery = Keterlambatan::select(
            DB::raw('COUNT(*) as total'),
            DB::raw("DATE_FORMAT(waktu_dicatat_security, '%Y-%m') as month")
        )
        ->where('waktu_dicatat_security', '>=', now()->subMonths(6))
        ->whereNotNull('waktu_pendampingan_wali_kelas');

        if ($user->hasRole('Wali Kelas')) {
            $trendsQuery->whereHas('siswa.rombels', function($q) use ($user) {
                $q->where('wali_kelas_id', $user->id);
            });
        }

        $trends = $trendsQuery->groupBy('month')->orderBy('month')->get();

        // 2. Effectiveness Analysis
        $totalCoached = (clone $query)->whereIn('status', ['selesai'])->count();
        $successful = (clone $query)
            ->whereIn('status', ['selesai'])
            ->where('updated_at', '<', now()->subDays(14)) // Success is defined as no lateness for 14 days after completion
            ->whereDoesntHave('siswa.keterlambatans', function($q) {
                $q->where('waktu_dicatat_security', '>', DB::raw('keterlambatans.updated_at'));
            })->count();
            
        $effectivenessRate = $totalCoached > 0 ? round(($successful / $totalCoached) * 100) : 0;

        // 3. Morning Routine Stats (From BK Coaching)
        $bkSummary = KeterlambatanBKCoaching::query();
        if ($user->hasRole('Wali Kelas')) {
            $bkSummary->whereHas('keterlambatan.siswa.rombels', function($q) use ($user) {
                $q->where('wali_kelas_id', $user->id);
            });
        }

        $routines = [
            'avg_wake' => $bkSummary->clone()->whereNotNull('jam_bangun')->avg(DB::raw('TIME_TO_SEC(jam_bangun)')),
            'avg_depart' => $bkSummary->clone()->whereNotNull('jam_berangkat')->avg(DB::raw('TIME_TO_SEC(jam_berangkat)')),
            'avg_travel' => round($bkSummary->clone()->avg('durasi_perjalanan') ?? 0),
        ];

        // Format times back to H:i
        $routines['avg_wake'] = $routines['avg_wake'] ? date('H:i', $routines['avg_wake']) : '--:--';
        $routines['avg_depart'] = $routines['avg_depart'] ? date('H:i', $routines['avg_depart']) : '--:--';

        // 4. Common Causes (Realities)
        $recentCoachings = KeterlambatanCoaching::with('keterlambatan.siswa')
            ->latest()
            ->take(10);
            
        if ($user->hasRole('Wali Kelas')) {
            $recentCoachings->whereHas('keterlambatan.siswa.rombels', function($q) use ($user) {
                $q->where('wali_kelas_id', $user->id);
            });
        }
        
        $recentCoachings = $recentCoachings->get();

        return view('pages.shared.coaching-analytics.index', compact(
            'activities',
            'trends',
            'effectivenessRate',
            'recentCoachings',
            'totalCoached',
            'routines'
        ));
    }
}
