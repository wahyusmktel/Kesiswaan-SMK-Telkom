<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Keterlambatan;
use App\Models\Kelas;
use App\Models\MasterSiswa;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalisaKeterlambatanController extends Controller
{
    public function index()
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first() ?? TahunPelajaran::latest()->first();
        $tahunAktifId = $tahunAktif ? $tahunAktif->id : null;

        // 1. Summary Stats
        $summary = [
            'today' => Keterlambatan::whereDate('waktu_dicatat_security', today())->count(),
            'week' => Keterlambatan::whereBetween('waktu_dicatat_security', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'month' => Keterlambatan::whereMonth('waktu_dicatat_security', now()->month)->whereYear('waktu_dicatat_security', now()->year)->count(),
            'total' => Keterlambatan::count(),
        ];

        // 2. Trend Data (Last 30 Days)
        $trendRaw = Keterlambatan::where('waktu_dicatat_security', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(waktu_dicatat_security) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->pluck('total', 'date');

        $trendLabels = [];
        $trendValues = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $trendLabels[] = now()->subDays($i)->format('d M');
            $trendValues[] = $trendRaw->get($date, 0);
        }

        // 3. Class Data
        $classDistribution = Rombel::where('tahun_pelajaran_id', $tahunAktifId)
            ->join('kelas', 'rombels.kelas_id', '=', 'kelas.id')
            ->select('kelas.nama_kelas', DB::raw('(SELECT COUNT(*) FROM keterlambatans JOIN master_siswa ON keterlambatans.master_siswa_id = master_siswa.id JOIN rombel_siswa ON master_siswa.id = rombel_siswa.master_siswa_id WHERE rombel_siswa.rombel_id = rombels.id) as total_terlambat'))
            ->orderBy('total_terlambat', 'desc')
            ->get();

        // 4. Top Repeat Offenders
        $topStudents = MasterSiswa::with(['user', 'rombels.kelas'])
            ->withCount('keterlambatans')
            ->orderBy('keterlambatans_count', 'desc')
            ->take(10)
            ->get();

        // 5. Peak Time Data (Hourly)
        $peakTimeRaw = Keterlambatan::select(DB::raw('HOUR(waktu_dicatat_security) as hour'), DB::raw('count(*) as total'))
            ->groupBy('hour')
            ->orderBy('hour', 'asc')
            ->get()
            ->pluck('total', 'hour');

        $peakTimeLabels = [];
        $peakTimeValues = [];
        // Typically school hours 6 AM to 6 PM
        for ($h = 6; $h <= 18; $h++) {
            $peakTimeLabels[] = sprintf('%02d:00', $h);
            $peakTimeValues[] = $peakTimeRaw->get($h, 0);
        }

        // 6. Reasons Analysis
        $reasons = Keterlambatan::select('alasan_siswa', DB::raw('count(*) as total'))
            ->whereNotNull('alasan_siswa')
            ->groupBy('alasan_siswa')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        return view('pages.kesiswaan.analisa-keterlambatan.index', [
            'summary' => $summary,
            'trendChart' => ['labels' => $trendLabels, 'data' => $trendValues],
            'classChart' => ['labels' => $classDistribution->pluck('nama_kelas'), 'data' => $classDistribution->pluck('total_terlambat')],
            'peakTimeChart' => ['labels' => $peakTimeLabels, 'data' => $peakTimeValues],
            'reasonsChart' => ['labels' => $reasons->pluck('alasan_siswa'), 'data' => $reasons->pluck('total')],
            'topStudents' => $topStudents,
            'tahunAktif' => $tahunAktif
        ]);
    }
}
