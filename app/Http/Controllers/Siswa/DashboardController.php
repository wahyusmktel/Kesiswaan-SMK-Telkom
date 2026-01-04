<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Perizinan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\JamPelajaran;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // 1. Data untuk Widget Ringkasan
        $totalDiajukan = Perizinan::where('user_id', $userId)->count();
        $totalDisetujui = Perizinan::where('user_id', $userId)->where('status', 'disetujui')->count();
        $totalDitolak = Perizinan::where('user_id', $userId)->where('status', 'ditolak')->count();
        
        $siswa = Auth::user()->masterSiswa;
        $totalPanggilan = $siswa ? $siswa->panggilans()->count() : 0;

        // 2. Data untuk Pie Chart
        $statusData = Perizinan::where('user_id', $userId)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusChartData = [
            'labels' => $statusData->keys(),
            'data' => $statusData->values(),
        ];

        // 3. Data Poin Pelanggaran
        $siswa = Auth::user()->masterSiswa;
        $poinData = [
            'current_points' => $siswa ? $siswa->getCurrentPoints() : 0,
            'status' => $siswa ? $siswa->getPointStatus() : ['label' => 'Aman', 'class' => 'bg-green-500'],
            'total_pelanggaran' => $siswa ? $siswa->getTotalViolationPoints() : 0,
            'total_prestasi' => $siswa ? $siswa->getTotalAchievementPoints() : 0,
            'total_pemutihan' => $siswa ? $siswa->getTotalExpungementPoints() : 0,
            'recent_activities' => $siswa ? collect([
                ...$siswa->pelanggarans()->with('peraturan')->latest()->take(3)->get()->map(fn($p) => [
                    'type' => 'Pelanggaran',
                    'title' => $p->peraturan->deskripsi,
                    'points' => -$p->peraturan->bobot_poin,
                    'date' => $p->tanggal,
                    'color' => 'text-red-600',
                    'bg' => 'bg-red-50'
                ]),
                ...$siswa->prestasis()->latest()->take(3)->get()->map(fn($p) => [
                    'type' => 'Prestasi',
                    'title' => $p->nama_prestasi,
                    'points' => $p->poin_bonus,
                    'date' => $p->tanggal,
                    'color' => 'text-green-600',
                    'bg' => 'bg-green-50'
                ]),
                ...$siswa->pemutihans()->where('status', 'disetujui')->latest()->take(3)->get()->map(fn($p) => [
                    'type' => 'Pemutihan',
                    'title' => $p->keterangan ?? 'Pemutihan Poin',
                    'points' => $p->poin_dikurangi,
                    'date' => $p->tanggal,
                    'color' => 'text-blue-600',
                    'bg' => 'bg-blue-50'
                ]),
            ])->sortByDesc('date')->take(5) : collect([]),
        ];

        $panggilanAktif = $siswa ? $siswa->panggilans()
            ->where('status', 'terkirim')
            ->latest()
            ->first() : null;

        $konsultasiHariIni = $siswa ? $siswa->konsultasiJadwals()
            ->whereDate('tanggal_rencana', today())
            ->where('status', 'approved')
            ->first() : null;

        $chat_rooms = \App\Models\BKChatRoom::with(['guruBK', 'messages' => fn($q) => $q->latest()->limit(1)])
            ->withCount(['messages as unread_count' => function($q) use ($userId) {
                $q->where('sender_id', '!=', $userId)->where('is_read', false);
            }])
            ->where('siswa_user_id', $userId)
            ->orderBy('last_message_at', 'desc')
            ->limit(5)
            ->get();

        $terlambatHariIni = $siswa ? $siswa->keterlambatans()
            ->whereDate('waktu_dicatat_security', today())
            ->latest()
            ->first() : null;

        $kegiatanSaatIni = $this->getKegiatanSaatIni();

        return view('pages.siswa.dashboard.index', compact(
            'totalDiajukan',
            'totalDisetujui',
            'totalDitolak',
            'totalPanggilan',
            'statusChartData',
            'poinData',
            'panggilanAktif',
            'konsultasiHariIni',
            'chat_rooms',
            'terlambatHariIni',
            'kegiatanSaatIni'
        ));
    }

    private function getKegiatanSaatIni()
    {
        $currentTime = now()->format('H:i:s');
        $namaHariIni = $this->getNamaHari(now()->dayOfWeek);
        
        $kegiatan = JamPelajaran::where('jam_mulai', '<=', $currentTime)
            ->where('jam_selesai', '>=', $currentTime)
            ->whereNotNull('tipe_kegiatan')
            ->where(function ($query) use ($namaHariIni) {
                $query->where('hari', $namaHariIni)
                      ->orWhereNull('hari');
            })
            ->orderByRaw('hari IS NULL ASC')
            ->first();

        if ($kegiatan && !$kegiatan->hari) {
            if ($kegiatan->tipe_kegiatan == 'upacara' && $namaHariIni != 'Senin') {
                $kegiatan = null;
            } elseif ($kegiatan->tipe_kegiatan == 'kegiatan_4r' && $namaHariIni != 'Jumat') {
                $kegiatan = null;
            }
        }

        return $kegiatan;
    }

    private function getNamaHari($dayOfWeek)
    {
        $hari = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
        return $hari[$dayOfWeek] ?? 'Tidak Diketahui';
    }
}
