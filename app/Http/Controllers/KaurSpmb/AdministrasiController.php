<?php

namespace App\Http\Controllers\KaurSpmb;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\MasterGuru;
use App\Models\SpmbBeritaAcara;
use App\Models\SpmbPesertaSeleksi;
use App\Models\SpmbPiketAbsensi;
use App\Models\SpmbPiketKelompok;
use App\Models\SpmbPiketPetugas;
use App\Models\SpmbPiketTanggal;
use App\Models\TahunPelajaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdministrasiController extends Controller
{
    /**
     * Display a listing of SPMB administration documents (Berita Acara Mingguan).
     */
    public function index(Request $request)
    {
        // 1. Resolve Available Academic Years
        $dbYears = SpmbPiketKelompok::distinct()->pluck('tahun_ajaran')->toArray();
        $tpYears = TahunPelajaran::distinct()->pluck('tahun')->toArray();
        $availableYears = array_unique(array_filter(array_merge($dbYears, $tpYears, ['2027/2028', '2026/2027', '2025/2026'])));
        rsort($availableYears);

        $selectedYear = $request->get('tahun_ajaran');
        if (! $selectedYear || ! in_array($selectedYear, $availableYears)) {
            $selectedYear = in_array('2027/2028', $availableYears) ? '2027/2028' : ($availableYears[0] ?? '2027/2028');
        }

        $search = trim($request->get('search', ''));
        $perPage = (int) $request->get('per_page', 10);
        if ($perPage < 5 || $perPage > 100) {
            $perPage = 10;
        }

        // 2. Query Berita Acara
        $query = SpmbBeritaAcara::where('tahun_ajaran', $selectedYear)
            ->with([
                'tanggal.kelompok.petugas',
                'tanggal.absensis',
                'tanggal.laporanPetugasBy',
                'kaurUser',
            ]);

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                    ->orWhere('judul', 'like', "%{$search}%")
                    ->orWhere('nama_penandatangan', 'like', "%{$search}%")
                    ->orWhere('ringkasan_kegiatan', 'like', "%{$search}%");
            });
        }

        $beritaAcaras = $query->orderBy('tanggal_kegiatan', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // 3. Statistics Cards
        $totalBeritaAcara = SpmbBeritaAcara::where('tahun_ajaran', $selectedYear)->count();
        $totalCasis = SpmbBeritaAcara::where('tahun_ajaran', $selectedYear)->sum('jumlah_casis_hadir');
        $totalTamu = SpmbBeritaAcara::where('tahun_ajaran', $selectedYear)->sum('jumlah_tamu');
        $pekanTerlaksana = SpmbPiketTanggal::whereHas('kelompok', fn ($q) => $q->where('tahun_ajaran', $selectedYear))
            ->where('status', 'Terlaksana')
            ->count();

        // 4. Resolve Default Kaur SPMB Signer
        $kaurUser = User::whereHas('roles', fn ($r) => $r->whereIn('name', ['Kaur SPMB', 'KAUR SPMB']))
            ->first();
        $currentUser = Auth::user();
        $defaultSignerUser = ($currentUser && $currentUser->hasRole(['Kaur SPMB', 'KAUR SPMB'])) ? $currentUser : ($kaurUser ?? $currentUser);

        $signerGuru = $defaultSignerUser?->id ? MasterGuru::where('user_id', $defaultSignerUser->id)->first() : null;
        $defaultNamaSigner = $defaultSignerUser?->name ?? 'Wahyu Rahmat Hidayat, S.Kom., Gr.';
        $defaultNipSigner = $signerGuru?->nik ?? '';
        $defaultJabatanSigner = 'KAUR SPMB SMK Telkom Lampung';

        // 5. Options Tanggal Piket for Modal Create
        $availableTanggals = SpmbPiketTanggal::whereHas('kelompok', fn ($q) => $q->where('tahun_ajaran', $selectedYear))
            ->with(['kelompok.petugas', 'absensis', 'laporanPetugasBy', 'pesertaSeleksis'])
            ->orderBy('tanggal', 'desc')
            ->get();

        $romawiMap = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];

        $tanggalOptions = $availableTanggals->map(function ($tgl) use ($romawiMap, $selectedYear) {
            $carbon = Carbon::parse($tgl->tanggal);
            $blnRomawi = $romawiMap[$carbon->month] ?? 'X';
            $thn = $carbon->year;

            $totalPetugas = $tgl->kelompok?->petugas?->count() ?? 0;
            $hadirCount = $tgl->absensis?->whereIn('status', ['Hadir', 'Terlambat'])->count() ?? 0;

            // Generate default number
            $nomorSurat = "0{$tgl->id}/BA-SPMB/SMKTEL-LPG/{$blnRomawi}/{$thn}";

            // Live metrics from digital attendance of candidates & parents
            $pesertas = $tgl->pesertaSeleksis;
            $totalCasis = $pesertas->count();
            $casisHadirCount = $pesertas->where('status_kehadiran_siswa', 'Hadir')->count();
            $ortuHadirCount = $pesertas->where('status_kehadiran_ortu', 'Hadir')->count();

            // Default ringkasan kegiatan
            $ringkasan = "Telah dilaksanakan kegiatan pelayanan dan piket SPMB (Seleksi Penerimaan Murid Baru) Tahun Pelajaran {$selectedYear} SMK Telkom Lampung pada hari {$tgl->hari}, " .
                $carbon->locale('id')->isoFormat('D MMMM Y') . " bertempat di SMK Telkom Lampung. " .
                "Seluruh rangkaian kegiatan mencakup penyambutan calon siswa & orang tua/wali, pemeriksaan tes buta warna, pelaksanaan wawancara orang tua dan calon siswa, penyiapan sarana tes, serta publikasi kegiatan berjalan dengan lancar, tertib, dan kondusif.";

            return [
                'id' => $tgl->id,
                'tanggal' => $tgl->tanggal->toDateString(),
                'label' => $carbon->locale('id')->isoFormat('dddd, D MMMM Y') . ' — ' . ($tgl->kelompok?->nama_kelompok ?? 'Kelompok') . " ({$hadirCount}/{$totalPetugas} Petugas Hadir, {$casisHadirCount} Casis Hadir)",
                'kelompok_nama' => $tgl->kelompok?->nama_kelompok ?? '',
                'hari_tanggal' => $carbon->locale('id')->isoFormat('dddd, D MMMM Y'),
                'nomor_surat' => $nomorSurat,
                'waktu' => $tgl->waktu ?: '07.30 - 12.00 WIB',
                'tempat' => 'SMK Telkom Lampung',
                'ringkasan_default' => $ringkasan,
                'total_petugas' => $totalPetugas,
                'hadir_count' => $hadirCount,
                'jumlah_casis_hadir' => $casisHadirCount,
                'jumlah_tamu' => $ortuHadirCount,
                'total_casis' => $totalCasis,
                'catatan_petugas' => $tgl->catatan_petugas ?? '',
                'laporan_petugas_by' => $tgl->laporanPetugasBy?->name ?? '',
                'laporan_petugas_at' => $tgl->laporan_petugas_at ? $tgl->laporan_petugas_at->format('d/m/Y H:i') : '',
                'has_eviden_siswa' => ! empty($tgl->foto_eviden_absen_siswa),
                'has_eviden_ortu' => ! empty($tgl->foto_eviden_absen_ortu),
            ];
        });

        return view('pages.kaur-spmb.administrasi.index', compact(
            'availableYears',
            'selectedYear',
            'search',
            'perPage',
            'beritaAcaras',
            'totalBeritaAcara',
            'pekanTerlaksana',
            'totalCasis',
            'totalTamu',
            'defaultNamaSigner',
            'defaultNipSigner',
            'defaultJabatanSigner',
            'tanggalOptions'
        ));
    }

    /**
     * Store a newly created Berita Acara.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'spmb_piket_tanggal_id' => 'required|exists:spmb_piket_tanggals,id',
            'tahun_ajaran' => 'required|string|max:20',
            'nomor_surat' => 'required|string|max:100',
            'judul' => 'required|string|max:255',
            'tanggal_kegiatan' => 'required|date',
            'waktu_mulai' => 'required|string|max:20',
            'waktu_selesai' => 'required|string|max:20',
            'tempat' => 'required|string|max:150',
            'ringkasan_kegiatan' => 'required|string',
            'jumlah_casis_hadir' => 'nullable|integer|min:0',
            'jumlah_tamu' => 'nullable|integer|min:0',
            'catatan_kendala' => 'nullable|string',
            'kesimpulan' => 'nullable|string',
            'nama_penandatangan' => 'required|string|max:150',
            'nip_penandatangan' => 'nullable|string|max:50',
            'jabatan_penandatangan' => 'required|string|max:100',
            'tempat_ttd' => 'required|string|max:100',
            'tanggal_ttd' => 'nullable|string|max:50',
            'status' => 'required|in:Draft,Final',
        ]);

        $validated['jumlah_casis_hadir'] = $validated['jumlah_casis_hadir'] ?? 0;
        $validated['jumlah_tamu'] = $validated['jumlah_tamu'] ?? 0;
        $validated['created_by'] = Auth::id();
        $validated['kaur_spmb_user_id'] = Auth::id();

        if (empty($validated['tanggal_ttd'])) {
            $validated['tanggal_ttd'] = Carbon::parse($validated['tanggal_kegiatan'])->locale('id')->translatedFormat('d F Y');
        }

        $beritaAcara = SpmbBeritaAcara::create($validated);

        // Update status tanggal piket menjadi Terlaksana jika sebelumnya Terjadwal
        if ($beritaAcara->spmb_piket_tanggal_id) {
            $tgl = SpmbPiketTanggal::find($beritaAcara->spmb_piket_tanggal_id);
            if ($tgl && $tgl->status === 'Terjadwal') {
                $tgl->update(['status' => 'Terlaksana']);
            }
        }

        return redirect()->back()->with('success', "Berita Acara nomor {$beritaAcara->nomor_surat} berhasil disimpan.");
    }

    /**
     * Update the specified Berita Acara.
     */
    public function update(Request $request, SpmbBeritaAcara $administrasi)
    {
        $validated = $request->validate([
            'nomor_surat' => 'required|string|max:100',
            'judul' => 'required|string|max:255',
            'tanggal_kegiatan' => 'required|date',
            'waktu_mulai' => 'required|string|max:20',
            'waktu_selesai' => 'required|string|max:20',
            'tempat' => 'required|string|max:150',
            'ringkasan_kegiatan' => 'required|string',
            'jumlah_casis_hadir' => 'nullable|integer|min:0',
            'jumlah_tamu' => 'nullable|integer|min:0',
            'catatan_kendala' => 'nullable|string',
            'kesimpulan' => 'nullable|string',
            'nama_penandatangan' => 'required|string|max:150',
            'nip_penandatangan' => 'nullable|string|max:50',
            'jabatan_penandatangan' => 'required|string|max:100',
            'tempat_ttd' => 'required|string|max:100',
            'tanggal_ttd' => 'nullable|string|max:50',
            'status' => 'required|in:Draft,Final',
        ]);

        $administrasi->update($validated);

        return redirect()->back()->with('success', "Berita Acara nomor {$administrasi->nomor_surat} berhasil diperbarui.");
    }

    /**
     * Remove the specified Berita Acara.
     */
    public function destroy(SpmbBeritaAcara $administrasi)
    {
        $nomor = $administrasi->nomor_surat;
        $administrasi->delete();

        return redirect()->back()->with('success', "Berita Acara nomor {$nomor} berhasil dihapus.");
    }

    /**
     * Print View for Berita Acara & Lampiran Absensi Petugas Piket SPMB (A4 Portrait, Kop Edge-to-Edge).
     */
    public function print(SpmbBeritaAcara $administrasi)
    {
        $beritaAcara = $administrasi->load([
            'tanggal.kelompok.petugas' => function ($q) {
                $q->orderBy('urutan', 'asc');
            },
            'tanggal.absensis',
            'tanggal.penggantis',
            'tanggal.laporanPetugasBy',
            'kaurUser',
        ]);

        $tanggal = $beritaAcara->tanggal;
        $kelompok = $tanggal?->kelompok;
        $tahunAjaran = $beritaAcara->tahun_ajaran;

        $hariTanggalStr = Carbon::parse($beritaAcara->tanggal_kegiatan)->locale('id')->isoFormat('dddd, D MMMM Y');

        // Build list of officers with attendance record & replacement support
        $petugasAbsensiList = [];
        $rawPetugas = $kelompok ? $kelompok->petugas : collect();
        $absensiMap = $tanggal ? $tanggal->absensis->keyBy('spmb_piket_petugas_id') : collect();
        $penggantiMap = $tanggal ? $tanggal->penggantis->keyBy('spmb_piket_petugas_id') : collect();

        foreach ($rawPetugas as $idx => $p) {
            $abs = $absensiMap->get($p->id);
            $pengganti = $penggantiMap->get($p->id);

            $status = $abs ? $abs->status : 'Belum Absen';
            $waktuAbsen = $abs?->jam_absen ? Carbon::parse($abs->jam_absen)->format('H:i') . ' WIB' : '-';
            $keterangan = $abs?->keterangan ?: '-';

            $namaTampil = $pengganti ? $pengganti->nama_pengganti : $p->nama_petugas;
            $jabatanTampil = $pengganti ? ($pengganti->jabatan_pengganti ?: 'Guru') : ($p->jabatan ?: 'Guru');

            $petugasAbsensiList[] = [
                'no' => $idx + 1,
                'nama' => $namaTampil,
                'nama_asli' => $p->nama_petugas,
                'is_pengganti' => ! is_null($pengganti),
                'alasan_ganti' => $pengganti?->alasan,
                'jabatan' => $jabatanTampil,
                'tugas' => JadwalPiketController::defaultTugasForPetugas($idx + 1, $p->jabatan ?? ''),
                'status' => $status,
                'waktu_absen' => $waktuAbsen,
                'keterangan' => $keterangan,
                'has_eviden' => (bool) $abs?->foto_eviden,
            ];
        }

        // Summary counts
        $totalPetugas = count($petugasAbsensiList);
        $totalHadir = collect($petugasAbsensiList)->whereIn('status', ['Hadir', 'Terlambat'])->count();
        $totalIzinSakit = collect($petugasAbsensiList)->whereIn('status', ['Izin', 'Sakit'])->count();
        $totalAlpa = collect($petugasAbsensiList)->whereIn('status', ['Alpa', 'Belum Absen'])->count();

        // Load Candidates (Lampiran 2)
        $pesertaSeleksis = $tanggal
            ? SpmbPesertaSeleksi::where('spmb_piket_tanggal_id', $tanggal->id)
                ->orderBy('nomor_pendaftaran', 'asc')
                ->get()
            : collect();

        // Load Physical Evidence Images (Lampiran 3) as base64 or storage url
        $convertImageToBase64 = function ($path) {
            if (! $path) {
                return null;
            }
            if (Storage::disk('public')->exists($path)) {
                $data = Storage::disk('public')->get($path);
                $mime = Storage::disk('public')->mimeType($path) ?: 'image/jpeg';
                return 'data:' . $mime . ';base64,' . base64_encode($data);
            }
            if (file_exists(storage_path('app/public/' . $path))) {
                $fullPath = storage_path('app/public/' . $path);
                $data = file_get_contents($fullPath);
                $mime = mime_content_type($fullPath) ?: 'image/jpeg';
                return 'data:' . $mime . ';base64,' . base64_encode($data);
            }
            return null;
        };

        $evidenSiswaBase64 = $convertImageToBase64($tanggal?->foto_eviden_absen_siswa);
        $evidenOrtuBase64 = $convertImageToBase64($tanggal?->foto_eviden_absen_ortu);

        // Load Kop Surat UKK from AppSetting
        $setting = AppSetting::first();
        $kopBase64 = null;
        $kopPath = $setting?->kop_surat_ukk;
        if ($kopPath) {
            if (Storage::disk('public')->exists($kopPath)) {
                $kopData = Storage::disk('public')->get($kopPath);
                $kopMime = Storage::disk('public')->mimeType($kopPath) ?: 'image/png';
                $kopBase64 = 'data:' . $kopMime . ';base64,' . base64_encode($kopData);
            } elseif (file_exists(storage_path('app/public/' . $kopPath))) {
                $fullPath = storage_path('app/public/' . $kopPath);
                $kopData = file_get_contents($fullPath);
                $kopMime = mime_content_type($fullPath) ?: 'image/png';
                $kopBase64 = 'data:' . $kopMime . ';base64,' . base64_encode($kopData);
            } elseif (file_exists(public_path('storage/' . $kopPath))) {
                $fullPath = public_path('storage/' . $kopPath);
                $kopData = file_get_contents($fullPath);
                $kopMime = mime_content_type($fullPath) ?: 'image/png';
                $kopBase64 = 'data:' . $kopMime . ';base64,' . base64_encode($kopData);
            }
        }

        return view('pages.kaur-spmb.administrasi.print', compact(
            'beritaAcara',
            'tanggal',
            'kelompok',
            'tahunAjaran',
            'hariTanggalStr',
            'petugasAbsensiList',
            'totalPetugas',
            'totalHadir',
            'totalIzinSakit',
            'totalAlpa',
            'pesertaSeleksis',
            'evidenSiswaBase64',
            'evidenOrtuBase64',
            'kopBase64'
        ));
    }
}
