<?php

namespace App\Http\Controllers\Pembimbing;

use App\Http\Controllers\Controller;
use App\Models\PrakerinBimbinganAktivitasLog;
use App\Models\PrakerinBimbinganAnotasi;
use App\Models\PrakerinBimbinganLaporan;
use App\Models\PrakerinBimbinganTahap;
use App\Models\PrakerinPenempatan;
use App\Models\PrakerinRombel;
use App\Models\UserDigitalSignature;
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
     * Daftar Siswa Bimbingan Guru Pembimbing Internal.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('Super Admin');
        $guru = $user->masterGuru;

        // 1. Validasi Prasyarat: Guru Harus Setup Tanda Tangan Digital Terlebih Dahulu
        $sig = UserDigitalSignature::where('user_id', $user->id)->first();
        $isSignatureReady = $sig && $sig->isReady();

        // 2. Query Siswa Bimbingan
        $penempatanQuery = PrakerinPenempatan::with([
            'siswa.rombels.kelas',
            'industri',
            'rombelPkl',
            'bimbinganLaporan.tahaps.anotasis',
        ])
        ->whereNotNull('prakerin_rombel_id')
        ->where('status', 'aktif');

        if (! $isSuperAdmin) {
            if (! $guru) {
                Alert::error('Akses Ditolak', 'Akun Anda tidak terhubung dengan data Master Guru.');
                return redirect()->route('dashboard');
            }
            $penempatanQuery->where('master_guru_id', $guru->id);
        }

        // Filter Pencarian
        if ($request->filled('search')) {
            $s = trim($request->search);
            $penempatanQuery->where(function ($q) use ($s) {
                $q->whereHas('siswa', fn ($sub) => $sub->where('nama_lengkap', 'like', "%{$s}%")->orWhere('nis', 'like', "%{$s}%"))
                    ->orWhereHas('industri', fn ($sub) => $sub->where('nama_industri', 'like', "%{$s}%"))
                    ->orWhereHas('bimbinganLaporan', fn ($sub) => $sub->where('judul_laporan', 'like', "%{$s}%"));
            });
        }

        // Filter Rombel PKL
        if ($request->filled('rombel_id')) {
            $penempatanQuery->where('prakerin_rombel_id', $request->rombel_id);
        }

        // Filter Status Judul
        if ($request->filled('status_judul') && $request->status_judul !== 'all') {
            $penempatanQuery->whereHas('bimbinganLaporan', function ($q) use ($request) {
                $q->where('status_judul', $request->status_judul);
            });
        }

        // Filter Status Laporan
        if ($request->filled('status_laporan') && $request->status_laporan !== 'all') {
            $penempatanQuery->whereHas('bimbinganLaporan', function ($q) use ($request) {
                $q->where('status_laporan', $request->status_laporan);
            });
        }

        $penempatans = $penempatanQuery->latest()->paginate(12)->withQueryString();

        // Ambil daftar Rombel PKL untuk dropdown filter
        if ($isSuperAdmin) {
            $rombels = PrakerinRombel::with('industri')->where('status', 'aktif')->orderBy('nama_rombel')->get();
        } else {
            $rombels = PrakerinRombel::with('industri')
                ->where('status', 'aktif')
                ->where(function ($q) use ($guru) {
                    $q->where('pembimbing_internal_id', $guru->id)
                        ->orWhereHas('pembimbingInternal', fn ($sub) => $sub->where('master_guru_id', $guru->id))
                        ->orWhereIn('id', function ($sub) use ($guru) {
                            $sub->select('prakerin_rombel_id')->from('prakerin_penempatans')->where('master_guru_id', $guru->id);
                        });
                })
                ->orderBy('nama_rombel')
                ->get();
        }

        // Pastikan setiap penempatan memiliki record bimbingan laporan
        foreach ($penempatans as $penempatan) {
            if (! $penempatan->bimbinganLaporan) {
                $laporan = PrakerinBimbinganLaporan::create([
                    'prakerin_penempatan_id' => $penempatan->id,
                    'status_judul' => 'draft',
                    'status_laporan' => 'draft',
                ]);
                foreach (PrakerinBimbinganLaporan::DEFAULT_TAHAP_TEMPLATES as $template) {
                    $laporan->tahaps()->create([
                        'judul_tahap' => $template['judul_tahap'],
                        'deskripsi' => $template['deskripsi'],
                        'urutan' => $template['urutan'],
                        'status' => 'belum_mulai',
                    ]);
                }
            }
        }

        // KPI Metrik Pembimbing
        $totalSiswaBimbingan = (clone $penempatanQuery)->count();
        $totalJudulDiajukan = (clone $penempatanQuery)->whereHas('bimbinganLaporan', fn ($q) => $q->where('status_judul', 'diajukan'))->count();
        $totalPerluReview = (clone $penempatanQuery)->whereHas('bimbinganLaporan.tahaps', fn ($q) => $q->where('status', 'diajukan'))->count();
        $totalAccFinal = (clone $penempatanQuery)->whereHas('bimbinganLaporan', fn ($q) => $q->where('status_laporan', 'selesai_acc'))->count();

        $hasTtdDigital = $isSignatureReady;

        return view('pages.pembimbing.bimbingan-laporan.index', compact(
            'penempatans',
            'rombels',
            'hasTtdDigital',
            'isSignatureReady',
            'sig',
            'totalSiswaBimbingan',
            'totalJudulDiajukan',
            'totalPerluReview',
            'totalAccFinal'
        ));
    }

    /**
     * Halaman Detail Progres Bimbingan Siswa Tertentu.
     */
    public function detailSiswa(PrakerinBimbinganLaporan $laporan)
    {
        $this->authorizeAccess($laporan);

        // Validasi prasyarat TTD digital
        $sig = UserDigitalSignature::where('user_id', Auth::id())->first();
        $isSignatureReady = $sig && $sig->isReady();
        $hasTtdDigital = $isSignatureReady;

        $laporan->load([
            'penempatan.siswa.rombels.kelas',
            'penempatan.industri',
            'penempatan.rombelPkl',
            'penempatan.guruPembimbing',
            'tahaps.anotasis',
            'tahaps.reviewer',
            'aktivitasLogs.user',
            'accBy',
        ]);

        $penempatan = $laporan->penempatan;

        return view('pages.pembimbing.bimbingan-laporan.detail', compact('laporan', 'penempatan', 'hasTtdDigital', 'isSignatureReady', 'sig'));
    }

    /**
     * Guru Pembimbing Menyetujui atau Menolak Judul Laporan Siswa.
     */
    public function reviewJudul(Request $request, PrakerinBimbinganLaporan $laporan)
    {
        $this->authorizeAccess($laporan);

        // Dukung input dari form action ('setuju'/'tolak') maupun status_keputusan ('disetujui'/'ditolak')
        $statusKeputusan = $request->input('status_keputusan');
        if (! $statusKeputusan && $request->filled('action')) {
            $statusKeputusan = $request->input('action') === 'setuju' ? 'disetujui' : 'ditolak';
        }

        $catatanJudul = $request->input('catatan_judul') ?? $request->input('catatan');

        $request->merge([
            'status_keputusan' => $statusKeputusan,
            'catatan_judul' => $catatanJudul,
        ]);

        $validated = $request->validate([
            'status_keputusan' => 'required|in:disetujui,ditolak',
            'catatan_judul' => 'required_if:status_keputusan,ditolak|nullable|string|max:1000',
        ], [
            'catatan_judul.required_if' => 'Alasan / catatan penolakan WAJIB diisi jika judul ditolak agar siswa dapat merevisi.',
        ]);

        $status = $validated['status_keputusan'];
        $catatan = $validated['catatan_judul'] ? trim($validated['catatan_judul']) : null;

        $laporan->update([
            'status_judul' => $status,
            'catatan_judul' => $catatan,
            'judul_reviewed_at' => now(),
            'status_laporan' => $status === 'disetujui' ? 'dalam_bimbingan' : $laporan->status_laporan,
        ]);

        PrakerinBimbinganAktivitasLog::catat(
            $laporan->id,
            Auth::id(),
            $status === 'disetujui' ? 'setujui_judul' : 'tolak_judul',
            'Guru pembimbing ' . ($status === 'disetujui' ? 'menyetujui' : 'menolak') . ' judul: "' . $laporan->judul_laporan . '". ' . ($catatan ? 'Catatan: ' . $catatan : '')
        );

        Alert::success('Berhasil', 'Keputusan judul laporan (' . strtoupper($status) . ') berhasil disimpan.');

        return back();
    }

    /**
     * Guru menambah bab/tahap bimbingan baru untuk siswa (Dinamis).
     */
    public function tambahTahap(Request $request, PrakerinBimbinganLaporan $laporan)
    {
        $this->authorizeAccess($laporan);

        $judulTahap = $request->input('judul_tahap') ?? $request->input('nama_tahap');
        $request->merge(['judul_tahap' => $judulTahap]);

        $validated = $request->validate([
            'judul_tahap' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:500',
        ]);

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
            'tambah_tahap_oleh_guru',
            'Guru pembimbing menambahkan tahapan: "' . $tahap->judul_tahap . '"',
            $tahap->id
        );

        Alert::success('Berhasil', 'Tahapan bimbingan berhasil ditambahkan.');

        return back();
    }

    /**
     * Guru menghapus bab bimbingan (Soft Delete).
     */
    public function hapusTahap(PrakerinBimbinganTahap $tahap)
    {
        $laporan = $tahap->laporan;
        $this->authorizeAccess($laporan);

        $judul = $tahap->judul_tahap;
        $tahap->delete();

        PrakerinBimbinganAktivitasLog::catat(
            $laporan->id,
            Auth::id(),
            'hapus_tahap_oleh_guru',
            'Guru pembimbing menghapus tahapan: "' . $judul . '"'
        );

        Alert::success('Berhasil', 'Tahapan berhasil dihapus.');

        return back();
    }

    /**
     * Halaman Editor Pemeriksaan Dokumen Interaktif (Interactive PDF Annotator).
     */
    public function editorAnotasi(PrakerinBimbinganTahap $tahap)
    {
        $laporan = $tahap->laporan;
        $this->authorizeAccess($laporan);

        // Validasi prasyarat TTD digital
        $sig = UserDigitalSignature::where('user_id', Auth::id())->first();
        if (! $sig || ! $sig->isReady()) {
            Alert::warning('Setup TTD Diperlukan', 'Harap lakukan setup Tanda Tangan Digital terlebih dahulu sebelum memeriksa dokumen bimbingan.');
            return redirect()->route('tanda-tangan.index');
        }

        $cleanPath = str_replace('public/', '', (string) $tahap->file_pdf_path);
        $fileExists = Storage::disk('public')->exists($tahap->file_pdf_path)
            || Storage::disk('public')->exists($cleanPath)
            || Storage::exists($tahap->file_pdf_path);

        if (! $tahap->file_pdf_path || ! $fileExists) {
            Alert::error('File Tidak Ada', 'Dokumen PDF belum diunggah oleh siswa.');
            return back();
        }

        $tahap->load([
            'anotasis.user',
            'laporan.penempatan.siswa.rombels.kelas',
            'laporan.penempatan.industri',
        ]);

        $anotasis = $tahap->anotasis;

        return view('pages.pembimbing.bimbingan-laporan.annotator', compact('tahap', 'laporan', 'sig', 'anotasis'));
    }

    /**
     * Simpan Anotasi Coretan / Kotak / Lingkaran / Pin Catatan Baru (AJAX JSON).
     */
    public function simpanAnotasi(Request $request, PrakerinBimbinganTahap $tahap)
    {
        $laporan = $tahap->laporan;
        $this->authorizeAccess($laporan);

        // Petakan tipe jika menggunakan nama alias ('box', 'circle', 'drawing', 'pin')
        $rawTipe = $request->input('tipe_anotasi') ?? $request->input('tipe', 'sorot_kotak');
        $tipeAnotasi = match ($rawTipe) {
            'box' => 'sorot_kotak',
            'circle' => 'sorot_lingkaran',
            'drawing' => 'coretan_bebas',
            'pin' => 'pin_catatan',
            default => $rawTipe,
        };

        $posX = $request->input('posisi_x') ?? $request->input('koordinat_x', 0);
        $posY = $request->input('posisi_y') ?? $request->input('koordinat_y', 0);
        $lebar = $request->input('lebar', 80);
        $tinggi = $request->input('tinggi', 40);

        $request->merge([
            'tipe_anotasi' => $tipeAnotasi,
            'posisi_x' => $posX,
            'posisi_y' => $posY,
            'lebar' => $lebar,
            'tinggi' => $tinggi,
        ]);

        $validated = $request->validate([
            'halaman' => 'required|integer|min:1',
            'posisi_x' => 'required|numeric|min:0',
            'posisi_y' => 'required|numeric|min:0',
            'lebar' => 'nullable|numeric|min:0',
            'tinggi' => 'nullable|numeric|min:0',
            'tipe_anotasi' => 'required|in:sorot_kotak,sorot_lingkaran,coretan_bebas,pin_catatan',
            'warna' => 'nullable|string|max:30',
            'catatan' => 'nullable|string|max:2000',
            'drawing_data' => 'nullable',
        ]);

        $drawingData = $validated['drawing_data'] ?? null;
        if (is_string($drawingData)) {
            $drawingData = json_decode($drawingData, true);
        }

        $anotasi = $tahap->anotasis()->create([
            'user_id' => Auth::id(),
            'halaman' => $validated['halaman'],
            'posisi_x' => $validated['posisi_x'],
            'posisi_y' => $validated['posisi_y'],
            'lebar' => $validated['lebar'] ?? 80,
            'tinggi' => $validated['tinggi'] ?? 40,
            'tipe_anotasi' => $validated['tipe_anotasi'],
            'warna' => $validated['warna'] ?? '#ef4444',
            'catatan' => trim($validated['catatan'] ?? '') ?: 'Penanda koreksi pembimbing',
            'drawing_data' => $drawingData,
        ]);

        $anotasi->load('user');

        PrakerinBimbinganAktivitasLog::catat(
            $laporan->id,
            Auth::id(),
            'tambah_anotasi',
            'Menambahkan catatan revisi pada ' . $tahap->judul_tahap . ' hal. ' . $anotasi->halaman . ': "' . \Illuminate\Support\Str::limit($anotasi->catatan, 50) . '"',
            $tahap->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Anotasi berhasil disimpan.',
            'anotasi' => $anotasi,
        ]);
    }

    /**
     * Hapus Anotasi Coretan / Catatan (Soft Delete AJAX JSON).
     */
    public function hapusAnotasi(PrakerinBimbinganAnotasi $anotasi)
    {
        $tahap = $anotasi->tahap;
        $laporan = $tahap->laporan;
        $this->authorizeAccess($laporan);

        $anotasi->delete();

        PrakerinBimbinganAktivitasLog::catat(
            $laporan->id,
            Auth::id(),
            'hapus_anotasi',
            'Menghapus catatan revisi pada ' . $tahap->judul_tahap . ' hal. ' . $anotasi->halaman,
            $tahap->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Anotasi berhasil dihapus.',
        ]);
    }

    /**
     * Selesaikan Review Bimbingan Bab (Putusan: Perlu Revisi / Disetujui).
     */
    public function selesaikanReview(Request $request, PrakerinBimbinganTahap $tahap)
    {
        $laporan = $tahap->laporan;
        $this->authorizeAccess($laporan);

        // Validasi prasyarat TTD digital
        $sig = UserDigitalSignature::where('user_id', Auth::id())->first();
        if (! $sig || ! $sig->isReady()) {
            Alert::warning('Setup TTD Diperlukan', 'Harap lakukan setup Tanda Tangan Digital terlebih dahulu.');
            return redirect()->route('tanda-tangan.index');
        }

        // Petakan status 'revisi' -> 'perlu_revisi'
        $rawStatus = $request->input('status');
        if ($rawStatus === 'revisi') {
            $rawStatus = 'perlu_revisi';
        }
        $request->merge(['status' => $rawStatus]);

        $validated = $request->validate([
            'status' => 'required|in:perlu_revisi,disetujui',
            'catatan_pembimbing' => 'nullable|string|max:2000',
            'pin' => 'nullable|string|max:10',
        ]);

        if ($request->filled('pin') && ! $sig->verifyPin($request->pin)) {
            Alert::error('PIN Salah', 'PIN Tanda Tangan Digital yang Anda masukkan tidak sesuai.');
            return back()->withInput();
        }

        $nomorBeritaAcara = $tahap->nomor_berita_acara;
        if (! $nomorBeritaAcara) {
            $nomorBeritaAcara = 'BA-PKL/' . date('Ymd') . '/' . str_pad($tahap->id, 4, '0', STR_PAD_LEFT);
        }

        $tahap->update([
            'status' => $validated['status'],
            'catatan_pembimbing' => $validated['catatan_pembimbing'] ? trim($validated['catatan_pembimbing']) : null,
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
            'disetujui_at' => $validated['status'] === 'disetujui' ? now() : null,
            'nomor_berita_acara' => $nomorBeritaAcara,
            'berita_acara_at' => now(),
        ]);

        // Jika seluruh bab sudah disetujui, update status laporan menjadi siap_acc
        if ($laporan->semua_bab_disetujui) {
            $laporan->update(['status_laporan' => 'siap_acc']);
        }

        PrakerinBimbinganAktivitasLog::catat(
            $laporan->id,
            Auth::id(),
            $validated['status'] === 'disetujui' ? 'setujui_bab' : 'minta_revisi_bab',
            'Guru pembimbing menetapkan ' . $tahap->judul_tahap . ' status: ' . strtoupper(str_replace('_', ' ', $validated['status'])),
            $tahap->id
        );

        Alert::success('Review Selesai', 'Pemeriksaan ' . $tahap->judul_tahap . ' berhasil disimpan sebagai ' . strtoupper(str_replace('_', ' ', $validated['status'])) . '.');

        return redirect()->route('pembimbing-prakerin.bimbingan-laporan.detail', $laporan->id);
    }

    /**
     * Tahap Akhir: ACC Final Laporan Prakerin dan Selesai.
     */
    public function accFinal(Request $request, PrakerinBimbinganLaporan $laporan)
    {
        $this->authorizeAccess($laporan);

        $request->validate([
            'catatan_acc' => 'nullable|string|max:1000',
        ]);

        $laporan->update([
            'status_laporan' => 'selesai_acc',
            'acc_at' => now(),
            'acc_by' => Auth::id(),
            'catatan_acc' => $request->filled('catatan_acc') ? trim($request->catatan_acc) : 'Laporan telah disetujui & memenuhi syarat pengesahan.',
        ]);

        PrakerinBimbinganAktivitasLog::catat(
            $laporan->id,
            Auth::id(),
            'acc_laporan_final',
            'Guru pembimbing memberikan ACC FINAL Laporan Prakerin. Laporan dinyatakan SELESAI.'
        );

        Alert::success('Laporan Selesai Di-ACC', 'Laporan Prakerin berhasil disetujui tuntas (ACC Selesai). Berita acara dan rekap bimbingan dapat diunduh.');

        return back();
    }

    /**
     * Unduh Berita Acara Bimbingan per Tahap (PDF).
     */
    public function unduhBeritaAcara(PrakerinBimbinganTahap $tahap)
    {
        $this->authorizeAccess($tahap->laporan);

        $pdf = $this->pdfService->generateBeritaAcaraPdf($tahap);

        return $pdf->stream('Berita-Acara-' . str_replace(' ', '-', $tahap->judul_tahap) . '.pdf');
    }

    /**
     * Unduh Rekap Log Riwayat Bimbingan Lengkap Siswa (PDF).
     */
    public function unduhRiwayatPdf(PrakerinBimbinganLaporan $laporan)
    {
        $this->authorizeAccess($laporan);

        $pdf = $this->pdfService->generateRiwayatBimbinganPdf($laporan);

        return $pdf->stream('Rekap-Riwayat-Bimbingan-Prakerin-' . ($laporan->penempatan->siswa?->nis ?? 'siswa') . '.pdf');
    }

    /**
     * Helper otorisasi: memastikan guru adalah pembimbing siswa tersebut (atau Super Admin).
     */
    private function authorizeAccess(PrakerinBimbinganLaporan $laporan): void
    {
        $user = Auth::user();
        if ($user->hasRole('Super Admin')) {
            return;
        }

        $guru = $user->masterGuru;
        if (! $guru || $laporan->penempatan->master_guru_id !== $guru->id) {
            abort(403, 'Anda bukan guru pembimbing yang ditugaskan untuk siswa ini.');
        }
    }
}
