<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\MasterSiswa;
use App\Models\MasterGuru;
use App\Models\Kelas;
use App\Models\Rombel;
use App\Models\DatabaseActivity;
use Carbon\Carbon;

class SuperAdminDashboardController extends Controller
{
    public function index()
    {
        // Active Users (sessions in last 5 minutes)
        $activeUsers = DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->where('sessions.last_activity', '>', now()->subMinutes(5)->timestamp)
            ->select('users.id', 'users.name', 'users.email', 'users.avatar', 'sessions.last_activity', 'sessions.ip_address')
            ->distinct()
            ->get()
            ->map(function ($user) {
                $user->last_activity_formatted = Carbon::createFromTimestamp($user->last_activity)->diffForHumans();
                return $user;
            });

        // Recent Login Activity (last 20 sessions)
        $recentLogins = DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->whereNotNull('sessions.user_id')
            ->orderByDesc('sessions.last_activity')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.avatar',
                'sessions.ip_address',
                'sessions.user_agent',
                'sessions.last_activity'
            )
            ->limit(20)
            ->get()
            ->map(function ($login) {
                $login->last_activity_formatted = Carbon::createFromTimestamp($login->last_activity)->diffForHumans();
                $login->last_activity_date = Carbon::createFromTimestamp($login->last_activity)->format('d M Y, H:i');
                $login->browser = $this->parseBrowser($login->user_agent);
                $login->device = $this->parseDevice($login->user_agent);
                return $login;
            });

        // Users by Role Statistics
        $usersByRole = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->select('roles.name as role', DB::raw('COUNT(*) as total'))
            ->groupBy('roles.id', 'roles.name')
            ->orderByDesc('total')
            ->get();

        // System Statistics
        $stats = [
            'total_users' => User::count(),
            'total_siswa' => MasterSiswa::count(),
            'total_guru' => MasterGuru::count(),
            'total_kelas' => Kelas::count(),
            'total_rombel' => Rombel::count(),
            'active_sessions' => DB::table('sessions')->whereNotNull('user_id')->count(),
        ];

        // Recent Database Activities (with user relationship)
        $recentActivities = DatabaseActivity::with('user')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // User Activity Feed (recent changes across the system)
        $userActivityFeed = $this->getRecentUserActivity();

        // Login trend (last 7 days) - using session data
        $loginTrend = collect(range(6, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo);
            $startTimestamp = $date->copy()->startOfDay()->timestamp;
            $endTimestamp = $date->copy()->endOfDay()->timestamp;

            return [
                'date' => $date->format('D'),
                'count' => DB::table('sessions')
                    ->whereNotNull('user_id')
                    ->whereBetween('last_activity', [$startTimestamp, $endTimestamp])
                    ->count()
            ];
        });

        return view('pages.super-admin.dashboard', compact(
            'activeUsers',
            'recentLogins',
            'usersByRole',
            'stats',
            'recentActivities',
            'userActivityFeed',
            'loginTrend'
        ));
    }

    private function getRecentUserActivity()
    {
        $activities = collect();

        // Get recent keterlambatan entries
        $keterlambatan = DB::table('keterlambatans')
            ->join('master_siswa', 'keterlambatans.master_siswa_id', '=', 'master_siswa.id')
            ->orderByDesc('keterlambatans.created_at')
            ->limit(5)
            ->select(
                'master_siswa.nama_lengkap as user_name',
                'keterlambatans.created_at',
                DB::raw("'keterlambatan' as type"),
                DB::raw("'dicatat terlambat masuk sekolah' as description")
            )
            ->get();

        // Get recent perizinan entries
        $perizinan = DB::table('perizinan')
            ->join('users', 'perizinan.user_id', '=', 'users.id')
            ->orderByDesc('perizinan.created_at')
            ->limit(5)
            ->select(
                'users.name as user_name',
                'perizinan.created_at',
                DB::raw("'perizinan' as type"),
                DB::raw("'mengajukan izin' as description")
            )
            ->get();

        // Get recent database activities
        $dbActivities = DB::table('database_activities')
            ->join('users', 'database_activities.user_id', '=', 'users.id')
            ->orderByDesc('database_activities.created_at')
            ->limit(5)
            ->select(
                'users.name as user_name',
                'database_activities.created_at',
                'database_activities.type',
                DB::raw("CONCAT('melakukan ', database_activities.type, ' database') as description")
            )
            ->get();

        // Get recent izin meninggalkan kelas
        $izinKeluar = DB::table('izin_meninggalkan_kelas')
            ->join('users', 'izin_meninggalkan_kelas.user_id', '=', 'users.id')
            ->orderByDesc('izin_meninggalkan_kelas.created_at')
            ->limit(5)
            ->select(
                'users.name as user_name',
                'izin_meninggalkan_kelas.created_at',
                DB::raw("'izin_keluar' as type"),
                DB::raw("'mengajukan izin meninggalkan kelas' as description")
            )
            ->get();

        // Merge and sort by created_at
        return $activities
            ->merge($keterlambatan)
            ->merge($perizinan)
            ->merge($dbActivities)
            ->merge($izinKeluar)
            ->sortByDesc('created_at')
            ->take(10)
            ->map(function ($item) {
                $item->created_at_formatted = Carbon::parse($item->created_at)->diffForHumans();
                return $item;
            })
            ->values();
    }

    private function parseBrowser($userAgent)
    {
        if (!$userAgent)
            return 'Unknown';

        if (str_contains($userAgent, 'Firefox'))
            return 'Firefox';
        if (str_contains($userAgent, 'Edg'))
            return 'Edge';
        if (str_contains($userAgent, 'Chrome'))
            return 'Chrome';
        if (str_contains($userAgent, 'Safari'))
            return 'Safari';
        if (str_contains($userAgent, 'Opera'))
            return 'Opera';

        return 'Other';
    }

    private function parseDevice($userAgent)
    {
        if (!$userAgent)
            return 'Unknown';

        if (str_contains($userAgent, 'Mobile'))
            return 'Mobile';
        if (str_contains($userAgent, 'Tablet'))
            return 'Tablet';

        return 'Desktop';
    }
}
