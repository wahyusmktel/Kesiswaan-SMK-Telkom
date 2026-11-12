<?php

namespace App\Http\Controllers\Pembimbing;

use App\Http\Controllers\Controller;
use App\Models\PrakerinBimbinganAktivitasLog;
use App\Models\PrakerinBimbinganAnotasi;
use App\Models\PrakerinBimbinganBeritaAcara;
use App\Models\PrakerinBimbinganJudul;
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

        if ($guru) {
            $penempatanQuery->where(function ($q) use ($guru) {
                $q->where('master_guru_id', $guru->id)
                    ->orWhereHas('rombelPkl', function ($rq) use ($guru) {
                        $rq->whereHas('pembimbingInternal', function ($iq) use ($guru) {
                            $iq->where('master_guru_id', $guru->id);
                        });
                    });
            });
        } elseif (! $isSuperAdmin) {
            Alert::error('Akses Ditolak', 'Akun Anda tidak terhubung dengan data Master Guru sebagai Pembimbing Sekolah.');
            return redirect()->route('dashboard');
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
        if ($guru) {
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
        } else {
            $rombels = PrakerinRombel::with('industri')->where('status', 'aktif')->orderBy('nama_rombel')->get();
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
            'totalAccFinal',
            'guru'
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

        // Auto-sinkron entri judul riwayat jika ada judul laporan tapi riwayat kosong
        if ($laporan->judul_laporan && $laporan->judulRiwayats()->count() === 0) {
            $nomorBa = null;
            if ($laporan->status_judul === 'disetujui') {
                $nomorBa = 'BA-JUDUL-ACC/' . date('Ymd') . '/' . str_pad($laporan->id, 4, '0', STR_PAD_LEFT);
            } elseif ($laporan->status_judul === 'ditolak') {
                $nomorBa = 'BA-JUDUL-TOLAK/' . date('Ymd') . '/' . str_pad($laporan->id, 4, '0', STR_PAD_LEFT) . '/R1';
            }
            $laporan->judulRiwayats()->create([
                'pengajuan_ke' => 1,
                'judul_laporan' => $laporan->judul_laporan,
                'deskripsi_judul' => $laporan->deskripsi_judul,
                'status' => in_array($laporan->status_judul, ['disetujui', 'ditolak']) ? $laporan->status_judul : 'diajukan',
                'catatan_pembimbing' => $laporan->catatan_judul,
                'nomor_berita_acara' => $nomorBa,
                'diajukan_at' => $laporan->judul_diajukan_at ?? now(),
                'reviewed_at' => $laporan->judul_reviewed_at,
            ]);
        }

        $laporan->load([
            'penempatan.siswa.rombels.kelas',
            'penempatan.industri',
            'penempatan.rombelPkl',
            'penempatan.guruPembimbing',
            'judulRiwayats.reviewer',
            'tahaps.anotasis',
            'tahaps.beritaAcaras',
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

        // Cegah penolakan berulang atau persetujuan saat status masih ditolak (menunggu siswa mengajukan kembali)
        if ($laporan->status_judul === 'ditolak') {
            Alert::warning('Menunggu Revisi Siswa', 'Judul telah ditolak sebelumnya. Anda baru dapat meninjau (menyetujui atau menolak) kembali setelah siswa mengajukan revisi judul laporan.');
            return back();
        }

        if ($laporan->status_judul === 'disetujui') {
            Alert::info('Judul Sudah Disetujui', 'Judul laporan ini sudah disetujui sebelumnya.');
            return back();
        }

        if ($laporan->status_judul !== 'diajukan') {
            Alert::warning('Tidak Dapat Direview', 'Status judul saat ini tidak dalam antrean review (' . ucfirst($laporan->status_judul) . ').');
            return back();
        }

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

        $latestJudul = $laporan->judulRiwayats()->where('status', 'diajukan')->latest()->first()
            ?? $laporan->judulRiwayats()->latest()->first();

        if (! $latestJudul) {
            $latestJudul = $laporan->judulRiwayats()->create([
                'pengajuan_ke' => $laporan->judulRiwayats()->count() + 1,
                'judul_laporan' => $laporan->judul_laporan ?? 'Judul Laporan Prakerin',
                'deskripsi_judul' => $laporan->deskripsi_judul,
                'status' => 'diajukan',
                'diajukan_at' => $laporan->judul_diajukan_at ?? now(),
            ]);
        }

        $isAcc = $status === 'disetujui';
        if ($isAcc) {
            $nomorBeritaAcara = 'BA-JUDUL-ACC/' . date('Ymd') . '/' . str_pad($laporan->id, 4, '0', STR_PAD_LEFT);
        } else {
            $revisiKe = $laporan->judulRiwayats()->where('status', 'ditolak')->count() + 1;
            $nomorBeritaAcara = 'BA-JUDUL-TOLAK/' . date('Ymd') . '/' . str_pad($laporan->id, 4, '0', STR_PAD_LEFT) . '/R' . $revisiKe;
        }

        $latestJudul->update([
            'status' => $status,
            'catatan_pembimbing' => $catatan,
            'nomor_berita_acara' => $nomorBeritaAcara,
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
        ]);

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
     * Guru Pembimbing Membatalkan Status ACC Judul Laporan Siswa.
     */
    public function batalkanAccJudul(Request $request, PrakerinBimbinganLaporan $laporan)
    {
        $this->authorizeAccess($laporan);

        if ($laporan->status_judul !== 'disetujui') {
            Alert::warning('Tidak Dapat Dibatalkan', 'Status judul saat ini bukan disetujui (ACC).');
            return back();
        }

        $validated = $request->validate([
            'alasan_pembatalan' => 'required|string|max:1000',
        ], [
            'alasan_pembatalan.required' => 'Alasan pembatalan ACC judul wajib diisi.',
        ]);

        $alasan = trim($validated['alasan_pembatalan']);

        // Update riwayat judul yang disetujui sebelumnya
        $latestJudul = $laporan->judulRiwayats()->where('status', 'disetujui')->latest()->first()
            ?? $laporan->judulRiwayats()->latest()->first();

        if ($latestJudul) {
            $latestJudul->update([
                'status' => 'diajukan',
                'catatan_pembimbing' => 'ACC Dibatalkan oleh Pembimbing. Alasan: ' . $alasan,
                'reviewed_at' => now(),
                'reviewed_by' => Auth::id(),
            ]);
        }

        // Kembalikan status judul laporan menjadi diajukan
        $laporanUpdate = [
            'status_judul' => 'diajukan',
            'catatan_judul' => 'ACC Dibatalkan: ' . $alasan,
            'judul_reviewed_at' => now(),
        ];

        // Jika laporan sebelumnya sudah selesai_acc atau siap_acc atau dalam_bimbingan,
        // kembalikan status laporan ke draft karena judulnya perlu disetujui ulang
        if (in_array($laporan->status_laporan, ['selesai_acc', 'siap_acc', 'dalam_bimbingan'])) {
            $laporanUpdate['status_laporan'] = 'draft';
            $laporanUpdate['acc_at'] = null;
            $laporanUpdate['acc_by'] = null;
        }

        $laporan->update($laporanUpdate);

        PrakerinBimbinganAktivitasLog::catat(
            $laporan->id,
            Auth::id(),
            'batal_acc_judul',
            'Guru pembimbing membatalkan persetujuan (ACC) judul: "' . $laporan->judul_laporan . '". Alasan: ' . $alasan
        );

        Alert::success('Berhasil', 'Persetujuan (ACC) judul laporan berhasil dibatalkan. Status judul kembali menjadi antrean peninjauan.');

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
     * Guru Pembimbing Membatalkan Status ACC Bab / Tahap Tertentu.
     */
    public function batalkanAccTahap(Request $request, PrakerinBimbinganTahap $tahap)
    {
        $laporan = $tahap->laporan;
        $this->authorizeAccess($laporan);

        if ($tahap->status !== 'disetujui') {
            Alert::warning('Tidak Dapat Dibatalkan', 'Status bab ini belum atau tidak berstatus disetujui (ACC).');
            return back();
        }

        $validated = $request->validate([
            'alasan_pembatalan' => 'required|string|max:1000',
            'status_tujuan' => 'nullable|in:perlu_revisi,diajukan',
        ], [
            'alasan_pembatalan.required' => 'Alasan pembatalan ACC bab wajib diisi.',
        ]);

        $alasan = trim($validated['alasan_pembatalan']);
        $statusTujuan = $validated['status_tujuan'] ?? 'perlu_revisi';

        $jumlahRevisi = $tahap->beritaAcaras()->where('jenis', 'revisi')->count();
        $revisiKe = $jumlahRevisi + 1;
        $nomorBeritaAcara = 'BA-REV/' . date('Ymd') . '/' . str_pad($tahap->id, 4, '0', STR_PAD_LEFT) . '/R' . $revisiKe;

        // Terbitkan catatan Berita Acara pembatalan / revisi
        $tahap->beritaAcaras()->create([
            'prakerin_bimbingan_laporan_id' => $laporan->id,
            'user_id' => Auth::id(),
            'nomor_berita_acara' => $nomorBeritaAcara,
            'jenis' => 'revisi',
            'revisi_ke' => $revisiKe,
            'judul_tahap' => $tahap->judul_tahap,
            'catatan_pembimbing' => 'ACC Dibatalkan oleh Pembimbing: ' . $alasan,
            'total_anotasi' => $tahap->anotasis()->count(),
            'file_pdf_path' => $tahap->file_pdf_path,
            'diterbitkan_at' => now(),
        ]);

        // Perbarui status tahap
        $baAcc = $tahap->beritaAcaras()->where('jenis', 'disetujui')->latest('id')->first();
        if ($baAcc) {
            // Lepaskan kaitan anotasi agar kembali aktif untuk dikoreksi kembali
            $baAcc->anotasis()->update(['prakerin_bimbingan_berita_acara_id' => null]);
        }

        $tahap->update([
            'status' => $statusTujuan,
            'disetujui_at' => null,
            'catatan_pembimbing' => 'ACC Dibatalkan: ' . $alasan,
            'nomor_berita_acara' => $nomorBeritaAcara,
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
            'berita_acara_at' => now(),
        ]);

        // Jika status laporan sebelumnya adalah siap_acc atau selesai_acc, kembalikan ke dalam_bimbingan
        if (in_array($laporan->status_laporan, ['siap_acc', 'selesai_acc'])) {
            $laporan->update([
                'status_laporan' => 'dalam_bimbingan',
                'acc_at' => null,
                'acc_by' => null,
            ]);
        }

        PrakerinBimbinganAktivitasLog::catat(
            $laporan->id,
            Auth::id(),
            'batal_acc_bab',
            'Guru pembimbing membatalkan ACC pada ' . $tahap->judul_tahap . '. Alasan: ' . $alasan,
            $tahap->id
        );

        Alert::success('Berhasil', 'Persetujuan (ACC) pada ' . $tahap->judul_tahap . ' berhasil dibatalkan.');

        return back();
    }

    /**
     * Halaman Editor Pemeriksaan Dokumen Interaktif (Interactive PDF Annotator).
     */
    public function editorAnotasi(Request $request, PrakerinBimbinganTahap $tahap)
    {
        $laporan = $tahap->laporan;
        $this->authorizeAccess($laporan);

        // Validasi prasyarat TTD digital
        $sig = UserDigitalSignature::where('user_id', Auth::id())->first();
        if (! $sig || ! $sig->isReady()) {
            Alert::warning('Setup TTD Diperlukan', 'Harap lakukan setup Tanda Tangan Digital terlebih dahulu sebelum memeriksa dokumen bimbingan.');
            return redirect()->route('tanda-tangan.index');
        }

        $beritaAcaras = $tahap->beritaAcaras()->with(['pembimbing', 'anotasis.user'])->orderBy('id', 'desc')->get();

        $selectedBaId = $request->query('berita_acara_id');
        $selectedBeritaAcara = $selectedBaId ? $beritaAcaras->firstWhere('id', (int) $selectedBaId) : null;
        $isArsipMode = (bool) $selectedBeritaAcara;

        $targetPdfPath = $isArsipMode && $selectedBeritaAcara->file_pdf_path
            ? $selectedBeritaAcara->file_pdf_path
            : $tahap->file_pdf_path;

        $cleanPath = str_replace('public/', '', (string) $targetPdfPath);
        $fileExists = Storage::disk('public')->exists($targetPdfPath)
            || Storage::disk('public')->exists($cleanPath)
            || Storage::exists($targetPdfPath);

        if (! $targetPdfPath || ! $fileExists) {
            // Fallback ke file tahap jika file arsip tidak ditemukan
            $targetPdfPath = $tahap->file_pdf_path;
            $cleanPath = str_replace('public/', '', (string) $targetPdfPath);
            $fileExists = Storage::disk('public')->exists($targetPdfPath)
                || Storage::disk('public')->exists($cleanPath)
                || Storage::exists($targetPdfPath);

            if (! $targetPdfPath || ! $fileExists) {
                Alert::error('File Tidak Ada', 'Dokumen PDF belum diunggah oleh siswa.');
                return back();
            }
        }

        $tahap->load([
            'laporan.penempatan.siswa.rombels.kelas',
            'laporan.penempatan.industri',
        ]);

        if ($isArsipMode) {
            $anotasis = $selectedBeritaAcara->anotasis()->with('user')->get();
            $canAnnotate = false;
        } else {
            $anotasis = $tahap->anotasisAktif()->with('user')->get();
            $canAnnotate = ($tahap->status !== 'disetujui');
        }

        $activePdfUrl = asset('storage/' . $cleanPath);

        return view('pages.pembimbing.bimbingan-laporan.annotator', compact(
            'tahap',
            'laporan',
            'sig',
            'anotasis',
            'beritaAcaras',
            'selectedBeritaAcara',
            'isArsipMode',
            'canAnnotate',
            'activePdfUrl'
        ));
    }

    /**
     * Simpan Anotasi Coretan / Kotak / Lingkaran / Pin Catatan Baru (AJAX JSON).
     */
    public function simpanAnotasi(Request $request, PrakerinBimbinganTahap $tahap)
    {
        $laporan = $tahap->laporan;
        $this->authorizeAccess($laporan);

        if ($tahap->status === 'disetujui') {
            return response()->json([
                'success' => false,
                'message' => 'Tahapan bimbingan ini telah disahkan (ACC) dan tidak dapat ditambahkan catatan koreksi lagi.',
            ], 422);
        }

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
            'prakerin_bimbingan_berita_acara_id' => null, // Anotasi aktif pada putaran berjalan
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

        if ($tahap->status === 'disetujui') {
            return response()->json([
                'success' => false,
                'message' => 'Tahapan bimbingan ini telah disahkan (ACC) dan catatan revisi terkunci.',
            ], 422);
        }

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

        if ($tahap->status === 'disetujui') {
            Alert::warning('Tahap Sudah Disetujui', 'Tahapan bimbingan ini telah disetujui (ACC) dan tidak dapat direvisi atau diubah lagi.');
            return back();
        }

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

        $isAcc = $validated['status'] === 'disetujui';

        if ($isAcc) {
            $nomorBeritaAcara = 'BA-ACC/' . date('Ymd') . '/' . str_pad($tahap->id, 4, '0', STR_PAD_LEFT);
            $revisiKe = null;
        } else {
            $jumlahRevisi = $tahap->beritaAcaras()->where('jenis', 'revisi')->count();
            $revisiKe = $jumlahRevisi + 1;
            $nomorBeritaAcara = 'BA-REV/' . date('Ymd') . '/' . str_pad($tahap->id, 4, '0', STR_PAD_LEFT) . '/R' . $revisiKe;
        }

        $totalAnotasiAktif = $tahap->anotasisAktif()->count();

        // Terbitkan entri Berita Acara resmi ke tabel riwayat berita acara
        $beritaAcara = $tahap->beritaAcaras()->create([
            'prakerin_bimbingan_laporan_id' => $laporan->id,
            'user_id' => Auth::id(),
            'nomor_berita_acara' => $nomorBeritaAcara,
            'jenis' => $isAcc ? 'disetujui' : 'revisi',
            'revisi_ke' => $revisiKe,
            'judul_tahap' => $tahap->judul_tahap,
            'catatan_pembimbing' => $validated['catatan_pembimbing'] ? trim($validated['catatan_pembimbing']) : null,
            'total_anotasi' => $totalAnotasiAktif,
            'file_pdf_path' => $tahap->file_pdf_path,
            'diterbitkan_at' => now(),
        ]);

        // Kunci / arsipkan seluruh anotasi aktif ke Berita Acara yang baru saja diterbitkan
        $tahap->anotasisAktif()->update([
            'prakerin_bimbingan_berita_acara_id' => $beritaAcara->id,
        ]);

        $tahap->update([
            'status' => $validated['status'],
            'catatan_pembimbing' => $validated['catatan_pembimbing'] ? trim($validated['catatan_pembimbing']) : null,
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
            'disetujui_at' => $isAcc ? now() : null,
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
            $isAcc ? 'setujui_bab' : 'minta_revisi_bab',
            'Guru pembimbing menerbitkan ' . ($isAcc ? 'Berita Acara Pengesahan (ACC)' : 'Berita Acara Revisi (Revisi ke-' . $revisiKe . ')') . ' untuk ' . $tahap->judul_tahap,
            $tahap->id
        );

        if ($isAcc) {
            Alert::success('Berita Acara ACC Diterbitkan', 'Bab ' . $tahap->judul_tahap . ' berhasil disetujui (ACC) dan Berita Acara Pengesahan resmi telah diterbitkan.');
        } else {
            Alert::success('Berita Acara Revisi Diterbitkan', 'Berita Acara Revisi (Revisi ke-' . $revisiKe . ') untuk ' . $tahap->judul_tahap . ' berhasil diterbitkan.');
        }

        return redirect()->route('pembimbing-prakerin.bimbingan-laporan.detail', $laporan->id);
    }

    /**
     * Unduh Berita Acara spesifik berdasarkan ID Berita Acara (PDF).
     */
    public function unduhBeritaAcaraItem(PrakerinBimbinganBeritaAcara $beritaAcara)
    {
        $this->authorizeAccess($beritaAcara->laporan);

        $pdf = $this->pdfService->generateBeritaAcaraPdf($beritaAcara);

        $filename = ($beritaAcara->jenis === 'disetujui' ? 'BA-ACC-' : 'BA-Revisi-' . ($beritaAcara->revisi_ke ? 'R' . $beritaAcara->revisi_ke . '-' : '')) . str_replace(' ', '-', $beritaAcara->judul_tahap) . '.pdf';

        return $pdf->stream($filename);
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
     * Unduh Berita Acara Review Judul Laporan (PDF).
     */
    public function unduhBeritaAcaraJudul(PrakerinBimbinganJudul $judul)
    {
        $this->authorizeAccess($judul->laporan);

        $pdf = $this->pdfService->generateBeritaAcaraJudulPdf($judul);

        $statusStr = $judul->status === 'disetujui' ? 'ACC' : 'Revisi';
        return $pdf->stream('Berita-Acara-Judul-' . $statusStr . '-Pengajuan-' . $judul->pengajuan_ke . '.pdf');
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
     * Koreksi Otomatis Dokumen Laporan menggunakan Stella AI (AJAX JSON).
     */
    public function aiKoreksi(Request $request, PrakerinBimbinganTahap $tahap)
    {
        $laporan = $tahap->laporan;
        $this->authorizeAccess($laporan);

        $request->validate([
            'teks_dokumen' => 'required|string|min:15',
            'lingkup' => 'nullable|string|in:halaman,seluruh',
            'halaman' => 'nullable|integer|min:1',
        ], [
            'teks_dokumen.required' => 'Teks dokumen tidak boleh kosong.',
            'teks_dokumen.min' => 'Teks dokumen terlalu pendek untuk dianalisis oleh AI.',
        ]);

        $setting = \App\Models\AppSetting::first();
        if (! $setting || ! $setting->stella_ai_enabled || ! $setting->stella_ai_api_key || ! $setting->stella_ai_base_url) {
            return response()->json([
                'success' => false,
                'message' => 'Layanan Stella AI belum aktif atau konfigurasinya belum lengkap. Silakan hubungi Super Admin untuk mengaktifkan Stella AI pada Menu Pengaturan Aplikasi.',
            ], 422);
        }

        $siswa = $laporan->penempatan?->siswa;
        $industri = $laporan->penempatan?->industri;
        $namaSiswa = $siswa?->nama_lengkap ?? 'Siswa Prakerin';
        $judulLaporan = $laporan->judul_laporan ?? 'Laporan Praktik Kerja Industri';
        $judulTahap = $tahap->judul_tahap;
        $halaman = $request->input('halaman', 1);
        $lingkup = $request->input('lingkup', 'halaman');
        $teksDokumen = trim($request->input('teks_dokumen'));

        // Batasi panjang teks (maksimal ~14.000 karakter) agar efisien pada context token
        $maxChars = 14000;
        if (mb_strlen($teksDokumen) > $maxChars) {
            $teksDokumen = mb_substr($teksDokumen, 0, $maxChars) . "\n\n[...Konten selanjutnya diringkas untuk analisis AI...]";
        }

        $systemPrompt = <<<PROMPT
Kamu adalah "Stella AI", asisten pembimbing laporan Praktik Kerja Industri (Prakerin/PKL) di SMK Telkom Lampung.
Tugasmu adalah menganalisis teks bab laporan siswa untuk membantu guru pembimbing sekolah melakukan koreksi akademik secara cepat, teliti, dan solutif.

Pedoman Penilaian:
1. Kesesuaian materi dengan judul laporan dan bab laporan PKL (Pendahuluan, Profil DU-DI, Kegiatan Teknis/Hasil, Penutup/Kesimpulan).
2. Sistematika penulisan karya ilmiah & laporan PKL SMK.
3. Kebakuan bahasa Indonesia (EYD Edisi V), ejaan, tanda baca, istilah teknis, dan kejelasan kalimat.
4. Kerapian logika alur bahasan dan kedalaman deskripsi teknis pekerjaan siswa.

WAJIB balas HANYA dalam format JSON murni yang valid tanpa teks pembuka/penutup dan tanpa markdown ```json.
Struktur JSON yang WAJIB dipatuhi:
{
  "ringkasan_evaluasi": "Uraian ringkas 2-3 kalimat mengenai kualitas dan isi materi yang diperiksa.",
  "skor_kelayakan": 80,
  "rekomendasi_status": "disetujui",
  "catatan_arahan": "Draf catatan arahan pembimbing yang santun, jelas, dan siap pakai untuk siswa di berita acara.",
  "poin_koreksi": [
    {
      "aspek": "Tata Bahasa & EYD",
      "temuan": "Uraian kalimat/bagian yang kurang tepat atau perlu diperbaiki",
      "saran": "Instruksi perbaikan praktis bagi siswa"
    }
  ]
}
Catatan untuk "rekomendasi_status":
- Isi "disetujui" jika secara keseluruhan teks layak (skor >= 75) dengan perbaikan minor.
- Isi "perlu_revisi" jika terdapat kesalahan konsep/data/format substansial yang harus diperbaiki siswa.
PROMPT;

        $userMessage = "Data Laporan Siswa SMK Telkom Lampung:\n"
            . "- Nama Siswa: {$namaSiswa}\n"
            . "- DU-DI / Industri: " . ($industri?->nama_industri ?? '-') . "\n"
            . "- Judul Laporan: {$judulLaporan}\n"
            . "- Bab / Bagian yang Diperiksa: {$judulTahap}\n"
            . "- Lingkup Analisis: " . ($lingkup === 'halaman' ? "Halaman {$halaman}" : "Seluruh Dokumen Bab") . "\n\n"
            . "Teks Dokumen Laporan Siswa:\n"
            . "\"\"\"\n{$teksDokumen}\n\"\"\"\n\n"
            . "Berikan evaluasi koreksi objektif sekarang dalam JSON valid.";

        try {
            $client = \Illuminate\Support\Facades\Http::acceptJson()
                ->asJson()
                ->withToken($setting->stella_ai_api_key)
                ->timeout(120);

            $endpoint = rtrim($setting->stella_ai_base_url, '/') . '/chat/completions';
            $model = $setting->stella_ai_chat_model ?: 'gpt-4o-mini';

            $response = $client->post($endpoint, [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userMessage],
                ],
                'temperature' => 0.3,
            ]);

            if ($response->failed()) {
                \Illuminate\Support\Facades\Log::warning('Stella AI Bimbingan Request Failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Layanan Stella AI menolak permintaan (HTTP ' . $response->status() . '). Periksa kuota / kunci API Stella AI.',
                ], 500);
            }

            $respData = $response->json();
            $content = data_get($respData, 'choices.0.message.content')
                ?: data_get($respData, 'choices.0.message.reasoning_content');

            if (! is_string($content) || trim($content) === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'Stella AI tidak mengembalikan respon teks yang dapat dibaca.',
                ], 500);
            }

            // Bersihkan wrapper markdown jika ada
            $cleanJson = trim($content);
            if (str_starts_with($cleanJson, '```json')) {
                $cleanJson = substr($cleanJson, 7);
            } elseif (str_starts_with($cleanJson, '```')) {
                $cleanJson = substr($cleanJson, 3);
            }
            if (str_ends_with($cleanJson, '```')) {
                $cleanJson = substr($cleanJson, 0, -3);
            }
            $cleanJson = trim($cleanJson);

            $parsed = json_decode($cleanJson, true);
            if (! is_array($parsed)) {
                // Fallback jika model menjawab dengan teks biasa
                $parsed = [
                    'ringkasan_evaluasi' => 'Hasil evaluasi dan telaah Stella AI terhadap dokumen laporan.',
                    'skor_kelayakan' => 70,
                    'rekomendasi_status' => 'perlu_revisi',
                    'catatan_arahan' => $cleanJson,
                    'poin_koreksi' => [
                        [
                            'aspek' => 'Telaah Dokumen',
                            'temuan' => 'Ulasan umum konten laporan siswa',
                            'saran' => $cleanJson,
                        ]
                    ],
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $parsed,
                'model' => $model,
                'halaman' => $halaman,
                'lingkup' => $lingkup,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Stella AI Exception in Bimbingan', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat menghubungi Stella AI: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Helper otorisasi: memastikan guru adalah pembimbing siswa tersebut (atau Super Admin).
     */
    private function authorizeAccess(PrakerinBimbinganLaporan $laporan): void
    {
        $user = Auth::user();
        $guru = $user->masterGuru;

        if ($guru) {
            $isAssigned = $laporan->penempatan->master_guru_id === $guru->id
                || ($laporan->penempatan->rombelPkl?->pembimbingInternal?->master_guru_id === $guru->id);
            if (! $isAssigned && ! $user->hasRole('Super Admin')) {
                abort(403, 'Anda bukan guru pembimbing yang ditugaskan untuk siswa ini.');
            }
            return;
        }

        if (! $user->hasRole('Super Admin')) {
            abort(403, 'Akses ditolak.');
        }
    }
}
