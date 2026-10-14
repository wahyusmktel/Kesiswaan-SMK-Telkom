<?php

namespace App\Http\Controllers;

use App\Models\RabRealisasi;
use App\Models\RabRealisasiEvidence;
use App\Models\RabSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class KeuanganRabController extends Controller
{
    /**
     * Tampilkan Dashboard Khusus Role Keuangan.
     */
    public function index(Request $request)
    {
        $this->ensureKeuanganAccess();
        RabSubmission::ensureTableExists();
        RabRealisasi::ensureTableExists();

        $activeTab = $request->query('tab', 'usulan');
        $tahun = $request->query('tahun', date('Y'));
        $status = $request->query('status', 'semua');
        $jenis = $request->query('jenis', 'semua');
        $search = trim((string) $request->query('q', ''));

        // Query Usulan RAB / Reimburse
        $usulanQuery = RabSubmission::with(['user', 'items', 'realisasi.evidences'])
            ->latest('tanggal_dibuat');

        if ($tahun !== 'semua') {
            $usulanQuery->where('tahun_anggaran', $tahun);
        }
        if ($status !== 'semua') {
            $usulanQuery->where('status', $status);
        }
        if ($jenis !== 'semua') {
            $usulanQuery->where('jenis_pengajuan', $jenis);
        }
        if ($search !== '') {
            $usulanQuery->where(function ($q) use ($search) {
                $q->where('judul_pengajuan', 'like', "%{$search}%")
                  ->orWhere('nomor_pengajuan', 'like', "%{$search}%")
                  ->orWhere('pembuat_nama', 'like', "%{$search}%")
                  ->orWhere('nama_akun', 'like', "%{$search}%");
            });
        }
        $usulans = $usulanQuery->paginate(15)->withQueryString();

        // Query Laporan Realisasi
        $realisasiQuery = RabRealisasi::with(['rabSubmission', 'items', 'evidences'])
            ->latest('updated_at');

        if ($tahun !== 'semua') {
            $realisasiQuery->whereHas('rabSubmission', function ($q) use ($tahun) {
                $q->where('tahun_anggaran', $tahun);
            });
        }
        if ($status !== 'semua') {
            $realisasiQuery->where('status', $status);
        }
        if ($search !== '') {
            $realisasiQuery->where(function ($q) use ($search) {
                $q->where('nomor_realisasi', 'like', "%{$search}%")
                  ->orWhere('pembuat_nama', 'like', "%{$search}%")
                  ->orWhereHas('rabSubmission', function ($sq) use ($search) {
                      $sq->where('judul_pengajuan', 'like', "%{$search}%");
                  });
            });
        }
        $realisasis = $realisasiQuery->paginate(15, ['*'], 'realisasi_page')->withQueryString();

        // Query Bukti Eviden Keseluruhan
        $evidencesQuery = RabRealisasiEvidence::with('realisasi.rabSubmission')
            ->latest();
        if ($search !== '') {
            $evidencesQuery->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%");
            });
        }
        $evidences = $evidencesQuery->paginate(24, ['*'], 'evidence_page')->withQueryString();

        // Metrik Statistik Keuangan
        $stats = [
            'total_usulan' => RabSubmission::count(),
            'menunggu_verifikasi' => RabSubmission::whereIn('status', ['diajukan', 'draft'])->count(),
            'total_anggaran_disetujui' => RabSubmission::where('status', 'disetujui')->sum('total_anggaran'),
            'total_realisasi_pengeluaran' => RabRealisasi::sum('total_pengeluaran'),
            'total_realisasi_saldo' => RabRealisasi::sum('saldo_akhir'),
            'total_evidences' => RabRealisasiEvidence::count(),
        ];

        // Daftar tahun yang tersedia
        $daftarTahun = RabSubmission::select('tahun_anggaran')
            ->distinct()
            ->orderByDesc('tahun_anggaran')
            ->pluck('tahun_anggaran')
            ->filter()
            ->values();

        if ($daftarTahun->isEmpty()) {
            $daftarTahun = collect([date('Y')]);
        }

        return view('pages.rab.keuangan.index', compact(
            'usulans',
            'realisasis',
            'evidences',
            'stats',
            'activeTab',
            'tahun',
            'status',
            'jenis',
            'search',
            'daftarTahun'
        ));
    }

    /**
     * Verifikasi & update status pengajuan RAB oleh tim Keuangan.
     */
    public function updateRabStatus(Request $request, RabSubmission $rab)
    {
        $this->ensureKeuanganAccess();

        $validated = $request->validate([
            'status' => 'required|in:draft,diajukan,disetujui,ditolak',
            'catatan' => 'nullable|string',
        ]);

        $user = Auth::user();
        $rab->update([
            'status' => $validated['status'],
            'pemeriksa_nama' => $user->name,
            'pemeriksa_nik' => $user->masterGuru?->nik ?? $user->nik ?? '25960023',
            'pemeriksa_jabatan' => session('active_role') ?: 'Kaur Keuangan',
            'tanggal_diperiksa' => now()->format('Y-m-d'),
            'catatan' => $validated['catatan'] ?: ($rab->catatan ?? '-'),
        ]);

        return back()->with('success', "Status pengajuan {$rab->nomor_pengajuan} berhasil diperbarui.");
    }

    /**
     * Verifikasi & update status laporan realisasi oleh tim Keuangan.
     */
    public function updateRealisasiStatus(Request $request, RabRealisasi $realisasi)
    {
        $this->ensureKeuanganAccess();

        $validated = $request->validate([
            'status' => 'required|in:draft,diajukan,diverifikasi_keuangan,disetujui,ditolak',
            'catatan_keuangan' => 'nullable|string',
        ]);

        $user = Auth::user();
        $realisasi->update([
            'status' => $validated['status'],
            'pemeriksa_nama' => $user->name,
            'pemeriksa_nik' => $user->masterGuru?->nik ?? $user->nik ?? '25960023',
            'pemeriksa_jabatan' => session('active_role') ?: 'Kaur Keuangan',
            'tanggal_diperiksa' => now()->format('Y-m-d'),
            'catatan_keuangan' => $validated['catatan_keuangan'] ?? $realisasi->catatan_keuangan,
        ]);

        return back()->with('success', "Status laporan realisasi {$realisasi->nomor_realisasi} berhasil diperbarui.");
    }

    /**
     * Pastikan pengguna memiliki peran tim Keuangan atau Super Admin.
     */
    private function ensureKeuanganAccess(): void
    {
        $user = Auth::user();
        abort_unless($user, 401);

        $activeRole = session('active_role') ?: $user->getRoleNames()->first();
        $roleName = Str::lower((string) $activeRole);

        $allowedRoles = ['keuangan', 'kaur keuangan', 'bendahara', 'super admin'];

        if (!in_array($roleName, $allowedRoles, true) && !$user->hasAnyRole(['Super Admin', 'Keuangan', 'Kaur Keuangan', 'Bendahara'])) {
            abort(403, 'Akses terbatas hanya untuk Bagian Keuangan dan Super Admin.');
        }
    }
}
