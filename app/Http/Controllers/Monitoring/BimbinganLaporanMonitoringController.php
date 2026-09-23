<?php

namespace App\Http\Controllers\Monitoring;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MasterGuru;
use App\Models\PrakerinBimbinganLaporan;
use App\Models\PrakerinIndustri;
use App\Models\PrakerinPenempatan;
use App\Models\Rombel;
use App\Services\PrakerinBeritaAcaraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class BimbinganLaporanMonitoringController extends Controller
{
    protected PrakerinBeritaAcaraService $pdfService;

    public function __construct(PrakerinBeritaAcaraService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Halaman Monitoring Aktivitas Bimbingan Laporan Prakerin.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $activeRole = session('active_role') ?: $user->getRoleNames()->first();
        $isWaliKelas = $activeRole === 'Wali Kelas' || $user->hasRole('Wali Kelas');

        $query = PrakerinPenempatan::with([
            'siswa.rombels.kelas',
            'industri',
            'guruPembimbing',
            'rombelPkl',
            'bimbinganLaporan.tahaps.anotasis',
            'bimbinganLaporan.accBy',
        ])
        ->whereNotNull('prakerin_rombel_id')
        ->where('status', 'aktif');

        // Jika Wali Kelas, batasi hanya menampilkan siswa yang berada di kelas perwaliannya
        $kelasWali = null;
        if ($isWaliKelas && ! $user->hasRole('Super Admin') && ! $user->hasRole('Hubin Sinergi UP dan Alumni')) {
            $rombelPerwalian = Rombel::with('kelas')->where('wali_kelas_id', $user->id)->first();
            if ($rombelPerwalian) {
                $kelasWali = $rombelPerwalian->kelas;
                $query->whereHas('siswa.rombels', function ($q) use ($rombelPerwalian) {
                    $q->where('rombels.id', $rombelPerwalian->id);
                });
            }
        }

        // Pencarian Siswa / Industri / Guru / Judul
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->whereHas('siswa', fn ($sub) => $sub->where('nama_lengkap', 'like', "%{$s}%")->orWhere('nis', 'like', "%{$s}%"))
                    ->orWhereHas('industri', fn ($sub) => $sub->where('nama_industri', 'like', "%{$s}%"))
                    ->orWhereHas('guruPembimbing', fn ($sub) => $sub->where('nama_lengkap', 'like', "%{$s}%"))
                    ->orWhereHas('bimbinganLaporan', fn ($sub) => $sub->where('judul_laporan', 'like', "%{$s}%"));
            });
        }

        // Filter Kelas (Khusus non-wali kelas atau jika ada pilihan)
        if ($request->filled('kelas_id') && ! $isWaliKelas) {
            $query->whereHas('siswa.rombels', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        // Filter Status Bimbingan
        if ($request->filled('status')) {
            if ($request->status === 'belum_judul') {
                $query->where(function ($q) {
                    $q->doesntHave('bimbinganLaporan')
                        ->orWhereHas('bimbinganLaporan', fn ($sub) => $sub->whereIn('status_judul', ['draft', 'diajukan', 'ditolak']));
                });
            } elseif ($request->status === 'proses') {
                $query->whereHas('bimbinganLaporan', fn ($q) => $q->where('status_judul', 'disetujui')->where('status_laporan', '!=', 'selesai_acc'));
            } elseif ($request->status === 'selesai') {
                $query->whereHas('bimbinganLaporan', fn ($q) => $q->where('status_laporan', 'selesai_acc'));
            }
        }

        $penempatans = $query->latest()->paginate(15)->withQueryString();

        // Opsi Filter
        $kelasList = Kelas::where('nama_kelas', 'like', 'XII%')
            ->orWhere('nama_kelas', 'like', '12%')
            ->orderBy('nama_kelas')
            ->get();

        return view('pages.monitoring.bimbingan-laporan.index', compact(
            'penempatans',
            'kelasList',
            'isWaliKelas',
            'kelasWali'
        ));
    }

    /**
     * Lihat Bentuk Review Laporan Terakhir Siswa yang Sedang dalam Proses Bimbingan.
     */
    public function lihatReviewTerakhir(PrakerinBimbinganLaporan $laporan)
    {
        $tahap = $laporan->tahap_terakhir;

        if (! $tahap || ! $tahap->file_pdf_path) {
            Alert::info('Belum Ada Dokumen', 'Siswa ini belum mengunggah dokumen laporan PDF.');
            return back();
        }

        $tahap->load(['anotasis.user', 'reviewer']);
        $laporan->load(['penempatan.siswa', 'penempatan.industri', 'penempatan.guruPembimbing']);
        $anotasis = $tahap->anotasis;

        return view('pages.siswa.bimbingan-laporan.viewer', compact('tahap', 'laporan', 'anotasis'));
    }

    /**
     * Unduh Rekap Log Riwayat Bimbingan Siswa (PDF).
     */
    public function unduhRiwayatPdf(PrakerinBimbinganLaporan $laporan)
    {
        $pdf = $this->pdfService->generateRiwayatBimbinganPdf($laporan);

        return $pdf->stream('Rekap-Riwayat-Bimbingan-Prakerin-' . ($laporan->penempatan->siswa?->nis ?? 'siswa') . '.pdf');
    }
}
