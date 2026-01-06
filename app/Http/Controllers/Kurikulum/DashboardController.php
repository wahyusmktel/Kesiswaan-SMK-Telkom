<?php

namespace App\Http\Controllers\Kurikulum;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\MasterGuru;
use App\Models\MataPelajaran;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Models\JamPelajaran;
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

        // 4. Data Top 5 Guru (Session-Based: Teacher + Rombel + Mapel + Date)
        $baseQuery = \App\Models\AbsensiGuru::join('jadwal_pelajarans', 'absensi_guru.jadwal_pelajaran_id', '=', 'jadwal_pelajarans.id')
            ->join('master_gurus', 'jadwal_pelajarans.master_guru_id', '=', 'master_gurus.id')
            ->join('rombels', 'jadwal_pelajarans.rombel_id', '=', 'rombels.id')
            ->select('master_gurus.nama_lengkap', 'master_gurus.id', 
                DB::raw('COUNT(DISTINCT jadwal_pelajarans.rombel_id, jadwal_pelajarans.mata_pelajaran_id, absensi_guru.tanggal) as total'))
            ->groupBy('master_gurus.id', 'master_gurus.nama_lengkap');

        if ($tahunAktifId) {
            $baseQuery->where('rombels.tahun_pelajaran_id', $tahunAktifId);
        }

        $topRajin = (clone $baseQuery)->where('status', 'hadir')->orderByDesc('total')->take(5)->get();
        $topTerlambat = (clone $baseQuery)->where('status', 'terlambat')->orderByDesc('total')->take(5)->get();
        $topAbsen = (clone $baseQuery)->where('status', 'tidak_hadir')->orderByDesc('total')->take(5)->get();
        $topIzin = (clone $baseQuery)->where('status', 'izin')->orderByDesc('total')->take(5)->get();
        $kegiatanSaatIni = $this->getKegiatanSaatIni();

        return view('pages.kurikulum.dashboard.index', compact(
            'totalGuru',
            'totalMapel',
            'totalRombel',
            'jadwalHariIni',
            'mapelChartData',
            'tahunAktif',
            'topRajin',
            'topTerlambat',
            'topAbsen',
            'topIzin',
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
