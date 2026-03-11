<?php

namespace App\Http\Controllers\SDM;

use App\Http\Controllers\Controller;
use App\Models\AbsensiPegawai;
use App\Models\AbsensiSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AbsensiMonitoringController extends Controller
{
    public function harian(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $tanggal = Carbon::parse($tanggal);
        $setting = AbsensiSetting::getSetting();

        // All users with their attendance for the selected date
        $absensiList = AbsensiPegawai::with('user')
            ->whereDate('tanggal', $tanggal)
            ->get();

        // Summary stats
        $totalHadir    = $absensiList->whereIn('status', ['tepat_waktu', 'terlambat'])->whereNotNull('waktu_checkin')->count();
        $totalTepat    = $absensiList->where('status', 'tepat_waktu')->count();
        $totalTerlambat = $absensiList->where('status', 'terlambat')->count();

        // Get total active users (non-siswa) - safer approach without requiring specific roles to exist
        $totalPegawai = User::whereHas('roles', function ($q) {
            $q->where('name', '!=', 'Siswa');
        })->orWhereDoesntHave('roles')->count();
        $totalBelumAbsen = max(0, $totalPegawai - $totalHadir);

        // Map points for OpenStreetMap
        $mapPoints = $absensiList->filter(fn($a) => $a->lat_checkin && $a->lng_checkin)->map(function ($a) {
            return [
                'lat'    => $a->lat_checkin,
                'lng'    => $a->lng_checkin,
                'name'   => $a->user->name ?? '-',
                'status' => $a->status,
                'waktu'  => $a->waktu_checkin ? $a->waktu_checkin->format('H:i') : '-',
            ];
        })->values();

        return view('pages.sdm.absensi.monitoring-harian', compact(
            'absensiList',
            'tanggal',
            'setting',
            'totalHadir',
            'totalTepat',
            'totalTerlambat',
            'totalBelumAbsen',
            'totalPegawai',
            'mapPoints'
        ));
    }

    public function bulanan(Request $request)
    {
        $bulan  = $request->input('bulan', Carbon::now()->month);
        $tahun  = $request->input('tahun', Carbon::now()->year);
        $setting = AbsensiSetting::getSetting();

        $daysInMonth = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;

        // Aggregate per day
        $perHari = AbsensiPegawai::selectRaw('
                DATE(tanggal) as hari,
                COUNT(*) as total,
                SUM(CASE WHEN status = "tepat_waktu" THEN 1 ELSE 0 END) as tepat_waktu,
                SUM(CASE WHEN status = "terlambat" THEN 1 ELSE 0 END) as terlambat,
                SUM(CASE WHEN waktu_checkin IS NOT NULL THEN 1 ELSE 0 END) as hadir
            ')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->groupBy('hari')
            ->orderBy('hari')
            ->get()
            ->keyBy('hari');

        // Build chart data
        $chartLabels = [];
        $chartHadir  = [];
        $chartTerlambat = [];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dateKey = Carbon::createFromDate($tahun, $bulan, $d)->format('Y-m-d');
            $chartLabels[] = $d;
            $dayData = $perHari->get($dateKey);
            $chartHadir[]      = $dayData ? $dayData->hadir : 0;
            $chartTerlambat[]  = $dayData ? $dayData->terlambat : 0;
        }

        $totalBulanIni = AbsensiPegawai::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->whereNotNull('waktu_checkin')
            ->count();

        // Pre-compute tepat_waktu series (hadir - terlambat) to avoid Blade arrow fn issue
        $chartTepatWaktu = array_map(fn($h, $t) => $h - $t, $chartHadir, $chartTerlambat);

        $bulanList = collect(range(1, 12))->map(fn($m) => ['value' => $m, 'label' => Carbon::createFromDate($tahun, $m, 1)->translatedFormat('F')]);

        return view('pages.sdm.absensi.laporan-bulanan', compact(
            'bulan', 'tahun', 'daysInMonth',
            'perHari', 'chartLabels', 'chartHadir', 'chartTerlambat', 'chartTepatWaktu',
            'totalBulanIni', 'bulanList', 'setting'
        ));
    }

    public function rekapitulasi(Request $request)
    {
        $bulan     = $request->input('bulan', Carbon::now()->month);
        $tahun     = $request->input('tahun', Carbon::now()->year);
        $userId    = $request->input('user_id');

        // Per-user summary for selected month
        $query = AbsensiPegawai::selectRaw('
                user_id,
                COUNT(*) as total_hari,
                SUM(CASE WHEN status = "tepat_waktu" THEN 1 ELSE 0 END) as total_tepat,
                SUM(CASE WHEN status = "terlambat" THEN 1 ELSE 0 END) as total_terlambat,
                SUM(CASE WHEN waktu_checkin IS NOT NULL THEN 1 ELSE 0 END) as total_hadir,
                SUM(CASE WHEN waktu_checkout IS NOT NULL THEN 1 ELSE 0 END) as total_checkout
            ')
            ->with('user')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->groupBy('user_id');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $rekap = $query->get()->sortBy('user.name');

        // Detail for selected user
        $detailUser = null;
        $detailAbsensi = collect();
        $chartData = [];

        if ($userId) {
            $detailUser = User::find($userId);
            $detailAbsensi = AbsensiPegawai::where('user_id', $userId)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->orderBy('tanggal')
                ->get();

            $chartData = [
                'labels' => $detailAbsensi->map(fn($a) => $a->tanggal->format('d/m')),
                'status' => $detailAbsensi->map(fn($a) => match($a->status) {
                    'tepat_waktu' => 1,
                    'terlambat'   => 0.5,
                    default       => 0,
                }),
            ];
        }

        $users = User::orderBy('name')->get();
        $bulanList = collect(range(1, 12))->map(fn($m) => ['value' => $m, 'label' => Carbon::createFromDate($tahun, $m, 1)->translatedFormat('F')]);

        return view('pages.sdm.absensi.rekapitulasi', compact(
            'bulan', 'tahun', 'rekap', 'users',
            'userId', 'detailUser', 'detailAbsensi', 'chartData', 'bulanList'
        ));
    }

    public function exportExcel(Request $request)
    {
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);

        $data = AbsensiPegawai::with('user')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal')
            ->orderBy('user_id')
            ->get();

        // Simple CSV export
        $filename = 'rekap-absensi-' . Carbon::createFromDate($tahun, $bulan, 1)->format('Y-m') . '.csv';
        $headers  = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=$filename"];

        $callback = function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Nama', 'Tanggal', 'Waktu Checkin', 'Waktu Checkout', 'Status', 'Dalam Radius', 'Durasi Kerja']);
            foreach ($data as $row) {
                fputcsv($handle, [
                    $row->user->name ?? '-',
                    $row->tanggal->format('d/m/Y'),
                    $row->waktu_checkin ? $row->waktu_checkin->format('H:i') : '-',
                    $row->waktu_checkout ? $row->waktu_checkout->format('H:i') : '-',
                    $row->status_label,
                    $row->dalam_radius_checkin ? 'Ya' : 'Tidak',
                    $row->durasi_kerja ?? '-',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
