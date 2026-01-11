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
            'total_izin_sekolah' => GuruIzin::where('status_sdm', 'disetujui')->where('kategori_penyetujuan', 'sekolah')->count(),
            'total_izin_luar' => GuruIzin::where('status_sdm', 'disetujui')->where('kategori_penyetujuan', 'luar')->count(),
            'total_terlambat' => GuruIzin::where('status_sdm', 'disetujui')->where('kategori_penyetujuan', 'terlambat')->count(),
            'total_approved' => GuruIzin::where('status_sdm', 'disetujui')->count(),
            'latest_requests' => GuruIzin::with('guru')->latest()->take(5)->get(),
        ];
        return view('pages.sdm.dashboard', compact('stats'));
    }

    public function monitoring()
    {
        $stats = [
            'total_izin_sekolah' => GuruIzin::where('kategori_penyetujuan', 'sekolah')->where('status_sdm', 'disetujui')->count(),
            'total_izin_luar' => GuruIzin::where('kategori_penyetujuan', 'luar')->where('status_sdm', 'disetujui')->count(),
            'total_terlambat' => GuruIzin::where('kategori_penyetujuan', 'terlambat')->where('status_sdm', 'disetujui')->count(),
            'total_pending' => GuruIzin::where('status_sdm', 'menunggu')->count(),
        ];

        // Chart Data (Last 7 Days)
        $chartData = [
            'labels' => [],
            'izin_sekolah' => [],
            'izin_luar' => [],
            'terlambat' => [],
        ];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $label = now()->subDays($i)->translatedFormat('d M');
            
            $chartData['labels'][] = $label;
            $chartData['izin_sekolah'][] = GuruIzin::whereDate('tanggal_mulai', $date)->where('kategori_penyetujuan', 'sekolah')->where('status_sdm', 'disetujui')->count();
            $chartData['izin_luar'][] = GuruIzin::whereDate('tanggal_mulai', $date)->where('kategori_penyetujuan', 'luar')->where('status_sdm', 'disetujui')->count();
            $chartData['terlambat'][] = GuruIzin::whereDate('tanggal_mulai', $date)->where('kategori_penyetujuan', 'terlambat')->where('status_sdm', 'disetujui')->count();
        }

        return view('pages.sdm.monitoring.index', compact('stats', 'chartData'));
    }
}
