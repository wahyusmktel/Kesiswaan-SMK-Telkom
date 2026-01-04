<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\IzinMeninggalkanKelas;
use App\Models\Keterlambatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->startOfDay();

        // 1. Statistik
        // Total verifikasi keluar/kembali hari ini oleh siapa saja (atau spesifik user?)
        // Biasanya dashboard peran menunjukkan operasional sekolah hari ini
        $totalVerifikasiHariIni = IzinMeninggalkanKelas::whereDate('security_verified_at', $today)
            ->orWhereDate('waktu_kembali_sebenarnya', $today)
            ->count();

        // Siswa yang sedang di luar (sudah verifikasi security tapi belum kembali)
        $siswaDiLuar = IzinMeninggalkanKelas::where('status', 'diverifikasi_security')->count();

        // Pendataan terlambat hari ini
        $terlambatHariIni = Keterlambatan::whereDate('waktu_dicatat_security', $today)->count();

        // 2. Aktivitas Terbaru (Gabungan Izin & Terlambat jika memungkinkan, atau pisah)
        $recentIzin = IzinMeninggalkanKelas::with(['siswa'])
            ->whereNotNull('security_verified_at')
            ->latest('security_verified_at')
            ->take(5)
            ->get();

        $recentTerlambat = Keterlambatan::with(['siswa.rombels.kelas'])
            ->latest('waktu_dicatat_security')
            ->take(5)
            ->get();

        return view('pages.security.dashboard.index', compact(
            'totalVerifikasiHariIni',
            'siswaDiLuar',
            'terlambatHariIni',
            'recentIzin',
            'recentTerlambat'
        ));
    }
}
