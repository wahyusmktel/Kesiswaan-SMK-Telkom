<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\TahunPelajaran;
use App\Models\TranscriptConfig;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Picqer\Barcode\BarcodeGeneratorPNG;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class KartuPelajarController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $siswa = $user->masterSiswa;

        // Ensure the user is a student and has master record
        if (!$siswa) {
            return redirect()->route('dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }

        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        // Load necessary relations
        $siswa->load([
            'dapodik',
            'studentRegistration',
            'rombels' => function ($q) use ($tahunAktif) {
                $q->where('tahun_pelajaran_id', $tahunAktif?->id)->with('kelas');
            }
        ]);

        // Get rombel (class) info
        $rombel = $siswa->rombels->first() ?? $siswa->rombels()->with('kelas')->first();

        // Get school settings
        $settings = AppSetting::first();

        // Generate barcode - Code 128 (wider and taller for better scanner readability)
        $generator = new BarcodeGeneratorPNG();
        $barcode = base64_encode($generator->getBarcode($siswa->nis, $generator::TYPE_CODE_128, 3, 80));

        // Get Kepala Sekolah data
        $kepsekData = $this->getKepalaSekolahData();

        // Generate QR Code verification for kepsek
        $qrCodeKepsek = $this->generateQrCodeBase64(route('verifikasi.kartu', $siswa->nis));

        return view('pages.siswa.kartu-pelajar.index', compact(
            'user',
            'siswa',
            'rombel',
            'settings',
            'barcode',
            'kepsekData',
            'qrCodeKepsek'
        ));
    }

    public function cetak(Request $request)
    {
        $user = Auth::user();
        $siswa = $user->masterSiswa;

        if (!$siswa) {
            return redirect()->route('dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }

        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        $siswa->load([
            'dapodik',
            'studentRegistration',
            'rombels' => function ($q) use ($tahunAktif) {
                $q->where('tahun_pelajaran_id', $tahunAktif?->id)->with('kelas');
            }
        ]);

        $generator = new BarcodeGeneratorPNG();
        $barcode = base64_encode($generator->getBarcode($siswa->nis, $generator::TYPE_CODE_128, 3, 80));

        $design = 'standar_ts';
        $kepsekData = $this->getKepalaSekolahData();
        $qrCodeKepsek = $this->generateQrCodeBase64(route('verifikasi.kartu', $siswa->nis));

        return view('pages.kesiswaan.kartu-akses.cetak', compact(
            'siswa',
            'barcode',
            'design',
            'kepsekData',
            'qrCodeKepsek'
        ));
    }

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
