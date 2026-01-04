<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Keterlambatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RiwayatKeterlambatanController extends Controller
{
    public function index()
    {
        $siswa = Auth::user()->masterSiswa;
        
        if (!$siswa) {
            return redirect()->route('siswa.dashboard.index')->with('error', 'Data siswa tidak ditemukan.');
        }

        $keterlambatans = $siswa->keterlambatans()
            ->with(['security', 'guruPiket'])
            ->latest('waktu_dicatat_security')
            ->paginate(10);

        return view('pages.siswa.riwayat-keterlambatan.index', compact('keterlambatans'));
    }

    public function printPdf(Keterlambatan $keterlambatan)
    {
        // Pastikan ini adalah data milik siswa yang sedang login
        if ($keterlambatan->master_siswa_id !== Auth::user()->masterSiswa->id) {
            abort(403);
        }

        $keterlambatan->load(['siswa.user', 'siswa.rombels.kelas', 'security', 'guruPiket', 'jadwalPelajaran.mataPelajaran', 'jadwalPelajaran.guru']);
        
        // Generate QR Codes required by the template (Logic same as MonitoringKeterlambatanController)
        $publicUrl = route('verifikasi.surat-terlambat', $keterlambatan->uuid);
        $publicQrCode = 'data:image/svg+xml;base64,' . base64_encode(QrCode::format('svg')->size(60)->generate($publicUrl));

        $guruKelasUrl = route('guru-kelas.verifikasi-terlambat.scan', $keterlambatan->uuid);
        $guruKelasQrCode = 'data:image/svg+xml;base64,' . base64_encode(QrCode::format('svg')->size(60)->generate($guruKelasUrl));

        // Use the existing PDF template
        $pdf = Pdf::loadView('pdf.surat-izin-masuk-kelas', compact('keterlambatan', 'publicQrCode', 'guruKelasQrCode'));
        return $pdf->download('surat-izin-masuk-kelas.pdf');
    }
}
