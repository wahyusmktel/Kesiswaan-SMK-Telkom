<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\Perizinan;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_siswa' => \App\Models\MasterSiswa::count(),
            'pending_konsultasi' => \App\Models\BKKonsultasiJadwal::where('status', 'pending')->count(),
            'total_pembinaan' => \App\Models\BKPembinaanRutin::count(),
            'izin_hari_ini' => \App\Models\Perizinan::whereDate('created_at', today())->count(),
        ];

        $recent_konsultasi = \App\Models\BKKonsultasiJadwal::with('siswa')->latest()->limit(5)->get();

        $chat_rooms = \App\Models\BKChatRoom::with(['siswa', 'messages' => fn($q) => $q->latest()->limit(1)])
            ->withCount([
                'messages as unread_count' => function ($q) {
                    $q->where('sender_id', '!=', \Illuminate\Support\Facades\Auth::id())->where('is_read', false);
                }
            ])
            ->where('guru_bk_user_id', \Illuminate\Support\Facades\Auth::id())
            ->orderBy('last_message_at', 'desc')
            ->limit(5)
            ->get();

        // Grafik Keterlambatan (1 bulan terakhir, kecuali Sabtu & Minggu)
        $lateness_data = [];
        $lateness_labels = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            // 0 = Sunday, 6 = Saturday
            if ($date->dayOfWeek !== 0 && $date->dayOfWeek !== 6) {
                $count = \App\Models\Keterlambatan::whereDate('waktu_dicatat_security', $date->toDateString())->count();
                $lateness_labels[] = $date->translatedFormat('d M');
                $lateness_data[] = $count;
            }
        }

        // Keterlambatan Hari Ini
        $today_lateness = \App\Models\Keterlambatan::with(['siswa', 'siswa.rombels.kelas'])
            ->whereDate('waktu_dicatat_security', today())
            ->latest('waktu_dicatat_security')
            ->limit(10)
            ->get();

        return view('pages.bk.dashboard.index', compact(
            'stats',
            'recent_konsultasi',
            'chat_rooms',
            'lateness_data',
            'lateness_labels',
            'today_lateness'
        ));
    }
}
