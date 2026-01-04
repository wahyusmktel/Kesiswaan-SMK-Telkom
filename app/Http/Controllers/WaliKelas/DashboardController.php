<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\MasterSiswa;
use App\Models\Perizinan;
use App\Models\Keterlambatan;
use App\Models\IzinMeninggalkanKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $waliKelas = Auth::user();

        // Ambil ID user dari siswa-siswa di bawah perwalian wali kelas ini (Tahun ajaran aktif)
        $userIds = MasterSiswa::whereHas('rombels', function ($query) use ($waliKelas) {
            $query->where('wali_kelas_id', $waliKelas->id)
                ->whereHas('tahunPelajaran', function($q) {
                    $q->where('is_active', true);
                });
        })->whereNotNull('user_id')->pluck('user_id');

        // 1. Data untuk Pie Chart Status Izin
        $statusData = Perizinan::whereIn('user_id', $userIds)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusChartData = [
            'labels' => $statusData->keys(),
            'data' => $statusData->values(),
        ];

        // 2. Data untuk Line Chart Tren Harian (15 hari terakhir)
        $dailyData = Perizinan::whereIn('user_id', $userIds)
            ->where('tanggal_izin', '>=', now()->subDays(15))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get([
                DB::raw('DATE(tanggal_izin) as date'),
                DB::raw('COUNT(*) as count')
            ])
            ->pluck('count', 'date');

        $dates = collect();
        for ($i = 14; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dates->put($date, $dailyData->get($date, 0));
        }

        $dailyChartData = [
            'labels' => $dates->keys()->map(fn($date) => \Carbon\Carbon::parse($date)->format('d M')),
            'data' => $dates->values(),
        ];

        // 3. Widget Aktivitas Terakhir
        $latestActivities = Perizinan::whereIn('user_id', $userIds)
            ->with(['user'])
            ->latest('updated_at')
            ->take(5)
            ->get();

        // Izin meninggalkan kelas terakhir (Hanya yang di kelas perwalian)
        $izinKeluarTerakhir = IzinMeninggalkanKelas::whereIn('user_id', function($query) use ($waliKelas) {
                $query->select('master_siswa.user_id')
                    ->from('master_siswa')
                    ->join('rombel_siswa', 'master_siswa.id', '=', 'rombel_siswa.master_siswa_id')
                    ->join('rombels', 'rombel_siswa.rombel_id', '=', 'rombels.id')
                    ->join('tahun_pelajaran', 'rombels.tahun_pelajaran_id', '=', 'tahun_pelajaran.id')
                    ->where('rombels.wali_kelas_id', $waliKelas->id)
                    ->where('tahun_pelajaran.is_active', true)
                    ->whereNotNull('master_siswa.user_id');
            })
            ->with(['siswa'])
            ->latest()
            ->take(10)
            ->get();

        // --- DATA BARU: Widget Keterlambatan Siswa ---
        $today = Carbon::today();
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // Siswa terlambat hari ini (Hanya yang di kelas perwalian)
        $terlambatHariIni = Keterlambatan::whereIn('master_siswa_id', function($query) use ($waliKelas) {
                $query->select('master_siswa_id')
                    ->from('rombel_siswa')
                    ->join('rombels', 'rombel_siswa.rombel_id', '=', 'rombels.id')
                    ->join('tahun_pelajaran', 'rombels.tahun_pelajaran_id', '=', 'tahun_pelajaran.id')
                    ->where('rombels.wali_kelas_id', $waliKelas->id)
                    ->where('tahun_pelajaran.is_active', true);
            })
            ->whereDate('waktu_dicatat_security', $today)
            ->with(['siswa.user'])
            ->latest()
            ->get();

        // Statistik 30 hari terakhir
        $stats30HariRaw = Keterlambatan::whereIn('master_siswa_id', function($query) use ($waliKelas) {
                $query->select('master_siswa_id')
                    ->from('rombel_siswa')
                    ->join('rombels', 'rombel_siswa.rombel_id', '=', 'rombels.id')
                    ->join('tahun_pelajaran', 'rombels.tahun_pelajaran_id', '=', 'tahun_pelajaran.id')
                    ->where('rombels.wali_kelas_id', $waliKelas->id)
                    ->where('tahun_pelajaran.is_active', true);
            })
            ->where('waktu_dicatat_security', '>=', $thirtyDaysAgo)
            ->select(DB::raw('DATE(waktu_dicatat_security) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get()
            ->pluck('total', 'date');

        $latenessLabels = [];
        $latenessValues = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $latenessLabels[] = Carbon::parse($date)->isoFormat('DD MMM');
            $latenessValues[] = $stats30HariRaw->get($date, 0);
        }

        $latenessChartData = [
            'labels' => $latenessLabels,
            'data' => $latenessValues,
        ];

        // Top 5 Siswa Sering Terlambat (Berdasarkan total record di class ini)
        $topLateStudents = Keterlambatan::whereIn('master_siswa_id', function($query) use ($waliKelas) {
                $query->select('master_siswa_id')
                    ->from('rombel_siswa')
                    ->join('rombels', 'rombel_siswa.rombel_id', '=', 'rombels.id')
                    ->join('tahun_pelajaran', 'rombels.tahun_pelajaran_id', '=', 'tahun_pelajaran.id')
                    ->where('rombels.wali_kelas_id', $waliKelas->id)
                    ->where('tahun_pelajaran.is_active', true);
            })
            ->select('master_siswa_id', DB::raw('count(*) as total'))
            ->groupBy('master_siswa_id')
            ->orderBy('total', 'DESC')
            ->with(['siswa.user'])
            ->take(5)
            ->get();

        return view('pages.wali-kelas.dashboard.index', compact(
            'statusChartData',
            'dailyChartData',
            'latestActivities',
            'izinKeluarTerakhir',
            'terlambatHariIni',
            'latenessChartData',
            'topLateStudents'
        ));
    }
}
