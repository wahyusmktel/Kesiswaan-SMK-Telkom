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
}
