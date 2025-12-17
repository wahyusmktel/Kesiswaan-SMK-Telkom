<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Perizinan;
use App\Models\Rombel;
use App\Models\TahunPelajaran; // Tambahkan Import ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\IzinMeninggalkanKelas;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil Tahun Ajaran Aktif dari Database
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        // Jika belum ada yang aktif, ambil yang terakhir
        if (!$tahunAktif) {
            $tahunAktif = TahunPelajaran::latest()->first();
        }

        $tahunAktifId = $tahunAktif ? $tahunAktif->id : null;

        // ==================================================
        //      BAGIAN 1: DATA UNTUK IZIN TIDAK MASUK
        // ==================================================

        // Pie Chart Status
        $statusData = Perizinan::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // Line Chart Tren Harian
        $dailyData = Perizinan::where('tanggal_izin', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get([
                DB::raw('DATE(tanggal_izin) as date'),
                DB::raw('COUNT(*) as count')
            ])
            ->pluck('count', 'date');

        $dates = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dates->put($date, $dailyData->get($date, 0));
        }

        // Bar Chart Izin Tidak Masuk per Rombel (Filter by Tahun Aktif ID)
        $rombelIzinTidakMasukChart = collect();

        if ($tahunAktifId) {
            $rombelIzinTidakMasukChart = Rombel::where('tahun_pelajaran_id', $tahunAktifId) // Ubah query di sini
                ->join('kelas', 'rombels.kelas_id', '=', 'kelas.id')
                ->select('kelas.nama_kelas', DB::raw('(SELECT COUNT(*) FROM perizinan JOIN users ON perizinan.user_id = users.id JOIN master_siswa ON users.id = master_siswa.user_id JOIN rombel_siswa ON master_siswa.id = rombel_siswa.master_siswa_id WHERE rombel_siswa.rombel_id = rombels.id) as total_izin'))
                ->orderBy('total_izin', 'desc')
                ->get();
        }

        // Aktivitas Terakhir
        $latestActivities = Perizinan::with(['user', 'approver'])
            ->latest('updated_at')
            ->take(10)
            ->get();


        // ==================================================
        //      BAGIAN 2: DATA UNTUK IZIN MENINGGALKAN KELAS
        // ==================================================

        // Widget Top Siswa
        $topSiswaIzinKeluar = User::role('Siswa')
            ->withCount('izinMeninggalkanKelas')
            ->orderBy('izin_meninggalkan_kelas_count', 'desc')
            ->take(10)
            ->get();

        // Grafik Rombel Izin Keluar (Filter by Tahun Aktif ID)
        $rombelIzinKeluarChart = collect();

        if ($tahunAktifId) {
            $rombelIzinKeluarChart = Rombel::where('tahun_pelajaran_id', $tahunAktifId) // Ubah query di sini
                ->join('kelas', 'rombels.kelas_id', '=', 'kelas.id')
                ->select('kelas.nama_kelas', DB::raw('(SELECT COUNT(*) FROM izin_meninggalkan_kelas WHERE izin_meninggalkan_kelas.rombel_id = rombels.id) as total_izin'))
                ->orderBy('total_izin', 'desc')->get();
        }

        // Grafik Tujuan
        $tujuanIzinKeluarChart = IzinMeninggalkanKelas::select('tujuan', DB::raw('count(*) as total'))
            ->groupBy('tujuan')
            ->orderBy('total', 'desc')
            ->take(5)
            ->pluck('total', 'tujuan');

        // Izin Keluar Hari Ini
        $izinKeluarHariIni = IzinMeninggalkanKelas::with([
            'siswa.masterSiswa.rombels' => function ($q) use ($tahunAktifId) {
                // Filter rombel siswa sesuai tahun aktif agar tidak ambil rombel lama
                if ($tahunAktifId) {
                    $q->where('tahun_pelajaran_id', $tahunAktifId);
                }
            },
            'siswa.masterSiswa.rombels.kelas',
            'jadwalPelajaran.mataPelajaran',
            'jadwalPelajaran.guru'
        ])
            ->whereDate('created_at', today())
            ->latest()
            ->get();


        // ==================================================
        //      DATA SUMMARY CARD (Contoh Dummy / Logic)
        // ==================================================
        // Anda bisa sesuaikan logika count ini sesuai kebutuhan
        $summary = [
            'today_submissions' => Perizinan::whereDate('created_at', today())->count(),
            'today_delta'       => Perizinan::whereDate('created_at', today())->count() - Perizinan::whereDate('created_at', today()->subDay())->count(),
            'approved'          => Perizinan::where('status', 'disetujui')->count(),
            'approve_rate'      => Perizinan::count() > 0 ? round((Perizinan::where('status', 'disetujui')->count() / Perizinan::count()) * 100) . '%' : '0%',
            'completed'         => IzinMeninggalkanKelas::where('status', 'selesai')->count(),
            'completed_week'    => IzinMeninggalkanKelas::where('status', 'selesai')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'rejected'          => Perizinan::where('status', 'ditolak')->count(),
            'rejected_trend'    => '+0%' // Logic trend bisa ditambahkan
        ];


        return view('pages.kesiswaan.dashboard.index', [
            'statusChartData' => ['labels' => $statusData->keys(), 'data' => $statusData->values()],
            'dailyChartData' => ['labels' => $dates->keys()->map(fn($date) => \Carbon\Carbon::parse($date)->format('d M')), 'data' => $dates->values()],
            'rombelChartData' => ['labels' => $rombelIzinTidakMasukChart->pluck('nama_kelas'), 'data' => $rombelIzinTidakMasukChart->pluck('total_izin')],
            'latestActivities' => $latestActivities,
            'topSiswaIzinKeluar' => $topSiswaIzinKeluar,
            'rombelIzinKeluarChartData' => ['labels' => $rombelIzinKeluarChart->pluck('nama_kelas'), 'data' => $rombelIzinKeluarChart->pluck('total_izin')],
            'tujuanIzinKeluarChartData' => ['labels' => $tujuanIzinKeluarChart->keys(), 'data' => $tujuanIzinKeluarChart->values()],
            'izinKeluarHariIni' => $izinKeluarHariIni,
            'summary' => $summary, // Kirim data summary
            'tahunAktif' => $tahunAktif // Kirim info tahun aktif untuk ditampilkan di view jika perlu
        ]);
    }
}
