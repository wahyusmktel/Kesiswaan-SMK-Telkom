<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\MasterSiswa;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Models\TranscriptConfig;
use App\Models\User;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class KartuAksesController extends Controller
{
    /**
     * Display the Stella Access Card generator page
     */
    public function index(Request $request)
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        
        $rombels = Rombel::with('kelas')
            ->where('tahun_pelajaran_id', $tahunAktif?->id)
            ->get()
            ->sortBy(fn($r) => $r->kelas->nama_kelas ?? '');

        $siswaQuery = MasterSiswa::query()
            ->whereHas('rombels', function ($q) use ($tahunAktif) {
                $q->where('tahun_pelajaran_id', $tahunAktif?->id);
            })
            ->with(['rombels' => function ($q) use ($tahunAktif) {
                $q->where('tahun_pelajaran_id', $tahunAktif?->id)->with('kelas');
            }]);

        // Filter by rombel if provided
        if ($request->filled('rombel_id')) {
            $siswaQuery->whereHas('rombels', function ($q) use ($request) {
                $q->where('rombels.id', $request->rombel_id);
            });
        }

        // Search by name or NIS
        if ($request->filled('search')) {
            $siswaQuery->where(function ($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('nis', 'like', '%' . $request->search . '%');
            });
        }

        $siswa = $siswaQuery->orderBy('nama_lengkap')->paginate(20)->withQueryString();

        return view('pages.kesiswaan.kartu-akses.index', compact('rombels', 'siswa'));
    }

    /**
     * Show single card preview
     */
    public function show(MasterSiswa $siswa)
    {
        $siswa->load([
            'dapodik',
            'studentRegistration',
            'rombels' => function ($q) {
                $tahunAktif = TahunPelajaran::where('is_active', true)->first();
                $q->where('tahun_pelajaran_id', $tahunAktif?->id)->with('kelas');
            }
        ]);

        // Generate barcode - wider (3) and taller (80) for better scanner readability
        $generator = new BarcodeGeneratorPNG();
        $barcode = base64_encode($generator->getBarcode($siswa->nis, $generator::TYPE_CODE_128, 3, 80));

        $design = request('design', 'standard');
        $kepsekData = $this->getKepalaSekolahData();
        $qrCodeKepsek = $this->generateQrCodeBase64(route('verifikasi.kartu', $siswa->nis));

        return view('pages.kesiswaan.kartu-akses.show', compact('siswa', 'barcode', 'design', 'kepsekData', 'qrCodeKepsek'));
    }

    /**
     * Print single card
     */
    public function cetak(MasterSiswa $siswa)
    {
        $siswa->load([
            'dapodik',
            'studentRegistration',
            'rombels' => function ($q) {
                $tahunAktif = TahunPelajaran::where('is_active', true)->first();
                $q->where('tahun_pelajaran_id', $tahunAktif?->id)->with('kelas');
            }
        ]);

        // Generate barcode - wider (3) and taller (80) for better scanner readability
        $generator = new BarcodeGeneratorPNG();
        $barcode = base64_encode($generator->getBarcode($siswa->nis, $generator::TYPE_CODE_128, 3, 80));

        $design = request('design', 'standard');
        $kepsekData = $this->getKepalaSekolahData();
        $qrCodeKepsek = $this->generateQrCodeBase64(route('verifikasi.kartu', $siswa->nis));

        return view('pages.kesiswaan.kartu-akses.cetak', compact('siswa', 'barcode', 'design', 'kepsekData', 'qrCodeKepsek'));
    }

    /**
     * Mass print cards filtered by class
     */
    public function cetakMasal(Request $request)
    {
        $request->validate([
            'rombel_id' => 'required|exists:rombels,id',
        ]);

        $rombel = Rombel::with('kelas')->findOrFail($request->rombel_id);
        
        $siswaList = MasterSiswa::whereHas('rombels', function ($q) use ($request) {
            $q->where('rombels.id', $request->rombel_id);
        })
        ->with([
            'dapodik',
            'studentRegistration',
            'rombels' => function ($q) use ($request) {
                $q->where('rombels.id', $request->rombel_id)->with('kelas');
            }
        ])
        ->orderBy('nama_lengkap')
        ->get();

        // Generate barcodes and QR Codes for all students
        $generator = new BarcodeGeneratorPNG();
        $barcodes = [];
        $qrCodesKepsek = [];
        $design = $request->design ?? 'standard';
        $kepsekData = $this->getKepalaSekolahData();

        foreach ($siswaList as $siswa) {
            $barcodes[$siswa->id] = base64_encode($generator->getBarcode($siswa->nis, $generator::TYPE_CODE_128, 3, 80));
            $qrCodesKepsek[$siswa->id] = $this->generateQrCodeBase64(route('verifikasi.kartu', $siswa->nis));
        }

        return view('pages.kesiswaan.kartu-akses.cetak-masal', compact('siswaList', 'barcodes', 'rombel', 'design', 'kepsekData', 'qrCodesKepsek'));
    }

    /**
     * Get data Kepala Sekolah and digital signature readiness
     */
    private function getKepalaSekolahData(): array
    {
        $kepsek = null;
        try {
            $kepsek = User::whereHas('roles', function ($q) {
                $q->where('name', 'Kepala Sekolah');
            })->with(['digitalSignature', 'masterGuru.dapodikGuru'])->first();
        } catch (\Throwable $e) {
            $kepsek = null;
        }

        $transcriptConfig = TranscriptConfig::first();

        $nama = $kepsek?->name ?? $transcriptConfig?->principal_name ?? 'Kepala SMK Telkom Lampung';
        $nip = $kepsek?->nip ?? $kepsek?->masterGuru?->dapodikGuru?->nip ?? $transcriptConfig?->principal_nip ?? '-';
        $hasDigitalSignature = (bool) ($kepsek && $kepsek->digitalSignature && $kepsek->digitalSignature->isReady());

        return [
            'user'          => $kepsek,
            'nama'          => $nama,
            'nip'           => $nip,
            'has_signature' => $hasDigitalSignature,
        ];
    }

    /**
     * Generate vector SVG QR Code encoded as Base64 data URL
     */
    private function generateQrCodeBase64(string $url): string
    {
        $svg = QrCode::format('svg')
            ->size(120)
            ->margin(0)
            ->errorCorrection('M')
            ->generate($url);

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
