<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\Perizinan;
use Illuminate\Http\Request;

class BKMonitoringController extends Controller
{
    public function index()
    {
        $perizinans = Perizinan::with(['siswa', 'picket', 'guruPiket'])
            ->latest()
            ->paginate(15);

        return view('pages.bk.monitoring.index', compact('perizinans'));
    }
}
