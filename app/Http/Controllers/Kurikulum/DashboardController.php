<?php

namespace App\Http\Controllers\Kurikulum;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\MasterGuru;
use App\Models\MataPelajaran;
use App\Models\Rombel;
use App\Models\TahunPelajaran; // Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 0. Ambil Tahun Ajaran Aktif
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        // Fallback jika tidak ada yang aktif
        if (!$tahunAktif) {
            $tahunAktif = TahunPelajaran::latest()->first();
        }
        $tahunAktifId = $tahunAktif ? $tahunAktif->id : null;

        // 1. Data untuk Widget Ringkasan
        $totalGuru = MasterGuru::count();
        $totalMapel = MataPelajaran::count();

        // Hitung Rombel HANYA di tahun aktif
        $totalRombel = 0;
        if ($tahunAktifId) {
            $totalRombel = Rombel::where('tahun_pelajaran_id', $tahunAktifId)->count();
        }

        // 2. Data untuk Widget Jadwal Hari Ini
        // Filter jadwal agar hanya memunculkan jadwal dari Rombel tahun ini
        $namaHariIni = $this->getNamaHari(now()->dayOfWeek);
        $jadwalQuery = JadwalPelajaran::with(['rombel.kelas', 'mataPelajaran', 'guru'])
            ->whereHas('rombel', function ($q) use ($tahunAktifId) {
                if ($tahunAktifId) {
                    $q->where('tahun_pelajaran_id', $tahunAktifId);
                }
            })
            ->where('hari', $namaHariIni)
            ->orderBy('jam_mulai')
            ->get();

        // Kelompokkan jadwal berdasarkan nama kelas
        $jadwalHariIni = $jadwalQuery->groupBy('rombel.kelas.nama_kelas');

        // 3. Data untuk Chart Mata Pelajaran
        // Filter chart agar hanya menghitung beban jam di tahun ini
        $mapelChart = JadwalPelajaran::with('mataPelajaran')
            ->whereHas('rombel', function ($q) use ($tahunAktifId) {
                if ($tahunAktifId) {
                    $q->where('tahun_pelajaran_id', $tahunAktifId);
                }
            })
            ->select('mata_pelajaran_id', DB::raw('count(*) as total_jam'))
            ->groupBy('mata_pelajaran_id')
            ->orderBy('total_jam', 'desc')
            ->take(7)
            ->get();

        $mapelChartData = [
            'labels' => $mapelChart->map(fn($item) => $item->mataPelajaran->nama_mapel),
            'data' => $mapelChart->pluck('total_jam'),
        ];

        return view('pages.kurikulum.dashboard.index', compact(
            'totalGuru',
            'totalMapel',
            'totalRombel',
            'jadwalHariIni',
            'mapelChartData',
            'tahunAktif' // Kirim variabel ini buat ditampilkan di header dashboard
        ));
    }

    private function getNamaHari($dayOfWeek)
    {
        $hari = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
        return $hari[$dayOfWeek] ?? 'Tidak Diketahui';
    }
}
