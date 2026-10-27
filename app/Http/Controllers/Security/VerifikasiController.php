<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\IzinMeninggalkanKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class VerifikasiController extends Controller
{
    public function index()
    {
        // Ambil izin yang perlu diverifikasi (baru disetujui piket)
        // dan yang sedang berada di luar (sudah diverifikasi keluar)
        $daftarIzin = IzinMeninggalkanKelas::with(['siswa.masterSiswa.rombels.kelas'])
            ->whereIn('status', ['disetujui_guru_piket', 'diverifikasi_security'])
            ->latest()
            ->paginate(20);

        return view('pages.security.verifikasi.index', compact('daftarIzin'));
    }

    /**
     * Memverifikasi siswa saat akan keluar gerbang.
     */
    public function verifyKeluar(IzinMeninggalkanKelas $izin)
    {
        try {
            $izin->update([
                'status' => 'diverifikasi_security',
                'security_verification_id' => Auth::id(),
                'security_verified_at' => now(),
                'waktu_keluar_sebenarnya' => now(),
            ]);
            toast('Siswa telah diverifikasi keluar.', 'success');
        } catch (\Exception $e) {
            Log::error('Error verifying leave by security: ' . $e->getMessage());
            toast('Gagal melakukan verifikasi.', 'error');
        }
        return back();
    }

    /**
     * Memverifikasi siswa saat kembali ke sekolah.
     */
    public function verifyKembali(IzinMeninggalkanKelas $izin)
    {
        try {
            $waktuKembali = now();
            $estimasiKembali = $izin->estimasi_kembali;

            // Tentukan status akhir: selesai atau terlambat
            $statusAkhir = $waktuKembali->gt($estimasiKembali) ? 'terlambat' : 'selesai';

            $izin->update([
                'status' => $statusAkhir,
                'waktu_kembali_sebenarnya' => $waktuKembali,
            ]);
            toast('Siswa telah diverifikasi kembali ke sekolah.', 'success');
        } catch (\Exception $e) {
            Log::error('Error verifying return by security: ' . $e->getMessage());
            toast('Gagal melakukan verifikasi.', 'error');
        }
        return back();
    }

    /**
     * Mencetak surat izin yang sudah diverifikasi.
     */
    public function printPdf(IzinMeninggalkanKelas $izin)
    {
        if (!in_array($izin->status, ['diverifikasi_security', 'selesai', 'terlambat'])) {
            toast('Surat izin ini belum bisa dicetak dari sisi security.', 'error');
            return back();
        }

        $izin->load(['siswa.masterSiswa.rombels.kelas', 'guruKelasApprover', 'guruPiketApprover', 'securityVerifier', 'jadwalPelajaran.mataPelajaran', 'jadwalPelajaran.guru']);

        // 1. URL untuk verifikasi publik
        $publicUrl = route('verifikasi.surat', $izin->uuid);
        $publicQrCode = QrCode::format('svg')->size(70)->generate($publicUrl);
        $publicQrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($publicQrCode);

        // 2. URL untuk aksi internal security
        $securityUrl = route('security.verifikasi.show-scan', $izin->uuid);
        $securityQrCode = QrCode::format('svg')->size(70)->generate($securityUrl);
        $securityQrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($securityQrCode);

        // 3. Barcode scanner 1D untuk PC security gerbang
        $barcodeGen = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $nis = $izin->siswa->masterSiswa?->nis;
        $securityBarcodeBase64 = $nis ? 'data:image/png;base64,' . base64_encode($barcodeGen->getBarcode($nis, $barcodeGen::TYPE_CODE_128, 2, 45)) : null;

        $pdf = Pdf::loadView('pdf.surat-izin-keluar', [
            'izin'                  => $izin,
            'publicQrCodeBase64'    => $publicQrCodeBase64,
            'securityQrCodeBase64'  => $securityQrCodeBase64,
            'securityBarcodeBase64' => $securityBarcodeBase64,
        ]);

        return $pdf->stream('surat-izin-terverifikasi-' . $izin->siswa->name . '.pdf');
    }

    /**
     * Menampilkan halaman untuk memindai QR Code.
     */
    public function scanQr()
    {
        return view('pages.security.verifikasi.scan');
    }

    /**
     * Menampilkan halaman aksi setelah QR Code dipindai.
     */
    public function showScanResult(string $uuid)
    {
        $izin = IzinMeninggalkanKelas::with(['siswa.masterSiswa.rombels.kelas'])
            ->where('uuid', $uuid)
            ->first();

        // Jika izin tidak ditemukan
        if (!$izin) {
            toast('Data izin tidak ditemukan atau tidak valid.', 'error');
            return redirect()->route('security.verifikasi.scan');
        }

        // Jika izin sudah selesai atau ditolak
        if (in_array($izin->status, ['selesai', 'terlambat', 'ditolak'])) {
            toast('Status izin ini sudah final dan tidak memerlukan aksi.', 'info')->autoClose(5000);
        }

        return view('pages.security.verifikasi.show-scan-result', compact('izin'));
    }

    /**
     * Memproses aksi berdasarkan status izin saat ini via scan QR.
     */
    public function processScanAction(string $uuid)
    {
        $izin = IzinMeninggalkanKelas::where('uuid', $uuid)->first();

        if (!$izin) {
            toast('Data izin tidak ditemukan.', 'error');
            return redirect()->route('security.verifikasi.scan');
        }

        try {
            // KONDISI 1: Jika izin siap untuk diverifikasi KELUAR
            if ($izin->status === 'disetujui_guru_piket') {
                $izin->update([
                    'status' => 'diverifikasi_security',
                    'security_verification_id' => Auth::id(),
                    'security_verified_at' => now(),
                    'waktu_keluar_sebenarnya' => now(),
                ]);

                toast('Verifikasi KELUAR berhasil! Mencetak surat izin...', 'success')->autoClose(4000);
                return redirect()->route('security.verifikasi.print', $izin->id);
            }
            // KONDISI 2: Jika izin siap untuk diverifikasi KEMBALI
            elseif ($izin->status === 'diverifikasi_security') {
                $waktuKembali = now();
                $estimasiKembali = \Carbon\Carbon::parse($izin->estimasi_kembali);
                $statusAkhir = $waktuKembali->gt($estimasiKembali) ? 'terlambat' : 'selesai';

                $izin->update([
                    'status' => $statusAkhir,
                    'waktu_kembali_sebenarnya' => $waktuKembali,
                ]);

                toast('Verifikasi KEMBALI berhasil dicatat.', 'success');
                return redirect()->route('security.verifikasi.index');
            }
            // KONDISI 3: Jika status sudah final atau lainnya
            else {
                toast('Status izin ini sudah final. Menampilkan detail...', 'info')->autoClose(5000);
                return redirect()->route('security.verifikasi.show-scan', $izin->uuid);
            }
        } catch (\Exception $e) {
            Log::error('Error processing scan action by security: ' . $e->getMessage());
            toast('Gagal memproses aksi otomatis.', 'error');
            return redirect()->route('security.verifikasi.scan');
        }
    }

    /**
     * Menampilkan halaman riwayat verifikasi.
     */
    public function riwayat(Request $request)
    {
        $query = IzinMeninggalkanKelas::with(['siswa.masterSiswa.rombels.kelas', 'securityVerifier'])
            ->whereNotNull('security_verification_id'); // Hanya tampilkan yang pernah diverifikasi security

        // Filter berdasarkan pencarian nama siswa
        if ($request->filled('search')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // Filter berdasarkan rentang tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereDate('security_verified_at', '>=', $request->start_date)
                ->whereDate('security_verified_at', '<=', $request->end_date);
        }

        $riwayatIzin = $query->latest('security_verified_at')->paginate(20);

        return view('pages.security.verifikasi.riwayat', compact('riwayatIzin'));
    }
}
