<?php

namespace App\Http\Controllers\Kurikulum;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\Rombel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitoringAbsensiPerKelasController extends Controller
{
    public function index(Request $request)
    {
        $rombelId = $request->rombel_id;
        $month = $request->month ?? now()->format('Y-m');
        
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();

        $rombels = Rombel::with('kelas')->get()->sortBy('kelas.nama_kelas');

        // Base Query
        $query = AbsensiGuru::with(['jadwalPelajaran.guru', 'jadwalPelajaran.mataPelajaran'])
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth]);

        if ($rombelId) {
            $query->whereHas('jadwalPelajaran', function($q) use ($rombelId) {
                $q->where('rombel_id', $rombelId);
            });
        }

        // 1. Data Tables: Detailed Logs
        $absensi = (clone $query)->latest('tanggal')->latest('waktu_absen')->paginate(20);

        // 2. Line Chart Data: Daily Trends
        $dailyStats = (clone $query)
            ->selectRaw('DATE(tanggal) as date, status, COUNT(*) as count')
            ->groupBy('date', 'status')
            ->get()
            ->groupBy('date');

        $labels = [];
        $dataHadir = [];
        $dataTerlambat = [];
        $dataTidakHadir = [];
        $dataIzin = [];

        $currentDate = $startOfMonth->copy();
        while ($currentDate <= $endOfMonth) {
            $dateString = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format('d/m'); // Label axis X

            $stats = $dailyStats->get($dateString, collect());

            $dataHadir[] = $stats->where('status', 'hadir')->sum('count');
            $dataTerlambat[] = $stats->where('status', 'terlambat')->sum('count');
            $dataTidakHadir[] = $stats->where('status', 'tidak_hadir')->sum('count');
            $dataIzin[] = $stats->where('status', 'izin')->sum('count');

            $currentDate->addDay();
        }

        // 3. Top Guru Stats
        $teacherStats = (clone $query)
            ->join('jadwal_pelajarans', 'absensi_guru.jadwal_pelajaran_id', '=', 'jadwal_pelajarans.id')
            ->join('master_gurus', 'jadwal_pelajarans.master_guru_id', '=', 'master_gurus.id')
            ->selectRaw('master_gurus.nama_lengkap, 
                SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir_count,
                SUM(CASE WHEN status = "terlambat" THEN 1 ELSE 0 END) as terlambat_count,
                SUM(CASE WHEN status = "tidak_hadir" THEN 1 ELSE 0 END) as tidak_hadir_count,
                SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as izin_count,
                COUNT(*) as total_count')
            ->groupBy('master_gurus.id', 'master_gurus.nama_lengkap')
            ->get();

        $mostActive = $teacherStats->sortByDesc('hadir_count')->first();
        $mostLate = $teacherStats->sortByDesc('terlambat_count')->first(); // Hanya ambil yg ada terlambatnya
        $mostAbsent = $teacherStats->sortByDesc('tidak_hadir_count')->first();
        $mostPermit = $teacherStats->sortByDesc('izin_count')->first();
        
        // Filter out if 0 count
        $mostLate = $mostLate && $mostLate->terlambat_count > 0 ? $mostLate : null;
        $mostAbsent = $mostAbsent && $mostAbsent->tidak_hadir_count > 0 ? $mostAbsent : null;
        $mostPermit = $mostPermit && $mostPermit->izin_count > 0 ? $mostPermit : null;


        return view('pages.kurikulum.monitoring-absensi-guru.per-kelas', compact(
            'rombels', 'rombelId', 'month', 'absensi',
            'labels', 'dataHadir', 'dataTerlambat', 'dataTidakHadir', 'dataIzin',
            'mostActive', 'mostLate', 'mostAbsent', 'mostPermit'
        ));
    }

    public function export(Request $request)
    {
        $rombelId = $request->rombel_id;
        $month = $request->month ?? now()->format('Y-m');
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();

        $fileName = 'Laporan_Absensi_Kelas_' . ($rombelId ? Rombel::find($rombelId)->nama_rombel : 'Semua') . '_' . $month . '.xlsx';

        return (new \App\Exports\AbsensiPerKelasExport($startOfMonth, $endOfMonth, $rombelId))->download($fileName);
    }
}
