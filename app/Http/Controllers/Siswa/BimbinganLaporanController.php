<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\PrakerinBimbinganAktivitasLog;
use App\Models\PrakerinBimbinganLaporan;
use App\Models\PrakerinBimbinganTahap;
use App\Models\PrakerinPenempatan;
use App\Services\PrakerinBeritaAcaraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class BimbinganLaporanController extends Controller
{
    protected PrakerinBeritaAcaraService $pdfService;

    public function __construct(PrakerinBeritaAcaraService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Halaman Utama Bimbingan Laporan Siswa.
     */
    public function index()
    {
        $user = Auth::user();
        $siswa = $user->masterSiswa;

        if (! $siswa) {
            Alert::error('Akses Ditolak', 'Akun Anda tidak terhubung dengan data Master Siswa.');
            return redirect()->route('dashboard');
        }

        // Cek apakah siswa sudah dimapping ke rombel PKL aktif
        $penempatan = PrakerinPenempatan::with(['industri', 'rombelPkl', 'guruPembimbing.user'])
            ->where('master_siswa_id', $siswa->id)
            ->whereNotNull('prakerin_rombel_id')
            ->where('status', 'aktif')
            ->first();

        if (! $penempatan) {
            return view('pages.siswa.bimbingan-laporan.index', [
                'penempatan' => null,
                'laporan' => null,
            ]);
        }

        // Ambil atau buat data bimbingan laporan untuk penempatan ini
        $laporan = PrakerinBimbinganLaporan::firstOrCreate(
            ['prakerin_penempatan_id' => $penempatan->id],
            [
                'status_judul' => 'draft',
                'status_laporan' => 'draft',
            ]
        );

        // Jika belum ada tahapan/bab sama sekali, buatkan template bab bawaan
        if ($laporan->tahaps()->count() === 0) {
            foreach (PrakerinBimbinganLaporan::DEFAULT_TAHAP_TEMPLATES as $template) {
                $laporan->tahaps()->create([
                    'judul_tahap' => $template['judul_tahap'],
                    'deskripsi' => $template['deskripsi'],
                    'urutan' => $template['urutan'],
                    'status' => 'belum_mulai',
                ]);
            }
        }

        $laporan->load([
            'tahaps.anotasis',
            'tahaps.reviewer',
            'aktivitasLogs.user',
            'penempatan.guruPembimbing.user.digitalSignature',
        ]);

        return view('pages.siswa.bimbingan-laporan.index', compact('penempatan', 'laporan'));
    }

    /**
     * Siswa mengajukan judul laporan ke pembimbing.
     */
    public function ajukanJudul(Request $request)
    {
        $judul = $request->input('judul_laporan') ?? $request->input('judul');
        $abstrak = $request->input('deskripsi_judul') ?? $request->input('abstrak_rencana');
        $request->merge([
            'judul_laporan' => $judul,
            'deskripsi_judul' => $abstrak,
        ]);

        $validated = $request->validate([
            'judul_laporan' => 'required|string|min:5|max:255',
            'deskripsi_judul' => 'nullable|string|max:1000',
        ], [
            'judul_laporan.required' => 'Judul laporan PKL wajib diisi.',
            'judul_laporan.min' => 'Judul laporan minimal 5 karakter.',
        ]);

        $laporan = $this->getLaporanSiswa();

        if ($laporan->status_judul === 'disetujui') {
            Alert::warning('Judul Sudah Disetujui', 'Judul laporan sudah disetujui oleh pembimbing dan tidak dapat diubah lagi.');
            return back();
        }

        if ($laporan->status_judul === 'diajukan') {
            Alert::info('Sedang Dalam Proses', 'Judul laporan Anda saat ini sedang dalam proses persetujuan oleh guru pembimbing.');
            return back();
        }

        $laporan->update([
            'judul_laporan' => trim($validated['judul_laporan']),
            'deskripsi_judul' => $validated['deskripsi_judul'] ? trim($validated['deskripsi_judul']) : null,
            'status_judul' => 'diajukan',
            'judul_diajukan_at' => now(),
            'catatan_judul' => null, // Reset catatan penolakan jika sebelumnya ditolak
        ]);

        PrakerinBimbinganAktivitasLog::catat(
            $laporan->id,
            Auth::id(),
            'ajukan_judul',
            'Siswa mengajukan judul laporan: "' . $laporan->judul_laporan . '"'
        );

        Alert::success('Berhasil', 'Judul laporan berhasil diajukan ke Guru Pembimbing.');

        return back();
    }

    /**
     * Siswa menambah bab/tahap bimbingan baru (Dinamis).
     */
    public function tambahTahap(Request $request)
    {
        $judulTahap = $request->input('judul_tahap') ?? $request->input('nama_tahap');
        $request->merge(['judul_tahap' => $judulTahap]);

        $validated = $request->validate([
            'judul_tahap' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:500',
        ]);

        $laporan = $this->getLaporanSiswa();

        $maxUrutan = (int) $laporan->tahaps()->max('urutan');

        $tahap = $laporan->tahaps()->create([
            'judul_tahap' => trim($validated['judul_tahap']),
            'deskripsi' => $validated['deskripsi'] ? trim($validated['deskripsi']) : null,
            'urutan' => $maxUrutan + 1,
            'status' => 'belum_mulai',
        ]);

        PrakerinBimbinganAktivitasLog::catat(
            $laporan->id,
            Auth::id(),
            'tambah_tahap',
            'Siswa menambahkan tahapan baru: "' . $tahap->judul_tahap . '"',
            $tahap->id
        );

        Alert::success('Berhasil', 'Tahapan/bab baru berhasil ditambahkan.');

        return back();
    }

    /**
     * Siswa menghapus bab kustom (Soft Delete).
     */
    public function hapusTahap(PrakerinBimbinganTahap $tahap)
    {
        $laporan = $this->getLaporanSiswa();
        abort_unless($tahap->prakerin_bimbingan_laporan_id === $laporan->id, 403);

        $judul = $tahap->judul_tahap;
        $tahap->delete();

        PrakerinBimbinganAktivitasLog::catat(
            $laporan->id,
            Auth::id(),
            'hapus_tahap',
            'Siswa menghapus tahapan: "' . $judul . '"'
        );

        Alert::success('Berhasil', 'Tahapan berhasil dihapus.');

        return back();
    }

    /**
     * Siswa mengunggah dokumen PDF laporan untuk bab tertentu.
     */
    public function uploadDokumen(Request $request, PrakerinBimbinganTahap $tahap)
    {
        $laporan = $this->getLaporanSiswa();
        abort_unless($tahap->prakerin_bimbingan_laporan_id === $laporan->id, 403);

        // Validasi judul harus sudah disetujui
        if ($laporan->status_judul !== 'disetujui') {
            Alert::error('Judul Belum Disetujui', 'Anda baru bisa menyusun & mengunggah laporan setelah judul disetujui oleh pembimbing.');
            return back();
        }

        $fileKey = $request->hasFile('file_pdf') ? 'file_pdf' : ($request->hasFile('file_dokumen') ? 'file_dokumen' : 'file_pdf');

        $request->validate([
            $fileKey => 'required|file|mimes:pdf|max:51200', // Wajib PDF, max 50 MB
            'catatan_siswa' => 'nullable|string|max:1000',
        ], [
            "{$fileKey}.required" => 'Pilih file dokumen laporan dalam format PDF.',
            "{$fileKey}.mimes" => 'File laporan WAJIB berformat PDF (.pdf).',
            "{$fileKey}.max" => 'Ukuran file PDF maksimal 50 MB.',
        ]);

        $file = $request->file($fileKey);
        $namaAsli = $file->getClientOriginalName();
        $fileSize = $file->getSize();

        $path = $file->store("prakerin_laporan/{$laporan->id}", 'public');

        $tahap->update([
            'file_pdf_path' => $path,
            'file_pdf_nama_asli' => $namaAsli,
            'file_pdf_size' => $fileSize,
            'catatan_siswa' => $request->filled('catatan_siswa') ? trim($request->catatan_siswa) : null,
            'status' => 'diajukan',
            'diajukan_at' => now(),
        ]);

        // Update status laporan menjadi dalam_bimbingan jika masih draft
        if ($laporan->status_laporan === 'draft') {
            $laporan->update(['status_laporan' => 'dalam_bimbingan']);
        }

        PrakerinBimbinganAktivitasLog::catat(
            $laporan->id,
            Auth::id(),
            'upload_laporan_pdf',
            'Siswa mengunggah dokumen PDF untuk ' . $tahap->judul_tahap . ' (' . $namaAsli . ')',
            $tahap->id
        );

        Alert::success('Berhasil', 'Dokumen PDF ' . $tahap->judul_tahap . ' berhasil diajukan untuk bimbingan.');

        return back();
    }

    /**
     * Halaman Siswa Melihat Hasil Catatan / Review Koreksi PDF dari Guru Pembimbing.
     */
    public function reviewViewer(PrakerinBimbinganTahap $tahap)
    {
        $laporan = $this->getLaporanSiswa();
        abort_unless($tahap->prakerin_bimbingan_laporan_id === $laporan->id, 403);

        $cleanPath = str_replace('public/', '', (string) $tahap->file_pdf_path);
        $fileExists = Storage::disk('public')->exists($tahap->file_pdf_path)
            || Storage::disk('public')->exists($cleanPath)
            || Storage::exists($tahap->file_pdf_path);

        if (! $tahap->file_pdf_path || ! $fileExists) {
            Alert::error('File Tidak Ditemukan', 'Dokumen PDF belum diunggah atau tidak ditemukan.');
            return back();
        }

        $tahap->load(['anotasis.user', 'reviewer']);
        $anotasis = $tahap->anotasis;

        return view('pages.siswa.bimbingan-laporan.viewer', compact('tahap', 'laporan', 'anotasis'));
    }

    /**
     * Unduh Berita Acara Bimbingan per Tahap (PDF).
     */
    public function unduhBeritaAcara(PrakerinBimbinganTahap $tahap)
    {
        $laporan = $this->getLaporanSiswa();
        abort_unless($tahap->prakerin_bimbingan_laporan_id === $laporan->id, 403);

        $pdf = $this->pdfService->generateBeritaAcaraPdf($tahap);

        return $pdf->stream('Berita-Acara-' . str_replace(' ', '-', $tahap->judul_tahap) . '.pdf');
    }

    /**
     * Unduh Rekap Log Riwayat Bimbingan Lengkap Siswa (PDF).
     */
    public function unduhRiwayatPdf()
    {
        $laporan = $this->getLaporanSiswa();

        $pdf = $this->pdfService->generateRiwayatBimbinganPdf($laporan);

        return $pdf->stream('Rekap-Riwayat-Bimbingan-Prakerin-' . ($laporan->penempatan->siswa?->nis ?? 'siswa') . '.pdf');
    }

    /**
     * Helper mengambil laporan siswa yang sedang login.
     */
    private function getLaporanSiswa(): PrakerinBimbinganLaporan
    {
        $siswa = Auth::user()->masterSiswa;
        abort_unless($siswa, 403);

        $penempatan = PrakerinPenempatan::where('master_siswa_id', $siswa->id)
            ->whereNotNull('prakerin_rombel_id')
            ->where('status', 'aktif')
            ->firstOrFail();

        return PrakerinBimbinganLaporan::where('prakerin_penempatan_id', $penempatan->id)->firstOrFail();
    }
}
