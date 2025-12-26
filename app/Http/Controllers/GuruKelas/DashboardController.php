<?php

namespace App\Http\Controllers\GuruKelas;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\MasterGuru;
use App\Models\IzinMeninggalkanKelas;
use App\Models\Perizinan;
use App\Models\Rombel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\JamPelajaran;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $masterGuru = MasterGuru::where('user_id', $user->id)->first();

        // Jika data master guru tidak ditemukan, kembalikan view kosong
        if (!$masterGuru) {
            return view('pages.guru-kelas.dashboard.index', [
                'kelasDiajar' => collect(),
                'jadwalHariIni' => collect(),
                'siswaIzinHariIni' => collect()
            ]);
        }

        // 1. Data untuk Widget Kelas & Siswa yang Diajar
        $rombelIds = JadwalPelajaran::where('master_guru_id', $masterGuru->id)
            ->distinct()
            ->pluck('rombel_id');

        $kelasDiajar = Rombel::withCount('siswa')
            ->with('kelas')
            ->whereIn('id', $rombelIds)
            ->get();

        // 2. Data untuk Widget Jadwal Mengajar Hari Ini
        $namaHariIni = $this->getNamaHari(now()->dayOfWeek);
        $jadwalHariIni = JadwalPelajaran::with(['rombel.kelas', 'mataPelajaran'])
            ->where('master_guru_id', $masterGuru->id)
            ->where('hari', $namaHariIni)
            ->orderBy('jam_mulai')
            ->get();

        // 3. Data untuk Widget Siswa Izin Hari Ini (dari kelas yang diajar)
        $siswaIzinHariIni = Perizinan::with(['user', 'user.masterSiswa.rombels.kelas'])
            ->where('status', '!=', 'ditolak') // Tampilkan yang diajukan & disetujui
            ->whereDate('tanggal_izin', today())
            ->whereHas('user.masterSiswa.rombels', function ($query) use ($rombelIds) {
                $query->whereIn('rombels.id', $rombelIds);
            })
            ->get();

        // ==================================================
        //      LOGIKA BARU: Siswa yang Sedang di Luar Kelas
        // ==================================================
        $siswaSedangKeluar = IzinMeninggalkanKelas::with(['siswa', 'rombel.kelas'])
            ->where('status', 'diverifikasi_security') // Status: sudah diverifikasi keluar oleh satpam
            ->whereIn('rombel_id', $rombelIds)
            ->get();

        // ==================================================
        //      LOGIKA BARU: Statistik Izin Keluar Kelas
        // ==================================================

        // 1. Grafik: Top 5 Siswa Paling Sering Izin Keluar
        $topSiswaIzinKeluar = IzinMeninggalkanKelas::select('user_id', DB::raw('count(*) as total'))
            ->whereIn('rombel_id', $rombelIds)
            ->groupBy('user_id')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();
        $topSiswaUserIds = $topSiswaIzinKeluar->pluck('user_id');
        $topSiswaUsers = User::whereIn('id', $topSiswaUserIds)->get()->keyBy('id');
        $topSiswaIzinKeluarChartData = [
            'labels' => $topSiswaIzinKeluar->map(fn($item) => $topSiswaUsers->get($item->user_id)->name ?? 'Siswa Dihapus'),
            'data' => $topSiswaIzinKeluar->pluck('total'),
        ];

        // 2. Grafik: Top 5 Tujuan Izin Keluar
        $tujuanIzinKeluarChart = IzinMeninggalkanKelas::select('tujuan', DB::raw('count(*) as total'))
            ->whereIn('rombel_id', $rombelIds)
            ->groupBy('tujuan')
            ->orderBy('total', 'desc')
            ->take(5)
            ->pluck('total', 'tujuan');
        $tujuanIzinKeluarChartData = [
            'labels' => $tujuanIzinKeluarChart->keys(),
            'data' => $tujuanIzinKeluarChart->values(),
        ];

        // ==================================================
        //      LOGIKA BARU: Kegiatan Sekolah Saat Ini
        // ==================================================
        $currentTime = now()->format('H:i:s');
        $namaHariIni = $this->getNamaHari(now()->dayOfWeek);
        
        $kegiatanSaatIni = JamPelajaran::where('jam_mulai', '<=', $currentTime)
            ->where('jam_selesai', '>=', $currentTime)
            ->whereNotNull('tipe_kegiatan')
            ->where(function ($query) use ($namaHariIni) {
                $query->where('hari', $namaHariIni) // Prioritaskan override hari ini
                      ->orWhereNull('hari');        // Atau jadwal umum
            })
            ->orderByRaw('hari IS NULL ASC') // Pastikan yang ada harinya (override) didahulukan
            ->first();

        // Validasi tambahan untuk upacara (Senin) dan 4R (Jumat) jika pakai jadwal umum
        if ($kegiatanSaatIni && !$kegiatanSaatIni->hari) {
            if ($kegiatanSaatIni->tipe_kegiatan == 'upacara' && $namaHariIni != 'Senin') {
                $kegiatanSaatIni = null;
            } elseif ($kegiatanSaatIni->tipe_kegiatan == 'kegiatan_4r' && $namaHariIni != 'Jumat') {
                $kegiatanSaatIni = null;
            }
        }

        return view('pages.guru-kelas.dashboard.index', compact(
            'kelasDiajar',
            'jadwalHariIni',
            'siswaIzinHariIni',
            'siswaSedangKeluar',
            'topSiswaIzinKeluarChartData',
            'tujuanIzinKeluarChartData',
            'kegiatanSaatIni' // <-- Kirim data kegiatan
        ));
    }

    private function getNamaHari($dayOfWeek)
    {
        $hari = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
        return $hari[$dayOfWeek] ?? 'Tidak Diketahui';
    }
}
