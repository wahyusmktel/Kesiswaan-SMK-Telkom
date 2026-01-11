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
        } 
        // Waka and Guru BK can see everything in this analytics view now
        // to allow comparative analysis as requested.

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
        ->where(function($q) {
            $q->whereNotNull('waktu_pendampingan_wali_kelas')
              ->orWhereNotNull('waktu_pembinaan_bk');
        });

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

        // 4. Wali Kelas GROW Action Plans
        $growSummary = KeterlambatanCoaching::with('keterlambatan.siswa')
            ->latest()
            ->take(6);
            
        if ($user->hasRole('Wali Kelas')) {
            $growSummary->whereHas('keterlambatan.siswa.rombels', function($q) use ($user) {
                $q->where('wali_kelas_id', $user->id);
            });
        }
        
        $recentGrow = $growSummary->get();

        // 5. Comparison Stats
        $stats = [
            'total_bk' => (clone $query)->whereNotNull('waktu_pembinaan_bk')->count(),
            'total_wali' => (clone $query)->whereNotNull('waktu_pendampingan_wali_kelas')->count(),
        ];

        return view('pages.shared.coaching-analytics.index', compact(
            'activities',
            'trends',
            'effectivenessRate',
            'recentGrow',
            'totalCoached',
            'routines',
            'stats'
        ));
    }
}
