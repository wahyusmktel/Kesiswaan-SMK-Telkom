<?php

namespace App\Http\Controllers\KaurSpmb;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\SpmbPesertaSeleksi;
use App\Models\SpmbPiketKelompok;
use App\Models\SpmbPiketTanggal;
use App\Models\SpmbWawancaraPertanyaan;
use App\Models\SpmbWawancaraSesi;
use App\Models\TahunPelajaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HasilWawancaraController extends Controller
{
    /**
     * Display a listing and analytics of interview results.
     */
    public function index(Request $request)
    {
        // 1. Resolve Academic Years
        $dbYears = SpmbPiketKelompok::distinct()->pluck('tahun_ajaran')->toArray();
        $tpYears = TahunPelajaran::distinct()->pluck('tahun')->toArray();
        $availableYears = array_unique(array_filter(array_merge($dbYears, $tpYears, ['2027/2028', '2026/2027', '2025/2026'])));
        rsort($availableYears);

        $selectedYear = $request->get('tahun_ajaran');
        if (! $selectedYear || ! in_array($selectedYear, $availableYears)) {
            $selectedYear = in_array('2027/2028', $availableYears) ? '2027/2028' : ($availableYears[0] ?? '2027/2028');
        }

        $tanggalId = $request->get('tanggal_id');
        $statusWawancara = $request->get('status'); // 'selesai', 'belum', 'sebagian'
        $hasilPredikat = $request->get('predikat'); // 'Sangat Baik', 'Baik', 'Cukup', 'Kurang Baik'
        $rekomendasi = $request->get('rekomendasi');
        $search = trim($request->get('search', ''));
        $perPage = (int) $request->get('per_page', 10);
        if ($perPage < 5 || $perPage > 100) {
            $perPage = 10;
        }

        $query = SpmbPesertaSeleksi::where('tahun_ajaran', $selectedYear)
            ->with(['tanggal.kelompok', 'wawancaraSesi.pewawancaraSiswa', 'wawancaraSesi.pewawancaraOrtu', 'user']);

        if (! empty($tanggalId)) {
            $query->where('spmb_piket_tanggal_id', $tanggalId);
        }

        if (! empty($statusWawancara)) {
            if ($statusWawancara === 'selesai') {
                $query->whereHas('wawancaraSesi', function ($q) {
                    $q->where('status_wawancara_siswa', 'Selesai')->where('status_wawancara_ortu', 'Selesai');
                });
            } elseif ($statusWawancara === 'sebagian') {
                $query->whereHas('wawancaraSesi', function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('status_wawancara_siswa', 'Selesai')->where('status_wawancara_ortu', '!=', 'Selesai');
                    })->orWhere(function ($sub) {
                        $sub->where('status_wawancara_ortu', 'Selesai')->where('status_wawancara_siswa', '!=', 'Selesai');
                    });
                });
            } elseif ($statusWawancara === 'belum') {
                $query->where(function ($q) {
                    $q->doesntHave('wawancaraSesi')
                        ->orWhereHas('wawancaraSesi', function ($sq) {
                            $sq->where('status_wawancara_siswa', '!=', 'Selesai')->where('status_wawancara_ortu', '!=', 'Selesai');
                        });
                });
            }
        }

        if (! empty($hasilPredikat)) {
            $query->whereHas('wawancaraSesi', function ($q) use ($hasilPredikat) {
                $q->where('hasil_wawancara_siswa', $hasilPredikat)
                    ->orWhere('hasil_wawancara_ortu', $hasilPredikat);
            });
        }

        if (! empty($rekomendasi)) {
            $query->whereHas('wawancaraSesi', fn ($q) => $q->where('rekomendasi_akhir', $rekomendasi));
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_pendaftaran', 'like', "%{$search}%")
                    ->orWhere('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nama_orang_tua', 'like', "%{$search}%")
                    ->orWhere('no_wa_siswa', 'like', "%{$search}%");
            });
        }

        $pesertas = $query->orderBy('nomor_pendaftaran', 'asc')
            ->paginate($perPage)
            ->withQueryString();

        // 2. Statistics
        $baseQuery = SpmbPesertaSeleksi::where('tahun_ajaran', $selectedYear);
        if (! empty($tanggalId)) {
            $baseQuery->where('spmb_piket_tanggal_id', $tanggalId);
        }

        $totalKandidat = (clone $baseQuery)->count();

        $sesiQuery = SpmbWawancaraSesi::where('tahun_ajaran', $selectedYear);
        if (! empty($tanggalId)) {
            $sesiQuery->where('spmb_piket_tanggal_id', $tanggalId);
        }

        $siswaSelesaiCount = (clone $sesiQuery)->where('status_wawancara_siswa', 'Selesai')->count();
        $ortuSelesaiCount = (clone $sesiQuery)->where('status_wawancara_ortu', 'Selesai')->count();
        $lengkapCount = (clone $sesiQuery)->where('status_wawancara_siswa', 'Selesai')->where('status_wawancara_ortu', 'Selesai')->count();

        $predikatSiswaCounts = [
            'Sangat Baik' => (clone $sesiQuery)->where('hasil_wawancara_siswa', 'Sangat Baik')->count(),
            'Baik' => (clone $sesiQuery)->where('hasil_wawancara_siswa', 'Baik')->count(),
            'Cukup' => (clone $sesiQuery)->where('hasil_wawancara_siswa', 'Cukup')->count(),
            'Kurang Baik' => (clone $sesiQuery)->where('hasil_wawancara_siswa', 'Kurang Baik')->count(),
        ];

        // 3. Tanggal Options
        $tanggalOptions = SpmbPiketTanggal::whereHas('kelompok', fn ($q) => $q->where('tahun_ajaran', $selectedYear))
            ->with('kelompok')
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('pages.kaur-spmb.hasil-wawancara.index', compact(
            'availableYears',
            'selectedYear',
            'tanggalId',
            'statusWawancara',
            'hasilPredikat',
            'rekomendasi',
            'search',
            'perPage',
            'pesertas',
            'totalKandidat',
            'siswaSelesaiCount',
            'ortuSelesaiCount',
            'lengkapCount',
            'predikatSiswaCounts',
            'tanggalOptions'
        ));
    }

    /**
     * Show Candidate Interview Transcript & Responses Detail.
     */
    public function show(SpmbPesertaSeleksi $pesertaSeleksi)
    {
        $pesertaSeleksi->loadMissing([
            'tanggal.kelompok',
            'wawancaraSesi.pewawancaraSiswa',
            'wawancaraSesi.pewawancaraOrtu',
            'wawancaraSesi.jawabans.pertanyaan',
            'user',
        ]);

        $sesi = $pesertaSeleksi->wawancaraSesi;

        $pertanyaansSiswa = SpmbWawancaraPertanyaan::where('kategori', 'siswa')->orderBy('nomor_urut')->get();
        $pertanyaansOrtu = SpmbWawancaraPertanyaan::where('kategori', 'orang_tua')->orderBy('nomor_urut')->get();

        $jawabansMap = $sesi ? $sesi->jawabans->keyBy('spmb_wawancara_pertanyaan_id') : collect();

        return view('pages.kaur-spmb.hasil-wawancara.show', compact(
            'pesertaSeleksi',
            'sesi',
            'pertanyaansSiswa',
            'pertanyaansOrtu',
            'jawabansMap'
        ));
    }

    /**
     * Update final recommendation status by Kaur SPMB.
     */
    public function updateRekomendasi(Request $request, SpmbPesertaSeleksi $pesertaSeleksi)
    {
        $validated = $request->validate([
            'rekomendasi_akhir' => 'required|in:Diterima,Diterima Bersyarat,Dipertimbangkan,Ditolak,Belum Diputuskan',
            'catatan_akhir' => 'nullable|string|max:1000',
        ]);

        $sesi = SpmbWawancaraSesi::firstOrCreate(
            ['spmb_peserta_seleksi_id' => $pesertaSeleksi->id],
            [
                'spmb_piket_tanggal_id' => $pesertaSeleksi->spmb_piket_tanggal_id,
                'tahun_ajaran' => $pesertaSeleksi->tahun_ajaran,
            ]
        );

        $sesi->update($validated);

        return redirect()->back()->with('success', "Keputusan rekomendasi akhir untuk {$pesertaSeleksi->nama_lengkap} berhasil diperbarui: {$validated['rekomendasi_akhir']}.");
    }

    /**
     * Print View for Interview Assessment Sheet (A4 Portrait, Kop Edge-to-Edge).
     */
    public function print(SpmbPesertaSeleksi $pesertaSeleksi)
    {
        $pesertaSeleksi->loadMissing([
            'tanggal.kelompok',
            'wawancaraSesi.pewawancaraSiswa',
            'wawancaraSesi.pewawancaraOrtu',
            'wawancaraSesi.jawabans.pertanyaan',
            'user',
        ]);

        $sesi = $pesertaSeleksi->wawancaraSesi;
        $pertanyaansSiswa = SpmbWawancaraPertanyaan::where('kategori', 'siswa')->orderBy('nomor_urut')->get();
        $pertanyaansOrtu = SpmbWawancaraPertanyaan::where('kategori', 'orang_tua')->orderBy('nomor_urut')->get();
        $jawabansMap = $sesi ? $sesi->jawabans->keyBy('spmb_wawancara_pertanyaan_id') : collect();

        // Load Kop Surat UKK
        $setting = AppSetting::first();
        $kopBase64 = null;
        $kopPath = $setting?->kop_surat_ukk;
        if ($kopPath) {
            if (Storage::disk('public')->exists($kopPath)) {
                $kopData = Storage::disk('public')->get($kopPath);
                $kopMime = Storage::disk('public')->mimeType($kopPath) ?: 'image/png';
                $kopBase64 = 'data:' . $kopMime . ';base64,' . base64_encode($kopData);
            } elseif (file_exists(storage_path('app/public/' . $kopPath))) {
                $fullPath = storage_path('app/public/' . $kopPath);
                $kopData = file_get_contents($fullPath);
                $kopMime = mime_content_type($fullPath) ?: 'image/png';
                $kopBase64 = 'data:' . $kopMime . ';base64,' . base64_encode($kopData);
            } elseif (file_exists(public_path('storage/' . $kopPath))) {
                $fullPath = public_path('storage/' . $kopPath);
                $kopData = file_get_contents($fullPath);
                $kopMime = mime_content_type($fullPath) ?: 'image/png';
                $kopBase64 = 'data:' . $kopMime . ';base64,' . base64_encode($kopData);
            }
        }

        $kaurUser = User::role('Kaur SPMB')->first() ?? Auth::user();
        $namaKaur = $kaurUser?->name ?? 'Wahyu Rahmat Hidayat, S.Kom., Gr.';

        $hariTanggalStr = $pesertaSeleksi->tanggal
            ? Carbon::parse($pesertaSeleksi->tanggal->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y')
            : Carbon::today()->locale('id')->isoFormat('dddd, D MMMM Y');

        return view('pages.kaur-spmb.hasil-wawancara.print', compact(
            'pesertaSeleksi',
            'sesi',
            'pertanyaansSiswa',
            'pertanyaansOrtu',
            'jawabansMap',
            'kopBase64',
            'namaKaur',
            'hariTanggalStr'
        ));
    }
}
