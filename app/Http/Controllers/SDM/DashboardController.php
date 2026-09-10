<?php

namespace App\Http\Controllers\SDM;

use App\Http\Controllers\Controller;
use App\Models\GuruIzin;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_pending' => GuruIzin::where('status_kurikulum', 'disetujui')->where('status_sdm', 'menunggu')->count(),
            'total_izin_sekolah' => GuruIzin::fullyApproved()->where('kategori_penyetujuan', 'sekolah')->count(),
            'total_izin_luar' => GuruIzin::fullyApproved()->where('kategori_penyetujuan', 'luar')->count(),
            'total_tidak_masuk' => GuruIzin::fullyApproved()->where('kategori_penyetujuan', 'tidak_masuk')->count(),
            'total_terlambat' => GuruIzin::fullyApproved()->where('kategori_penyetujuan', 'terlambat')->count(),
            'total_approved' => GuruIzin::fullyApproved()->count(),
            'latest_requests' => GuruIzin::with('guru')->latest()->take(5)->get(),
        ];

        return view('pages.sdm.dashboard', compact('stats'));
    }

    public function monitoring()
    {
        $stats = [
            'total_izin_sekolah' => GuruIzin::fullyApproved()->where('kategori_penyetujuan', 'sekolah')->count(),
            'total_izin_luar' => GuruIzin::fullyApproved()->where('kategori_penyetujuan', 'luar')->count(),
            'total_tidak_masuk' => GuruIzin::fullyApproved()->where('kategori_penyetujuan', 'tidak_masuk')->count(),
            'total_terlambat' => GuruIzin::fullyApproved()->where('kategori_penyetujuan', 'terlambat')->count(),
            'total_pending' => GuruIzin::where('status_sdm', 'menunggu')->count(),
        ];

        // Chart Data (Last 7 Days)
        $chartData = [
            'labels' => [],
            'izin_sekolah' => [],
            'izin_luar' => [],
            'tidak_masuk' => [],
            'terlambat' => [],
        ];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $label = now()->subDays($i)->translatedFormat('d M');

            $chartData['labels'][] = $label;
            $chartData['izin_sekolah'][] = GuruIzin::whereDate('tanggal_mulai', $date)->fullyApproved()->where('kategori_penyetujuan', 'sekolah')->count();
            $chartData['izin_luar'][] = GuruIzin::whereDate('tanggal_mulai', $date)->fullyApproved()->where('kategori_penyetujuan', 'luar')->count();
            $chartData['tidak_masuk'][] = GuruIzin::whereDate('tanggal_mulai', $date)->fullyApproved()->where('kategori_penyetujuan', 'tidak_masuk')->count();
            $chartData['terlambat'][] = GuruIzin::whereDate('tanggal_mulai', $date)->fullyApproved()->where('kategori_penyetujuan', 'terlambat')->count();
        }

        return view('pages.sdm.monitoring.index', compact('stats', 'chartData'));
    }
}
