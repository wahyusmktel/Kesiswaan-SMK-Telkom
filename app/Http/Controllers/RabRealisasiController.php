<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\RabRealisasi;
use App\Models\RabRealisasiEvidence;
use App\Models\RabRealisasiItem;
use App\Models\RabSubmission;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RabRealisasiController extends Controller
{
    /**
     * Tampilkan detail Laporan Realisasi Penggunaan Anggaran.
     */
    public function show(RabSubmission $rab)
    {
        $this->ensureValidRab($rab);
        $realisasi = $this->getOrCreateRealisasi($rab);
        $realisasi->loadMissing(['items', 'evidences']);

        $kopBase64 = $this->getKopBase64();
        $isKeuanganUser = $this->isKeuanganUser();

        return view('pages.rab.realisasi.show', compact('rab', 'realisasi', 'kopBase64', 'isKeuanganUser'));
    }

    /**
     * Tampilkan form penyusunan transaksi realisasi & upload eviden.
     */
    public function edit(RabSubmission $rab)
    {
        $this->ensureValidRab($rab);
        $realisasi = $this->getOrCreateRealisasi($rab);
        $realisasi->loadMissing(['items', 'evidences']);

        return view('pages.rab.realisasi.edit', compact('rab', 'realisasi'));
    }

    /**
     * Simpan pembaruan data transaksi realisasi dan bukti eviden.
     */
    public function update(Request $request, RabSubmission $rab)
    {
        $this->ensureValidRab($rab);
        $realisasi = $this->getOrCreateRealisasi($rab);

        $validated = $request->validate([
            'nomor_realisasi' => 'nullable|string|max:100',
            'status' => 'nullable|in:draft,diajukan,diverifikasi_keuangan,disetujui,ditolak',

            'pembuat_nama' => 'nullable|string|max:255',
            'pembuat_nik' => 'nullable|string|max:50',
            'pembuat_jabatan' => 'nullable|string|max:100',
            'tanggal_dibuat' => 'nullable|date',

            'pemeriksa_nama' => 'nullable|string|max:255',
            'pemeriksa_nik' => 'nullable|string|max:50',
            'pemeriksa_jabatan' => 'nullable|string|max:100',
            'tanggal_diperiksa' => 'nullable|date',

            'penyetuju_nama' => 'nullable|string|max:255',
            'penyetuju_nik' => 'nullable|string|max:50',
            'penyetuju_jabatan' => 'nullable|string|max:100',
            'tanggal_disetujui' => 'nullable|date',

            'catatan' => 'nullable|string',

            // Transaksi items
            'items' => 'required|array|min:1',
            'items.*.tanggal' => 'required|date',
            'items.*.uraian' => 'required|string',
            'items.*.penerimaan' => 'nullable|numeric|min:0',
            'items.*.pengeluaran' => 'nullable|numeric|min:0',
            'items.*.keterangan' => 'nullable|string',

            // Bukti Eviden (Multiple Upload)
            'evidences' => 'nullable|array',
            'evidences.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240', // max 10MB
            'evidence_titles' => 'nullable|array',
            'evidence_titles.*' => 'nullable|string|max:255',
            'evidence_notes' => 'nullable|array',
            'evidence_notes.*' => 'nullable|string',
        ]);

        $realisasi->update([
            'nomor_realisasi' => $validated['nomor_realisasi'] ?? $realisasi->nomor_realisasi,
            'status' => $validated['status'] ?? $realisasi->status,

            'pembuat_nama' => $validated['pembuat_nama'] ?? $realisasi->pembuat_nama,
            'pembuat_nik' => $validated['pembuat_nik'] ?? $realisasi->pembuat_nik,
            'pembuat_jabatan' => $validated['pembuat_jabatan'] ?? $realisasi->pembuat_jabatan,
            'tanggal_dibuat' => $validated['tanggal_dibuat'] ?? $realisasi->tanggal_dibuat,

            'pemeriksa_nama' => $validated['pemeriksa_nama'] ?? $realisasi->pemeriksa_nama,
            'pemeriksa_nik' => $validated['pemeriksa_nik'] ?? $realisasi->pemeriksa_nik,
            'pemeriksa_jabatan' => $validated['pemeriksa_jabatan'] ?? $realisasi->pemeriksa_jabatan,
            'tanggal_diperiksa' => $validated['tanggal_diperiksa'] ?? $realisasi->tanggal_diperiksa,

            'penyetuju_nama' => $validated['penyetuju_nama'] ?? $realisasi->penyetuju_nama,
            'penyetuju_nik' => $validated['penyetuju_nik'] ?? $realisasi->penyetuju_nik,
            'penyetuju_jabatan' => $validated['penyetuju_jabatan'] ?? $realisasi->penyetuju_jabatan,
            'tanggal_disetujui' => $validated['tanggal_disetujui'] ?? $realisasi->tanggal_disetujui,

            'catatan' => $validated['catatan'] ?? '-',
        ]);

        // Simpan / Timpa Item Transaksi
        $realisasi->items()->delete();
        foreach ($validated['items'] as $index => $itemData) {
            $realisasi->items()->create([
                'tanggal' => $itemData['tanggal'],
                'uraian' => $itemData['uraian'],
                'penerimaan' => (float) ($itemData['penerimaan'] ?? 0),
                'pengeluaran' => (float) ($itemData['pengeluaran'] ?? 0),
                'saldo' => 0, // Dihitung di recalculateTotals()
                'keterangan' => $itemData['keterangan'] ?: '-',
                'sort_order' => $index + 1,
            ]);
        }

        // Proses Bukti Eviden yang Diunggah
        if ($request->hasFile('evidences')) {
            $files = $request->file('evidences');
            $titles = $request->input('evidence_titles', []);
            $notes = $request->input('evidence_notes', []);

            foreach ($files as $idx => $file) {
                if ($file && $file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $ext = strtolower($file->getClientOriginalExtension());
                    $mime = $file->getMimeType();
                    $size = $file->getSize();

                    $folder = "rab_evidences/{$rab->id}";
                    $fileName = 'eviden_' . time() . '_' . Str::random(8) . '.' . $ext;
                    $path = $file->storeAs($folder, $fileName, 'public');

                    $title = !empty($titles[$idx]) ? trim($titles[$idx]) : pathinfo($originalName, PATHINFO_FILENAME);
                    $note = !empty($notes[$idx]) ? trim($notes[$idx]) : null;

                    $realisasi->evidences()->create([
                        'judul' => $title,
                        'file_path' => $path,
                        'file_name' => $originalName,
                        'file_type' => $mime,
                        'file_size' => $size,
                        'keterangan' => $note,
                    ]);
                }
            }
        }

        // Recalculate Totals & Running Saldo
        $realisasi->recalculateTotals();

        return redirect()->route('rab.realisasi.show', $rab)
            ->with('success', 'Laporan Realisasi Penggunaan Anggaran berhasil diperbarui.');
    }

    /**
     * Unggah bukti eviden secara langsung (misal dari halaman show).
     */
    public function storeEvidence(Request $request, RabSubmission $rab)
    {
        $this->ensureValidRab($rab);
        $realisasi = $this->getOrCreateRealisasi($rab);

        $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
            'judul' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $customTitle = $request->input('judul');
        $customNote = $request->input('keterangan');

        $uploadedCount = 0;
        foreach ($request->file('files') as $file) {
            if ($file->isValid()) {
                $originalName = $file->getClientOriginalName();
                $ext = strtolower($file->getClientOriginalExtension());
                $folder = "rab_evidences/{$rab->id}";
                $fileName = 'eviden_' . time() . '_' . Str::random(8) . '.' . $ext;
                $path = $file->storeAs($folder, $fileName, 'public');

                $title = $customTitle ?: pathinfo($originalName, PATHINFO_FILENAME);

                $realisasi->evidences()->create([
                    'judul' => $title,
                    'file_path' => $path,
                    'file_name' => $originalName,
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'keterangan' => $customNote,
                ]);
                $uploadedCount++;
            }
        }

        return back()->with('success', "{$uploadedCount} berkas bukti eviden berhasil diunggah.");
    }

    /**
     * Hapus berkas bukti eviden.
     */
    public function destroyEvidence(RabSubmission $rab, RabRealisasiEvidence $evidence)
    {
        $this->ensureValidRab($rab);

        if ($evidence->file_path && Storage::disk('public')->exists($evidence->file_path)) {
            Storage::disk('public')->delete($evidence->file_path);
        }

        $evidence->delete();

        return back()->with('success', 'Berkas bukti eviden berhasil dihapus.');
    }

    /**
     * Export Laporan Realisasi ke format PDF (DomPDF).
     */
    public function exportPdf(RabSubmission $rab, Request $request)
    {
        $this->ensureValidRab($rab);
        $realisasi = $this->getOrCreateRealisasi($rab);
        $realisasi->loadMissing(['items', 'evidences']);

        $kopBase64 = $this->getKopBase64();
        $settings = AppSetting::first();

        $pdf = Pdf::loadView('pages.rab.realisasi.pdf', [
            'rab' => $rab,
            'realisasi' => $realisasi,
            'kopBase64' => $kopBase64,
            'settings' => $settings,
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'Arial')
            ->setOption('isRemoteEnabled', true);

        $filename = 'Realisasi_' . Str::slug($rab->judul_pengajuan) . '_' . ($rab->tahun_anggaran ?? date('Y')) . '.pdf';

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
        $this->ensureValidRab($rab);
        $realisasi = $this->getOrCreateRealisasi($rab);
        $realisasi->loadMissing(['items', 'evidences']);

        $kopBase64 = $this->getKopBase64();
        $settings = AppSetting::first();

        return view('pages.rab.realisasi.print', compact('rab', 'realisasi', 'kopBase64', 'settings'));
    }

    /**
     * Perbarui status Laporan Realisasi (Draft, Diajukan, Diverifikasi Keuangan, Selesai).
     */
    public function updateStatus(Request $request, RabSubmission $rab)
    {
        $this->ensureValidRab($rab);
        $realisasi = $this->getOrCreateRealisasi($rab);

        $validated = $request->validate([
            'status' => 'required|in:draft,diajukan,diverifikasi_keuangan,disetujui,ditolak',
            'catatan_keuangan' => 'nullable|string',
        ]);

        $realisasi->update([
            'status' => $validated['status'],
            'catatan_keuangan' => $validated['catatan_keuangan'] ?? $realisasi->catatan_keuangan,
        ]);

        return back()->with('success', 'Status laporan realisasi berhasil diperbarui.');
    }

    /**
     * Validasi bahwa dokumen adalah jenis pengajuan RAB (bukan reimburse).
     */
    private function ensureValidRab(RabSubmission $rab): void
    {
        RabSubmission::ensureTableExists();
        RabRealisasi::ensureTableExists();

        if ($rab->jenis_pengajuan !== 'rab') {
            abort(404, 'Laporan realisasi anggaran hanya berlaku untuk pengajuan berjenis RAB.');
        }
    }

    /**
     * Dapatkan atau inisialisasi dokumen RabRealisasi untuk RabSubmission terkait.
     */
    private function getOrCreateRealisasi(RabSubmission $rab): RabRealisasi
    {
        $realisasi = $rab->realisasi()->first();

        if (!$realisasi) {
            $nomorRealisasi = 'REAL/' . $rab->nomor_pengajuan;

            $realisasi = RabRealisasi::create([
                'rab_submission_id' => $rab->id,
                'nomor_realisasi' => $nomorRealisasi,
                'status' => 'draft',
                'total_penerimaan' => $rab->total_anggaran,
                'total_pengeluaran' => 0,
                'saldo_akhir' => $rab->total_anggaran,

                'pembuat_nama' => $rab->pembuat_nama,
                'pembuat_nik' => $rab->pembuat_nik,
                'pembuat_jabatan' => $rab->pembuat_jabatan,
                'tanggal_dibuat' => now()->format('Y-m-d'),

                'pemeriksa_nama' => $rab->pemeriksa_nama ?: 'Mevita Yollanda',
                'pemeriksa_nik' => $rab->pemeriksa_nik ?: '25960023',
                'pemeriksa_jabatan' => $rab->pemeriksa_jabatan ?: 'Kaur Keuangan',
                'tanggal_diperiksa' => now()->format('Y-m-d'),

                'penyetuju_nama' => $rab->penyetuju_nama ?: 'Dedi Eko Cahyono',
                'penyetuju_nik' => $rab->penyetuju_nik ?: '22880011',
                'penyetuju_jabatan' => $rab->penyetuju_jabatan ?: 'Kepala Sekolah',
                'tanggal_disetujui' => now()->format('Y-m-d'),

                'catatan' => '-',
            ]);

            // Buat baris pertama default: Realisasi Penerimaan Dana sesuai RAB
            $realisasi->items()->create([
                'tanggal' => $rab->tanggal_disetujui ?? now()->format('Y-m-d'),
                'uraian' => 'Realisasi Dana',
                'penerimaan' => $rab->total_anggaran,
                'pengeluaran' => 0,
                'saldo' => $rab->total_anggaran,
                'keterangan' => '-',
                'sort_order' => 1,
            ]);

            $realisasi->recalculateTotals();
        }

        return $realisasi;
    }

    /**
     * Ambil Base64 Data URI untuk Kop Surat UKK dari App Settings.
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
     * Periksa apakah pengguna saat ini adalah Bagian Keuangan / Super Admin.
     */
    private function isKeuanganUser(): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        $activeRole = session('active_role') ?: $user->getRoleNames()->first();
        $roleName = Str::lower((string) $activeRole);

        return in_array($roleName, ['kaur keuangan', 'keuangan', 'bendahara', 'super admin', 'kepala sekolah'], true)
            || $user->hasAnyRole(['Super Admin', 'Kepala Sekolah', 'Kaur Keuangan']);
    }
}
