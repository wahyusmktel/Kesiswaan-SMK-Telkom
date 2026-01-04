<?php

namespace App\Http\Controllers\Piket;

use App\Http\Controllers\Controller;
use App\Models\IzinMeninggalkanKelas;
use App\Models\Perizinan;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Models\User; // <-- Pastikan User model di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\JamPelajaran;

class DashboardController extends Controller
{
    public function index()
    {
        $piketUserId = Auth::id();

        // ==================================================
        //      BAGIAN 1: DATA LAMA ANDA (IZIN TIDAK MASUK)
        // ==================================================

        // Data untuk Widget Izin Hari Ini (Izin Tidak Masuk)
        $izinHariIni = Perizinan::with(['user.masterSiswa.rombels.kelas'])
            ->whereDate('tanggal_izin', today())
            ->latest('updated_at')
            ->get();

        // Data untuk Pie Chart Status Izin
        $statusData = Perizinan::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // Data untuk Line Chart Tren Harian (30 hari terakhir)
        $dailyData = Perizinan::where('tanggal_izin', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get([
                DB::raw('DATE(tanggal_izin) as date'),
                DB::raw('COUNT(*) as count')
            ])->pluck('count', 'date');
        $dates = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dates->put($date, $dailyData->get($date, 0));
        }

        // Data untuk Bar Chart Izin per Rombel
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        
        if ($tahunAktif) {
            $rombelData = Rombel::where('tahun_pelajaran_id', $tahunAktif->id)
                ->with('kelas')
                ->join('kelas', 'rombels.kelas_id', '=', 'kelas.id')
                ->select('rombels.id', 'kelas.nama_kelas', DB::raw('(SELECT COUNT(*) FROM perizinan JOIN users ON perizinan.user_id = users.id JOIN master_siswa ON users.id = master_siswa.user_id JOIN rombel_siswa ON master_siswa.id = rombel_siswa.master_siswa_id WHERE rombel_siswa.rombel_id = rombels.id) as total_izin'))
                ->orderBy('total_izin', 'desc')
                ->get();
        } else {
            $rombelData = collect();
        }


        // ==================================================
        //      BAGIAN 2: DATA BARU (IZIN MENINGGALKAN KELAS)
        // ==================================================

        // Widget: Total Izin Diproses oleh Anda
        $totalIzinDiprosesPiket = IzinMeninggalkanKelas::where('guru_piket_approval_id', $piketUserId)->count();

        // Grafik: Tren Harian Izin yang Anda Proses
        $dailyDataPiket = IzinMeninggalkanKelas::where('guru_piket_approval_id', $piketUserId)
            ->where('guru_piket_approved_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get([
                DB::raw('DATE(guru_piket_approved_at) as date'),
                DB::raw('COUNT(*) as count')
            ])->pluck('count', 'date');
        $datesPiket = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $datesPiket->put($date, $dailyDataPiket->get($date, 0));
        }

        // Grafik: Top 5 Tujuan Izin yang Anda Setujui
        $tujuanChartPiket = IzinMeninggalkanKelas::where('guru_piket_approval_id', $piketUserId)
            ->select('tujuan', DB::raw('count(*) as total'))
            ->groupBy('tujuan')
            ->orderBy('total', 'desc')
            ->take(5)
            ->pluck('total', 'tujuan');

        // ==================================================
        //      PERBAIKAN QUERY TOP 10 SISWA
        // ==================================================
        // Widget Top 10 Siswa (Personal - yang Anda proses)
        $topSiswaPersonalData = IzinMeninggalkanKelas::select('user_id', DB::raw('count(*) as total_izin'))
            ->where('guru_piket_approval_id', $piketUserId)
            ->groupBy('user_id')
            ->orderBy('total_izin', 'desc')
            ->take(10)
            ->get();
        $topSiswaPersonalIds = $topSiswaPersonalData->pluck('user_id');
        $topSiswaPersonalUsers = User::whereIn('id', $topSiswaPersonalIds)->get()->keyBy('id');
        $topSiswaIzinKeluarPersonal = $topSiswaPersonalData->map(function ($item) use ($topSiswaPersonalUsers) {
            $user = $topSiswaPersonalUsers->get($item->user_id);
            if ($user) {
                $user->izin_meninggalkan_kelas_count = $item->total_izin;
                return $user;
            }
            return null;
        })->filter();

        // Widget BARU: Top 10 Siswa (Global)
        $topSiswaIzinKeluarGlobal = User::role('Siswa')
            ->withCount('izinMeninggalkanKelas')
            ->orderBy('izin_meninggalkan_kelas_count', 'desc')
            ->take(10)
            ->get();


        // ==================================================
        //      BAGIAN 4: DATA KETERLAMBATAN (REQ USER)
        // ==================================================
        
        // Widget: Keterlambatan Hari Ini
        $keterlambatanHariIni = \App\Models\Keterlambatan::whereDate('waktu_dicatat_security', today())->count();

        // Widget: Total Keterlambatan
        $totalKeterlambatan = \App\Models\Keterlambatan::count();

        // Chart: Analisa Keterlambatan (Tren 30 Hari)
        $dailyKeterlambatan = \App\Models\Keterlambatan::where('waktu_dicatat_security', '>=', now()->subDays(30))
            ->selectRaw('DATE(waktu_dicatat_security) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->pluck('count', 'date');

        $datesKeterlambatan = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $datesKeterlambatan->put($date, $dailyKeterlambatan->get($date, 0));
        }

        // List: Siswa Terlambat Hari Ini (Detail)
        $detailKeterlambatanHariIni = \App\Models\Keterlambatan::with(['siswa.rombels.kelas', 'security'])
            ->whereDate('waktu_dicatat_security', today())
            ->orderBy('waktu_dicatat_security', 'desc')
            ->get();

        // List: Kelas Paling Sering Terlambat (Top 5)
        $topKelasTerlambat = DB::table('keterlambatans')
            ->join('master_siswa', 'keterlambatans.master_siswa_id', '=', 'master_siswa.id')
            ->join('rombel_siswa', 'master_siswa.id', '=', 'rombel_siswa.master_siswa_id')
            ->join('rombels', 'rombel_siswa.rombel_id', '=', 'rombels.id')
            ->join('kelas', 'rombels.kelas_id', '=', 'kelas.id')
            ->where('rombels.tahun_pelajaran_id', $tahunAktif ? $tahunAktif->id : 0) 
            ->select('kelas.nama_kelas', DB::raw('count(*) as total'))
            ->groupBy('kelas.nama_kelas')
            ->orderByDesc('total')
            ->limit(5)
            ->get();


        // ==================================================
        //      BAGIAN 3: MENGIRIM SEMUA DATA KE VIEW
        // ==================================================
        // Recent Activity Feed (Combined)
        $recentPermissions = Perizinan::with('user')->latest()->take(5)->get()->map(function($item) {
            return [
                'type' => 'Izin Tidak Masuk',
                'name' => $item->user->name,
                'time' => $item->created_at,
                'status' => $item->status,
                'color' => 'amber'
            ];
        });

        $recentLate = \App\Models\Keterlambatan::with('siswa.user')->latest()->take(5)->get()->map(function($item) {
            return [
                'type' => 'Keterlambatan',
                'name' => $item->siswa->user->name,
                'time' => $item->waktu_dicatat_security,
                'status' => $item->status,
                'color' => 'red'
            ];
        });

        $recentActivity = $recentPermissions->concat($recentLate)->sortByDesc('time')->take(5);

        return view('pages.piket.dashboard.index', [
            // Variabel dari statistik umum
            'izinHariIni' => $izinHariIni,
            'statusChartData' => ['labels' => $statusData->keys(), 'data' => $statusData->values()],
            'dailyChartData' => ['labels' => $dates->keys()->map(fn($date) => \Carbon\Carbon::parse($date)->format('d M')), 'data' => $dates->values()],
            'rombelChartData' => ['labels' => $rombelData->pluck('nama_kelas'), 'data' => $rombelData->pluck('total_izin')],

            // Variabel untuk statistik personal & global Guru Piket
            'totalIzinDiprosesPiket' => $totalIzinDiprosesPiket,
            'dailyChartDataPiket' => ['labels' => $datesPiket->keys()->map(fn($date) => \Carbon\Carbon::parse($date)->format('d M')), 'data' => $datesPiket->values()],
            'tujuanChartDataPiket' => ['labels' => $tujuanChartPiket->keys(), 'data' => $tujuanChartPiket->values()],
            'topSiswaIzinKeluarPersonal' => $topSiswaIzinKeluarPersonal,
            'topSiswaIzinKeluarGlobal' => $topSiswaIzinKeluarGlobal,
            'kegiatanSaatIni' => $this->getKegiatanSaatIni(),
            
            // New Data Keterlambatan
            'keterlambatanHariIni' => $keterlambatanHariIni,
            'totalKeterlambatan' => $totalKeterlambatan,
            'analisaKeterlambatanChart' => ['labels' => $datesKeterlambatan->keys()->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M')), 'data' => $datesKeterlambatan->values()],
            'topKelasTerlambat' => $topKelasTerlambat,
            'detailKeterlambatan' => $detailKeterlambatanHariIni,
            'recentActivity' => $recentActivity,
        ]);
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
