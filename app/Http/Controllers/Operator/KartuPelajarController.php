<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\MasterSiswa;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Services\KartuPelajarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class KartuPelajarController extends Controller
{
    protected KartuPelajarService $kartuService;

    public function __construct(KartuPelajarService $kartuService)
    {
        $this->kartuService = $kartuService;
    }

    /**
     * Halaman Utama Pengelolaan & Cetak Kartu Pelajar
     */
    public function index(Request $request)
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        $rombels = Rombel::with('kelas')
            ->where('tahun_pelajaran_id', $tahunAktif?->id)
            ->get()
            ->sortBy(fn($r) => $r->kelas->nama_kelas ?? '');

        $query = MasterSiswa::query()->active()->with(['rombels' => function ($q) use ($tahunAktif) {
            $q->where('tahun_pelajaran_id', $tahunAktif?->id)->with('kelas');
        }]);

        // Filter Rombel / Kelas
        if ($request->filled('rombel_id')) {
            $query->whereHas('rombels', function ($q) use ($request) {
                $q->where('rombels.id', $request->rombel_id);
            });
        }

        // Pencarian Nama / NIS
        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        // Filter status foto
        if ($request->filled('status_foto')) {
            if ($request->status_foto === 'ada') {
                $query->whereNotNull('foto');
            } elseif ($request->status_foto === 'belum') {
                $query->whereNull('foto');
            }
        }

        $selectedRombel = $request->filled('rombel_id') ? $rombels->firstWhere('id', (int) $request->rombel_id) : null;

        $siswa = $query->orderBy('nama_lengkap')->paginate(15)->withQueryString();

        $kepsekData = $this->kartuService->getKepalaSekolahData();

        return view('pages.operator.kartu-pelajar.index', compact(
            'rombels',
            'siswa',
            'selectedRombel',
            'tahunAktif',
            'kepsekData'
        ));
    }

    private function resolveSiswa($siswa): MasterSiswa
    {
        if (!$siswa instanceof MasterSiswa) {
            return MasterSiswa::findOrFail($siswa);
        }
        if (!$siswa->exists) {
            $id = request()->route('siswa') ?? request()->route('id');
            return MasterSiswa::findOrFail($id);
        }
        return $siswa;
    }

    /**
     * API / View Preview Kartu Pelajar Satuan
     */
    public function preview($siswa)
    {
        $siswa = $this->resolveSiswa($siswa);
        $siswa->load(['rombels.kelas']);
        $barcodeBase64 = base64_encode($this->kartuService->generateBarcodePng($siswa->nis));
        $kepsekData = $this->kartuService->getKepalaSekolahData();

        return view('pages.operator.kartu-pelajar.preview-modal', compact('siswa', 'barcodeBase64', 'kepsekData'));
    }

    /**
     * Download kartu pelajar dalam format file .jpg
     */
    public function downloadJpg($siswa)
    {
        $siswa = $this->resolveSiswa($siswa);
        $siswa->load(['rombels.kelas']);
        $namaKelas = $siswa->rombels->first()?->kelas?->nama_kelas ?? 'Tanpa Kelas';

        $rawFileName = "{$namaKelas}_{$siswa->nama_lengkap}_{$siswa->nis}.jpg";
        $cleanFileName = preg_replace('/[\\\\\/:*?"<>|]/', '-', $rawFileName);

        $tempPath = storage_path('app/temp/' . Str::random(10) . '_' . $cleanFileName);
        $this->kartuService->saveCardAsJpg($siswa, $tempPath, $namaKelas);

        return response()->download($tempPath, $cleanFileName)->deleteFileAfterSend(true);
    }

    /**
     * Cetak kartu pelajar satuan (Print Preview HTML 8.6 x 5.4 cm)
     */
    public function cetak($siswa)
    {
        $siswa = $this->resolveSiswa($siswa);
        $siswa->load(['rombels.kelas']);
        $barcodeBase64 = base64_encode($this->kartuService->generateBarcodePng($siswa->nis));
        $kepsekData = $this->kartuService->getKepalaSekolahData();

        return view('pages.operator.kartu-pelajar.cetak', compact('siswa', 'barcodeBase64', 'kepsekData'));
    }

    /**
     * Cetak kartu pelajar massal per kelas / rombel
     */
    public function cetakKelas(Request $request)
    {
        $request->validate([
            'rombel_id' => 'required|exists:rombels,id',
        ]);

        $rombel = Rombel::with('kelas')->findOrFail($request->rombel_id);

        $siswaList = MasterSiswa::active()
            ->whereHas('rombels', fn($q) => $q->where('rombels.id', $rombel->id))
            ->with(['rombels.kelas'])
            ->orderBy('nama_lengkap')
            ->get();

        if ($siswaList->isEmpty()) {
            toast('Tidak ada siswa aktif di kelas yang dipilih.', 'warning');
            return back();
        }

        $barcodes = [];
        foreach ($siswaList as $s) {
            $barcodes[$s->id] = base64_encode($this->kartuService->generateBarcodePng($s->nis));
        }

        $kepsekData = $this->kartuService->getKepalaSekolahData();

        return view('pages.operator.kartu-pelajar.cetak-kelas', compact('siswaList', 'rombel', 'barcodes', 'kepsekData'));
    }

    /**
     * Export masal dalam bentuk file .zip berisi file .jpg per siswa
     * Nama file: {nama kelas}_{nama siswa}_{nis}.jpg
     */
    public function exportZip(Request $request)
    {
        $rombel = null;
        $query = MasterSiswa::active()->with(['rombels.kelas']);

        if ($request->filled('rombel_id')) {
            $rombel = Rombel::with('kelas')->findOrFail($request->rombel_id);
            $query->whereHas('rombels', fn($q) => $q->where('rombels.id', $rombel->id));
        }

        $siswaList = $query->orderBy('nama_lengkap')->get();

        if ($siswaList->isEmpty()) {
            toast('Tidak ada data siswa untuk diekspor.', 'warning');
            return back();
        }

        try {
            $zipPath = $this->kartuService->generateZipArchive($siswaList, $rombel);
            $zipName = basename($zipPath);

            return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            toast('Gagal membuat file ZIP: ' . $e->getMessage(), 'error');
            return back();
        }
    }

    /**
     * Upload masal foto siswa berdasarkan NIS (ZIP atau multi-file image)
     */
    public function uploadFotoMasal(Request $request)
    {
        $request->validate([
            'file_zip' => 'nullable|file|mimes:zip|max:51200', // max 50MB
            'photos'   => 'nullable|array',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120', // max 5MB per image
        ]);

        if (!$request->hasFile('file_zip') && !$request->hasFile('photos')) {
            toast('Silakan pilih file ZIP atau beberapa file foto yang akan diunggah.', 'warning');
            return back();
        }

        try {
            if ($request->hasFile('file_zip')) {
                $result = $this->kartuService->processMassPhotoUpload($request->file('file_zip'));
            } else {
                $result = $this->kartuService->processMassPhotoUpload($request->file('photos'));
            }

            $msg = "Berhasil mengunggah {$result['matched']} foto siswa sesuai NIS.";
            if (!empty($result['unmatched'])) {
                $unmatchedCount = count($result['unmatched']);
                $sample = implode(', ', array_slice($result['unmatched'], 0, 5));
                if ($unmatchedCount > 5) $sample .= '...';
                $msg .= " ({$unmatchedCount} file foto tidak ditemukan NIS-nya di sistem: {$sample})";
            }

            toast($msg, $result['matched'] > 0 ? 'success' : 'warning');
            return back();
        } catch (\Exception $e) {
            toast('Gagal memproses upload foto: ' . $e->getMessage(), 'error');
            return back();
        }
    }
}
