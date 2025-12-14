<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Dispensasi; // Pastikan Model sesuai dengan file kamu
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PersetujuanDispensasiController extends Controller
{
    /**
     * Menampilkan daftar pengajuan dispensasi dengan Filter & Search.
     */
    public function index(Request $request)
    {
        // withCount('siswa') ditambahkan agar kolom "Jumlah Siswa" di tabel bisa muncul tanpa error N+1
        $query = Dispensasi::with(['diajukanOleh', 'siswa'])
            ->withCount('siswa');

        // 1. Logika Pencarian (Search Bar)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kegiatan', 'like', "%{$search}%")
                    ->orWhereHas('diajukanOleh', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // 2. Logika Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // withQueryString() agar saat pindah halaman (pagination), filter pencarian tidak hilang
        $daftarDispensasi = $query->latest()->paginate(10)->withQueryString();

        return view('pages.kesiswaan.persetujuan-dispensasi.index', compact('daftarDispensasi'));
    }

    /**
     * Menampilkan detail pengajuan untuk ditinjau.
     */
    public function show(Dispensasi $dispensasi)
    {
        // Eager load relasi yang dibutuhkan di halaman Detail
        $dispensasi->load(['diajukanOleh', 'siswa.rombels.kelas']);

        return view('pages.kesiswaan.persetujuan-dispensasi.show', compact('dispensasi'));
    }

    /**
     * Menyetujui pengajuan.
     */
    public function approve(Dispensasi $dispensasi)
    {
        try {
            $dispensasi->update([
                'status' => 'disetujui',
                'disetujui_oleh_id' => Auth::id(),
            ]);

            toast('Dispensasi berhasil disetujui.', 'success');

            // Redirect kembali ke halaman SHOW agar user bisa langsung lihat tombol cetak
            return redirect()->route('kesiswaan.persetujuan-dispensasi.show', $dispensasi->id);
        } catch (\Exception $e) {
            Log::error('Error approving dispensation: ' . $e->getMessage());
            toast('Gagal menyetujui dispensasi.', 'error');
            return back();
        }
    }

    /**
     * Menolak pengajuan.
     */
    public function reject(Request $request, Dispensasi $dispensasi)
    {
        $request->validate(['alasan_penolakan' => 'required|string|min:5']);

        try {
            $dispensasi->update([
                'status' => 'ditolak',
                'disetujui_oleh_id' => Auth::id(),
                'alasan_penolakan' => $request->alasan_penolakan,
            ]);

            toast('Dispensasi telah ditolak.', 'info');

            // Redirect kembali ke halaman SHOW agar user melihat status terbarunya
            return redirect()->route('kesiswaan.persetujuan-dispensasi.show', $dispensasi->id);
        } catch (\Exception $e) {
            Log::error('Error rejecting dispensation: ' . $e->getMessage());
            toast('Gagal menolak dispensasi.', 'error');
            return back();
        }
    }

    /**
     * Mencetak PDF (Logika tetap sama seperti punya kamu).
     */
    public function printPdf(Dispensasi $dispensasi)
    {
        if ($dispensasi->status !== 'disetujui') {
            abort(403, 'Surat dispensasi ini belum disetujui.');
        }

        $dispensasi->load(['siswa.rombels.kelas', 'diajukanOleh', 'disetujuiOleh']);

        // Generate QR Code untuk verifikasi keaslian surat
        $verificationUrl = route('verifikasi.dispensasi', $dispensasi->id);
        $qrCode = 'data:image/svg+xml;base64,' . base64_encode(QrCode::format('svg')->size(80)->generate($verificationUrl));

        $pdf = Pdf::loadView('pdf.surat-dispensasi', compact('dispensasi', 'qrCode'));

        return $pdf->stream('surat-dispensasi-' . $dispensasi->nama_kegiatan . '.pdf');
    }
}
