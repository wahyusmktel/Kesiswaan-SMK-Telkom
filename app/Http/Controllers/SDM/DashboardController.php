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
            'total_approved' => GuruIzin::where('status_sdm', 'disetujui')->count(),
            'latest_requests' => GuruIzin::with('guru')->latest()->take(5)->get(),
        ];
        return view('pages.sdm.dashboard', compact('stats'));
    }

    public function monitoring()
    {
        $stats = [
            'total_izin' => GuruIzin::where('status_sdm', 'disetujui')->count(),
            'total_pending' => GuruIzin::where('status_sdm', 'menunggu')->count(),
        ];

        // Chart Data (Last 7 Days)
        $chartData = [
            'labels' => [],
            'izin' => [],
        ];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $label = now()->subDays($i)->translatedFormat('d M');
            
            $chartData['labels'][] = $label;
            $chartData['izin'][] = GuruIzin::whereDate('tanggal_mulai', $date)->where('status_sdm', 'disetujui')->count();
        }

        return view('pages.sdm.monitoring.index', compact('stats', 'chartData'));
    }
}
