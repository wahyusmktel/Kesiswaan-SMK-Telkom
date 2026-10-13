<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\RabItem;
use App\Models\RabSubmission;
use App\Services\RabAiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RabController extends Controller
{
    /**
     * Pastikan role siswa tidak dapat mengakses fitur RAB.
     */
    private function ensureNotSiswa(): void
    {
        $user = Auth::user();
        if ($user && ($user->hasRole('Siswa') || strtolower(session('active_role') ?? '') === 'siswa')) {
            abort(403, 'Akses ditolak. Halaman Rencana Anggaran Biaya (RAB) tidak dapat diakses oleh Siswa.');
        }
    }

    /**
     * Tampilkan daftar dokumen RAB.
     */
    public function index(Request $request)
    {
        $this->ensureNotSiswa();
        RabSubmission::ensureTableExists();

        $query = RabSubmission::query()->with(['items', 'user']);

        // Filter Jenis Pengajuan (RAB vs Reimburse)
        if ($request->filled('jenis') && in_array($request->jenis, ['rab', 'reimburse'])) {
            $query->where('jenis_pengajuan', $request->jenis);
        }

        // Filter Pencarian
        $search = trim((string) $request->input('search'));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('judul_pengajuan', 'like', "%{$search}%")
                    ->orWhere('nomor_pengajuan', 'like', "%{$search}%")
                    ->orWhere('nomor_akun', 'like', "%{$search}%")
                    ->orWhere('nama_akun', 'like', "%{$search}%")
                    ->orWhere('drk', 'like', "%{$search}%")
                    ->orWhere('pembuat_nama', 'like', "%{$search}%");
            });
        }

        // Filter Status
        if ($request->filled('status') && in_array($request->status, ['draft', 'diajukan', 'disetujui', 'ditolak'])) {
            $query->where('status', $request->status);
        }

        // Filter Tahun
        if ($request->filled('tahun')) {
            $query->where('tahun_anggaran', $request->tahun);
        }

        $rabs = $query->latest('updated_at')->paginate(12)->withQueryString();

        // Ringkasan Statistik
        $stats = [
            'total_dokumen' => RabSubmission::count(),
            'total_rab' => RabSubmission::where('jenis_pengajuan', 'rab')->count(),
            'total_reimburse' => RabSubmission::where('jenis_pengajuan', 'reimburse')->count(),
            'total_nominal' => RabSubmission::sum('total_anggaran'),
            'draft' => RabSubmission::where('status', 'draft')->count(),
            'diajukan' => RabSubmission::where('status', 'diajukan')->count(),
            'disetujui' => RabSubmission::where('status', 'disetujui')->count(),
        ];

        $daftarTahun = RabSubmission::select('tahun_anggaran')
            ->distinct()
            ->orderByDesc('tahun_anggaran')
            ->pluck('tahun_anggaran')
            ->filter()
            ->values();

        if ($daftarTahun->isEmpty()) {
            $daftarTahun = collect([date('Y')]);
        }

        return view('pages.rab.index', compact('rabs', 'stats', 'search', 'daftarTahun'));
    }

    /**
     * Tampilkan form pembuatan RAB baru.
     */
    public function create()
    {
        $this->ensureNotSiswa();
        RabSubmission::ensureTableExists();

        $user = Auth::user();
        $user->loadMissing('masterGuru');

        $activeRole = session('active_role') ?: ($user->getRoleNames()->first() ?: 'Staf');

        // Nilai Default Cerdas
        $defaults = [
            'jenis_pengajuan' => 'rab',
            'judul_pengajuan' => '',
            'tahun_anggaran' => date('Y'),
            'nomor_akun' => '5611104',
            'nama_akun' => 'BPP Alat Pengolah Data',
            'drk' => 'Peningkatan Kapasitas dan Modernisasi Infrastruktur Pendidikan',
            'kebutuhan_waktu' => now()->translatedFormat('F Y'),
            'status' => 'draft',

            // Data Pembuat
            'pembuat_nama' => $user->name,
            'pembuat_nik' => $user->masterGuru?->nik ?? $user->nik ?? '',
            'pembuat_jabatan' => $activeRole,
            'tanggal_dibuat' => now()->format('Y-m-d'),

            // Data Catatan Anggaran
            'catatan_anggaran' => '-',

            // Data Pemeriksa (Default: Kaur Keuangan)
            'pemeriksa_nama' => 'Mevita Yollanda',
            'pemeriksa_nik' => '25960023',
            'pemeriksa_jabatan' => 'Kaur Keuangan',
            'tanggal_diperiksa' => now()->format('Y-m-d'),

            // Data Penyetuju (Default: Kepala Sekolah)
            'penyetuju_nama' => 'Dedi Eko Cahyono',
            'penyetuju_nik' => '22880011',
            'penyetuju_jabatan' => 'Kepala Sekolah',
            'tanggal_disetujui' => now()->format('Y-m-d'),

            'catatan' => '-',
        ];

        return view('pages.rab.create', compact('defaults'));
    }

    /**
     * Simpan dokumen RAB baru beserta item rinciannya.
     */
    public function store(Request $request)
    {
        $this->ensureNotSiswa();
        RabSubmission::ensureTableExists();

        $validated = $request->validate([
            'jenis_pengajuan' => 'required|in:rab,reimburse',
            'judul_pengajuan' => 'required|string|max:255',
            'tahun_anggaran' => 'required|string|max:20',
            'nomor_akun' => 'required|string|max:50',
            'nama_akun' => 'required|string|max:255',
            'drk' => 'required|string',
            'kebutuhan_waktu' => 'required|string|max:100',
            'status' => 'nullable|in:draft,diajukan,disetujui,ditolak',

            'pembuat_nama' => 'nullable|string|max:255',
            'pembuat_nik' => 'nullable|string|max:50',
            'pembuat_jabatan' => 'nullable|string|max:100',
            'tanggal_dibuat' => 'nullable|date',

            'catatan_anggaran' => 'nullable|string',

            'pemeriksa_nama' => 'nullable|string|max:255',
            'pemeriksa_nik' => 'nullable|string|max:50',
            'pemeriksa_jabatan' => 'nullable|string|max:100',
            'tanggal_diperiksa' => 'nullable|date',

            'penyetuju_nama' => 'nullable|string|max:255',
            'penyetuju_nik' => 'nullable|string|max:50',
            'penyetuju_jabatan' => 'nullable|string|max:100',
            'tanggal_disetujui' => 'nullable|date',

            'catatan' => 'nullable|string',

            // Rincian items
            'items' => 'required|array|min:1',
            'items.*.uraian_kegiatan' => 'required|string',
            'items.*.spesifikasi' => 'nullable|string',
            'items.*.satuan' => 'required|string|max:50',
            'items.*.volume' => 'required|numeric|min:0.01',
            'items.*.harga_satuan' => 'required|numeric|min:0',
        ]);

        $jenis = $validated['jenis_pengajuan'] ?? 'rab';
        $prefix = $jenis === 'reimburse' ? 'RMB' : 'RAB';
        $nextNumber = RabSubmission::where('tahun_anggaran', $validated['tahun_anggaran'])
            ->where('jenis_pengajuan', $jenis)
            ->count() + 1;
        $nomorPengajuan = sprintf("%s/%s/%03d", $prefix, $validated['tahun_anggaran'], $nextNumber);

        $rab = RabSubmission::create([
            'user_id' => Auth::id(),
            'nomor_pengajuan' => $nomorPengajuan,
            'jenis_pengajuan' => $jenis,
            'judul_pengajuan' => strtoupper(trim($validated['judul_pengajuan'])),
            'tahun_anggaran' => $validated['tahun_anggaran'],
            'nomor_akun' => $validated['nomor_akun'],
            'nama_akun' => $validated['nama_akun'],
            'drk' => $validated['drk'],
            'kebutuhan_waktu' => $validated['kebutuhan_waktu'],
            'status' => $validated['status'] ?? 'draft',

            'pembuat_nama' => $validated['pembuat_nama'] ?? Auth::user()->name,
            'pembuat_nik' => $validated['pembuat_nik'] ?? '',
            'pembuat_jabatan' => $validated['pembuat_jabatan'] ?? 'Staf',
            'tanggal_dibuat' => $validated['tanggal_dibuat'] ?? now()->format('Y-m-d'),

            'catatan_anggaran' => $validated['catatan_anggaran'] ?: '-',

            'pemeriksa_nama' => $validated['pemeriksa_nama'] ?: 'Mevita Yollanda',
            'pemeriksa_nik' => $validated['pemeriksa_nik'] ?: '25960023',
            'pemeriksa_jabatan' => $validated['pemeriksa_jabatan'] ?: 'Kaur Keuangan',
            'tanggal_diperiksa' => $validated['tanggal_diperiksa'] ?? now()->format('Y-m-d'),

            'penyetuju_nama' => $validated['penyetuju_nama'] ?: 'Dedi Eko Cahyono',
            'penyetuju_nik' => $validated['penyetuju_nik'] ?: '22880011',
            'penyetuju_jabatan' => $validated['penyetuju_jabatan'] ?: 'Kepala Sekolah',
            'tanggal_disetujui' => $validated['tanggal_disetujui'] ?? now()->format('Y-m-d'),

            'catatan' => $validated['catatan'] ?: '-',
            'total_anggaran' => 0,
        ]);

        foreach ($validated['items'] as $index => $itemData) {
            $vol = (float) $itemData['volume'];
            $harga = (float) $itemData['harga_satuan'];
            $rab->items()->create([
                'uraian_kegiatan' => $itemData['uraian_kegiatan'],
                'spesifikasi' => $itemData['spesifikasi'] ?? '-',
                'satuan' => $itemData['satuan'],
                'volume' => $vol,
                'harga_satuan' => $harga,
                'jumlah' => $vol * $harga,
                'sort_order' => $index + 1,
            ]);
        }

        $rab->recalculateTotal();

        $label = $jenis === 'reimburse' ? 'Pengajuan Reimburse' : 'Rencana Anggaran Biaya (RAB)';
        return redirect()->route('rab.show', $rab)->with('success', "{$label} berhasil dibuat.");
    }

    /**
     * Tampilkan detail dokumen RAB.
     */
    public function show(RabSubmission $rab)
    {
        $this->ensureNotSiswa();
        $rab->loadMissing(['items', 'user']);

        $kopBase64 = $this->getKopBase64();
        $settings = AppSetting::first();

        return view('pages.rab.show', compact('rab', 'kopBase64', 'settings'));
    }

    /**
     * Tampilkan form edit dokumen RAB.
     */
    public function edit(RabSubmission $rab)
    {
        $this->ensureNotSiswa();
        $rab->loadMissing('items');

        return view('pages.rab.edit', compact('rab'));
    }

    /**
     * Perbarui dokumen RAB beserta item rinciannya.
     */
    public function update(Request $request, RabSubmission $rab)
    {
        $this->ensureNotSiswa();

        $validated = $request->validate([
            'jenis_pengajuan' => 'required|in:rab,reimburse',
            'judul_pengajuan' => 'required|string|max:255',
            'tahun_anggaran' => 'required|string|max:20',
            'nomor_akun' => 'required|string|max:50',
            'nama_akun' => 'required|string|max:255',
            'drk' => 'required|string',
            'kebutuhan_waktu' => 'required|string|max:100',
            'status' => 'nullable|in:draft,diajukan,disetujui,ditolak',

            'pembuat_nama' => 'nullable|string|max:255',
            'pembuat_nik' => 'nullable|string|max:50',
            'pembuat_jabatan' => 'nullable|string|max:100',
            'tanggal_dibuat' => 'nullable|date',

            'catatan_anggaran' => 'nullable|string',

            'pemeriksa_nama' => 'nullable|string|max:255',
            'pemeriksa_nik' => 'nullable|string|max:50',
            'pemeriksa_jabatan' => 'nullable|string|max:100',
            'tanggal_diperiksa' => 'nullable|date',

            'penyetuju_nama' => 'nullable|string|max:255',
            'penyetuju_nik' => 'nullable|string|max:50',
            'penyetuju_jabatan' => 'nullable|string|max:100',
            'tanggal_disetujui' => 'nullable|date',

            'catatan' => 'nullable|string',

            'items' => 'required|array|min:1',
            'items.*.uraian_kegiatan' => 'required|string',
            'items.*.spesifikasi' => 'nullable|string',
            'items.*.satuan' => 'required|string|max:50',
            'items.*.volume' => 'required|numeric|min:0.01',
            'items.*.harga_satuan' => 'required|numeric|min:0',
        ]);

        $rab->update([
            'jenis_pengajuan' => $validated['jenis_pengajuan'],
            'judul_pengajuan' => strtoupper(trim($validated['judul_pengajuan'])),
            'tahun_anggaran' => $validated['tahun_anggaran'],
            'nomor_akun' => $validated['nomor_akun'],
            'nama_akun' => $validated['nama_akun'],
            'drk' => $validated['drk'],
            'kebutuhan_waktu' => $validated['kebutuhan_waktu'],
            'status' => $validated['status'] ?? $rab->status,

            'pembuat_nama' => $validated['pembuat_nama'] ?? $rab->pembuat_nama,
            'pembuat_nik' => $validated['pembuat_nik'] ?? $rab->pembuat_nik,
            'pembuat_jabatan' => $validated['pembuat_jabatan'] ?? $rab->pembuat_jabatan,
            'tanggal_dibuat' => $validated['tanggal_dibuat'] ?? $rab->tanggal_dibuat,

            'catatan_anggaran' => $validated['catatan_anggaran'] ?: '-',

            'pemeriksa_nama' => $validated['pemeriksa_nama'] ?: $rab->pemeriksa_nama,
            'pemeriksa_nik' => $validated['pemeriksa_nik'] ?: $rab->pemeriksa_nik,
            'pemeriksa_jabatan' => $validated['pemeriksa_jabatan'] ?: $rab->pemeriksa_jabatan,
            'tanggal_diperiksa' => $validated['tanggal_diperiksa'] ?? $rab->tanggal_diperiksa,

            'penyetuju_nama' => $validated['penyetuju_nama'] ?: $rab->penyetuju_nama,
            'penyetuju_nik' => $validated['penyetuju_nik'] ?: $rab->penyetuju_nik,
            'penyetuju_jabatan' => $validated['penyetuju_jabatan'] ?: $rab->penyetuju_jabatan,
            'tanggal_disetujui' => $validated['tanggal_disetujui'] ?? $rab->tanggal_disetujui,

            'catatan' => $validated['catatan'] ?: '-',
        ]);

        // Hapus item lama dan simpan item baru
        $rab->items()->delete();

        foreach ($validated['items'] as $index => $itemData) {
            $vol = (float) $itemData['volume'];
            $harga = (float) $itemData['harga_satuan'];
            $rab->items()->create([
                'uraian_kegiatan' => $itemData['uraian_kegiatan'],
                'spesifikasi' => $itemData['spesifikasi'] ?? '-',
                'satuan' => $itemData['satuan'],
                'volume' => $vol,
                'harga_satuan' => $harga,
                'jumlah' => $vol * $harga,
                'sort_order' => $index + 1,
            ]);
        }

        $rab->recalculateTotal();

        $label = $rab->jenis_pengajuan === 'reimburse' ? 'Pengajuan Reimburse' : 'Dokumen RAB';
        return redirect()->route('rab.show', $rab)->with('success', "Perubahan {$label} berhasil disimpan.");
    }

    /**
     * Hapus dokumen RAB.
     */
    public function destroy(RabSubmission $rab)
    {
        $this->ensureNotSiswa();
        $rab->delete();

        return redirect()->route('rab.index')->with('success', 'Dokumen berhasil dihapus.');
    }

    /**
     * Update status persetujuan dokumen RAB.
     */
    public function updateStatus(Request $request, RabSubmission $rab)
    {
        $this->ensureNotSiswa();

        $validated = $request->validate([
            'status' => 'required|in:draft,diajukan,disetujui,ditolak',
            'catatan_anggaran' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $rab->update([
            'status' => $validated['status'],
            'catatan_anggaran' => $validated['catatan_anggaran'] ?? $rab->catatan_anggaran,
            'catatan' => $validated['catatan'] ?? $rab->catatan,
        ]);

        return back()->with('success', 'Status dokumen berhasil diperbarui.');
    }

    /**
     * Hasilkan draft RAB dengan bantuan Stella AI via AJAX / API.
     */
    public function aiGenerate(Request $request, RabAiService $aiService)
    {
        $this->ensureNotSiswa();

        $request->validate([
            'prompt' => 'required|string|min:5|max:1000',
        ]);

        try {
            $draft = $aiService->generateRabDraft($request->prompt);
            return response()->json([
                'success' => true,
                'message' => 'Draft RAB berhasil digenerate oleh AI.',
                'data' => $draft,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate AI: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export Dokumen RAB ke PDF (sama persis dengan format lampiran).
     */
    public function exportPdf(RabSubmission $rab, Request $request)
    {
        $this->ensureNotSiswa();
        $rab->loadMissing('items');

        $pdf = $this->buildPdf($rab);
        $prefix = $rab->jenis_pengajuan === 'reimburse' ? 'Reimburse' : 'RAB';
        $filename = $prefix . '_' . Str::slug($rab->judul_pengajuan) . '_' . ($rab->tahun_anggaran ?? date('Y')) . '.pdf';

        if ($request->query('view') === 'stream') {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    /**
     * Tampilan cetak langsung browser (HTML Print View).
     */
    public function printView(RabSubmission $rab)
    {
        $this->ensureNotSiswa();
        $rab->loadMissing('items');

        $kopBase64 = $this->getKopBase64();
        $logoDataUri = $this->imageDataUri(public_path('images/teaching-module/smk-telkom-lampung.png'));
        $ribbonDataUri = $this->imageDataUri(public_path('images/teaching-module/header-ribbon.png'));
        $settings = AppSetting::first();

        return view('pages.rab.print', compact('rab', 'kopBase64', 'logoDataUri', 'ribbonDataUri', 'settings'));
    }

    /**
     * Bangun objek DomPDF dengan layout presisi.
     */
    private function buildPdf(RabSubmission $rab)
    {
        $kopBase64 = $this->getKopBase64();
        $logoDataUri = $this->imageDataUri(public_path('images/teaching-module/smk-telkom-lampung.png'));
        $ribbonDataUri = $this->imageDataUri(public_path('images/teaching-module/header-ribbon.png'));

        return Pdf::loadView('pages.rab.pdf', [
            'rab' => $rab,
            'kopBase64' => $kopBase64,
            'logoDataUri' => $logoDataUri,
            'ribbonDataUri' => $ribbonDataUri,
            'settings' => AppSetting::first(),
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'Arial')
            ->setOption('isRemoteEnabled', true);
    }

    /**
     * Ambil Base64 Data URI untuk gambar Kop Surat Penilaian UKK dari App Settings.
     */
    private function getKopBase64(): ?string
    {
        $settings = AppSetting::first();
        $kopPath = $settings->kop_surat_ukk ?? null;
        if ($kopPath && Storage::disk('public')->exists($kopPath)) {
            $kopData = Storage::disk('public')->get($kopPath);
            $kopMime = Storage::disk('public')->mimeType($kopPath);
            return 'data:' . $kopMime . ';base64,' . base64_encode($kopData);
        }
        return null;
    }

    /**
     * Konversi gambar lokal menjadi Base64 Data URI agar DomPDF dapat memuat secara instan.
     */
    private function imageDataUri(string $path): ?string
    {
        if (!is_file($path) || !is_readable($path)) {
            return null;
        }

        $mime = mime_content_type($path) ?: 'image/png';
        return 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($path));
    }
}
