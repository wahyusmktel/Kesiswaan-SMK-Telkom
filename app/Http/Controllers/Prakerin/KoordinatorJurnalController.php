<?php

namespace App\Http\Controllers\Prakerin;

use App\Http\Controllers\Controller;
use App\Models\PrakerinAbsensi;
use App\Models\PrakerinJurnal;
use App\Models\PrakerinPenempatan;
use Illuminate\Support\Facades\DB;

class KoordinatorJurnalController extends Controller
{
    public function index()
    {
        $jurnals = PrakerinJurnal::with(['penempatan.siswa', 'penempatan.rombelPkl', 'penempatan.industri', 'penempatan.guruPembimbing'])
            ->latest('tanggal')
            ->paginate(20);

        return view('pages.prakerin.koordinator-jurnal.index', compact('jurnals'));
    }

    public function analytics()
    {
        $studentActivity = PrakerinPenempatan::with(['siswa', 'rombelPkl'])
            ->withCount(['jurnals', 'absensis'])
            ->where('status', 'aktif')
            ->orderByDesc('jurnals_count')
            ->limit(15)
            ->get();

        $teacherActivity = DB::table('prakerin_penempatans')
            ->join('master_gurus', 'master_gurus.id', '=', 'prakerin_penempatans.master_guru_id')
            ->leftJoin('prakerin_jurnals', function ($join) {
                $join->on('prakerin_jurnals.prakerin_penempatan_id', '=', 'prakerin_penempatans.id')
                    ->where('prakerin_jurnals.status_verifikasi', 'disetujui');
            })
            ->selectRaw('master_gurus.nama_lengkap, count(distinct prakerin_penempatans.id) as siswa_count, count(prakerin_jurnals.id) as reviewed_jurnals_count')
            ->where('prakerin_penempatans.status', 'aktif')
            ->groupBy('master_gurus.id', 'master_gurus.nama_lengkap')
            ->orderByDesc('reviewed_jurnals_count')
            ->limit(10)
            ->get();

        $summary = [
            'total_jurnal' => PrakerinJurnal::count(),
            'jurnal_menunggu' => PrakerinJurnal::where('status_verifikasi', 'menunggu')->count(),
            'jurnal_ditinjau' => PrakerinJurnal::where('status_verifikasi', 'disetujui')->count(),
            'total_absensi' => PrakerinAbsensi::count(),
        ];

        return view('pages.prakerin.koordinator-jurnal.analytics', compact('studentActivity', 'teacherActivity', 'summary'));
    }
}
