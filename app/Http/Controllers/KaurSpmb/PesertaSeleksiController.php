<?php

namespace App\Http\Controllers\KaurSpmb;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\SpmbPesertaSeleksi;
use App\Models\SpmbPiketKelompok;
use App\Models\SpmbPiketTanggal;
use App\Models\TahunPelajaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class PesertaSeleksiController extends Controller
{
    /**
     * Display a listing of SPMB Selection Candidates per Scheduled Picket Date.
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

        $tanggalId = $request->get('tanggal_id');
        $jurusan = $request->get('jurusan');
        $statusSiswa = $request->get('status_siswa');
        $search = trim($request->get('search', ''));
        $perPage = (int) $request->get('per_page', 10);
        if ($perPage < 5 || $perPage > 100) {
            $perPage = 10;
        }

        // 2. Query Peserta Seleksi
        $query = SpmbPesertaSeleksi::where('tahun_ajaran', $selectedYear)
            ->with(['tanggal.kelompok', 'user']);

        if (! empty($tanggalId)) {
            $query->where('spmb_piket_tanggal_id', $tanggalId);
        }

        if (! empty($jurusan)) {
            $query->where('jurusan_pilihan', $jurusan);
        }

        if (! empty($statusSiswa)) {
            $query->where('status_kehadiran_siswa', $statusSiswa);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_pendaftaran', 'like', "%{$search}%")
                    ->orWhere('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('asal_sekolah', 'like', "%{$search}%")
                    ->orWhere('nama_orang_tua', 'like', "%{$search}%")
                    ->orWhere('no_wa_siswa', 'like', "%{$search}%")
                    ->orWhere('no_wa_orang_tua', 'like', "%{$search}%");
            });
        }

        $pesertas = $query->orderBy('nomor_pendaftaran', 'asc')
            ->paginate($perPage)
            ->withQueryString();

        // 3. Stats
        $baseStats = SpmbPesertaSeleksi::where('tahun_ajaran', $selectedYear);
        if (! empty($tanggalId)) {
            $baseStats->where('spmb_piket_tanggal_id', $tanggalId);
        }

        $totalPeserta = (clone $baseStats)->count();
        $totalHadirSiswa = (clone $baseStats)->where('status_kehadiran_siswa', 'Hadir')->count();
        $totalHadirOrtu = (clone $baseStats)->where('status_kehadiran_ortu', 'Hadir')->count();
        $totalTerjadwal = (clone $baseStats)->where('status_kehadiran_siswa', 'Terjadwal')->count();

        // 4. Tanggal Options (Saturday Dates for the Academic Year)
        $tanggalOptions = SpmbPiketTanggal::whereHas('kelompok', fn ($q) => $q->where('tahun_ajaran', $selectedYear))
            ->with('kelompok')
            ->orderBy('tanggal', 'asc')
            ->get()
            ->map(function ($tgl) {
                $carbon = Carbon::parse($tgl->tanggal);
                $pesertaCount = SpmbPesertaSeleksi::where('spmb_piket_tanggal_id', $tgl->id)->count();
                return [
                    'id' => $tgl->id,
                    'tanggal' => $tgl->tanggal->toDateString(),
                    'label' => $carbon->locale('id')->isoFormat('dddd, D MMMM Y') . ' — ' . ($tgl->kelompok?->nama_kelompok ?? 'Kelompok') . " ({$pesertaCount} Calon Siswa)",
                    'hari_tanggal' => $carbon->locale('id')->isoFormat('dddd, D MMMM Y'),
                    'kelompok_nama' => $tgl->kelompok?->nama_kelompok ?? '',
                    'peserta_count' => $pesertaCount,
                ];
            });

        // 5. Jurusan Master
        $jurusanList = [
            'Teknik Jaringan Akses',
            'Rekayasa Perangkat Lunak',
            'Teknik Komputer dan Jaringan',
            'Animasi',
        ];

        // 6. Next auto register number suggestion
        $prefix = 'SPMB' . substr(str_replace('/', '', $selectedYear), 2, 2) . '-';
        $maxNum = SpmbPesertaSeleksi::where('tahun_ajaran', $selectedYear)
            ->where('nomor_pendaftaran', 'like', "{$prefix}%")
            ->count();
        $suggestedNoPendaftaran = $prefix . str_pad($maxNum + 1, 3, '0', STR_PAD_LEFT);

        return view('pages.kaur-spmb.peserta-seleksi.index', compact(
            'availableYears',
            'selectedYear',
            'tanggalId',
            'jurusan',
            'statusSiswa',
            'search',
            'perPage',
            'pesertas',
            'totalPeserta',
            'totalHadirSiswa',
            'totalHadirOrtu',
            'totalTerjadwal',
            'tanggalOptions',
            'jurusanList',
            'suggestedNoPendaftaran'
        ));
    }

    /**
     * Store a newly created candidate and auto-generate their login account.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'spmb_piket_tanggal_id' => 'required|exists:spmb_piket_tanggals,id',
            'tahun_ajaran' => 'required|string|max:20',
            'nomor_pendaftaran' => 'required|string|max:50|unique:spmb_peserta_seleksis,nomor_pendaftaran',
            'nama_lengkap' => 'required|string|max:150',
            'nisn' => 'nullable|string|max:20',
            'nik' => 'nullable|string|max:20',
            'jenis_kelamin' => 'required|in:L,P',
            'asal_sekolah' => 'nullable|string|max:150',
            'jurusan_pilihan' => 'required|string|max:100',
            'jurusan_pilihan_2' => 'nullable|string|max:100',
            'no_wa_siswa' => 'nullable|string|max:30',
            'nama_orang_tua' => 'nullable|string|max:150',
            'hubungan_wali' => 'nullable|string|max:50',
            'no_wa_orang_tua' => 'nullable|string|max:30',
            'custom_password' => 'nullable|string|min:4',
            'catatan' => 'nullable|string',
        ]);

        // Auto-generate student user login credentials
        $cleanNo = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($validated['nomor_pendaftaran']));
        $username = $cleanNo;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $cleanNo . $counter;
            $counter++;
        }

        $email = $username . '@spmb.smktelkom-lpg.sch.id';
        while (User::where('email', $email)->exists()) {
            $email = $username . rand(10, 99) . '@spmb.smktelkom-lpg.sch.id';
        }

        $rawPassword = ! empty($validated['custom_password'])
            ? trim($validated['custom_password'])
            : strtolower($validated['nomor_pendaftaran']);

        DB::beginTransaction();
        try {
            // 1. Create User Account
            $user = User::create([
                'name' => $validated['nama_lengkap'],
                'username' => $username,
                'email' => $email,
                'password' => Hash::make($rawPassword),
            ]);

            // Assign role 'Calon Siswa'
            $roleCasis = Role::firstOrCreate(['name' => 'Calon Siswa', 'guard_name' => 'web']);
            $user->assignRole($roleCasis);

            // 2. Create Peserta Seleksi
            $peserta = SpmbPesertaSeleksi::create([
                'user_id' => $user->id,
                'spmb_piket_tanggal_id' => $validated['spmb_piket_tanggal_id'],
                'tahun_ajaran' => $validated['tahun_ajaran'],
                'nomor_pendaftaran' => strtoupper($validated['nomor_pendaftaran']),
                'nama_lengkap' => $validated['nama_lengkap'],
                'nisn' => $validated['nisn'] ?? null,
                'nik' => $validated['nik'] ?? null,
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'asal_sekolah' => $validated['asal_sekolah'] ?? null,
                'jurusan_pilihan' => $validated['jurusan_pilihan'],
                'jurusan_pilihan_2' => $validated['jurusan_pilihan_2'] ?? null,
                'no_wa_siswa' => $validated['no_wa_siswa'] ?? null,
                'nama_orang_tua' => $validated['nama_orang_tua'] ?? null,
                'hubungan_wali' => $validated['hubungan_wali'] ?? 'Orang Tua',
                'no_wa_orang_tua' => $validated['no_wa_orang_tua'] ?? null,
                'raw_password' => $rawPassword,
                'status_kehadiran_siswa' => 'Terjadwal',
                'status_kehadiran_ortu' => 'Terjadwal',
                'status_seleksi' => 'Terjadwal',
                'catatan' => $validated['catatan'] ?? null,
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->back()->with('success', "Calon siswa {$peserta->nama_lengkap} berhasil didaftarkan. Akun login dibuat: Username [ {$username} ] | Password [ {$rawPassword} ].");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mendaftarkan calon siswa: ' . $e->getMessage());
        }
    }

    /**
     * Update Candidate Data.
     */
    public function update(Request $request, SpmbPesertaSeleksi $pesertaSeleksi)
    {
        $validated = $request->validate([
            'spmb_piket_tanggal_id' => 'required|exists:spmb_piket_tanggals,id',
            'nama_lengkap' => 'required|string|max:150',
            'nisn' => 'nullable|string|max:20',
            'nik' => 'nullable|string|max:20',
            'jenis_kelamin' => 'required|in:L,P',
            'asal_sekolah' => 'nullable|string|max:150',
            'jurusan_pilihan' => 'required|string|max:100',
            'jurusan_pilihan_2' => 'nullable|string|max:100',
            'no_wa_siswa' => 'nullable|string|max:30',
            'nama_orang_tua' => 'nullable|string|max:150',
            'hubungan_wali' => 'nullable|string|max:50',
            'no_wa_orang_tua' => 'nullable|string|max:30',
            'status_kehadiran_siswa' => 'required|in:Terjadwal,Hadir,Tidak Hadir,Menunggu',
            'status_kehadiran_ortu' => 'required|in:Terjadwal,Hadir,Tidak Hadir,Menunggu',
            'custom_password' => 'nullable|string|min:4',
            'catatan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update associated user if present
            if ($pesertaSeleksi->user) {
                $userUpdates = ['name' => $validated['nama_lengkap']];
                if (! empty($validated['custom_password'])) {
                    $userUpdates['password'] = Hash::make($validated['custom_password']);
                    $pesertaSeleksi->raw_password = $validated['custom_password'];
                }
                $pesertaSeleksi->user->update($userUpdates);
            }

            // Kehadiran timestamp
            if ($validated['status_kehadiran_siswa'] === 'Hadir' && $pesertaSeleksi->status_kehadiran_siswa !== 'Hadir') {
                $pesertaSeleksi->waktu_kehadiran_siswa = now();
            }
            if ($validated['status_kehadiran_ortu'] === 'Hadir' && $pesertaSeleksi->status_kehadiran_ortu !== 'Hadir') {
                $pesertaSeleksi->waktu_kehadiran_ortu = now();
            }

            $pesertaSeleksi->update($validated);

            DB::commit();

            return redirect()->back()->with('success', "Data calon siswa {$pesertaSeleksi->nama_lengkap} berhasil diperbarui.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Remove Candidate & Account.
     */
    public function destroy(SpmbPesertaSeleksi $pesertaSeleksi)
    {
        $nama = $pesertaSeleksi->nama_lengkap;
        $user = $pesertaSeleksi->user;

        $pesertaSeleksi->delete();
        if ($user) {
            $user->delete();
        }

        return redirect()->back()->with('success', "Calon siswa {$nama} beserta akun loginnya berhasil dihapus.");
    }

    /**
     * Quick Toggle Kehadiran (Siswa / Orang Tua).
     */
    public function updateKehadiran(Request $request, SpmbPesertaSeleksi $pesertaSeleksi)
    {
        $validated = $request->validate([
            'target' => 'required|in:siswa,ortu',
            'status' => 'required|in:Terjadwal,Hadir,Tidak Hadir,Menunggu',
        ]);

        if ($validated['target'] === 'siswa') {
            $pesertaSeleksi->status_kehadiran_siswa = $validated['status'];
            $pesertaSeleksi->waktu_kehadiran_siswa = ($validated['status'] === 'Hadir') ? now() : null;
        } else {
            $pesertaSeleksi->status_kehadiran_ortu = $validated['status'];
            $pesertaSeleksi->waktu_kehadiran_ortu = ($validated['status'] === 'Hadir') ? now() : null;
        }

        $pesertaSeleksi->save();

        return redirect()->back()->with('success', "Status kehadiran {$validated['target']} ({$pesertaSeleksi->nama_lengkap}) berhasil diubah menjadi {$validated['status']}.");
    }

    /**
     * Print View: Daftar Hadir Peserta Calon Siswa Seleksi (A4 Portrait, Kop Edge-to-Edge).
     */
    public function cetakHadirSiswa(Request $request)
    {
        $tanggalId = $request->get('tanggal_id');
        $tahunAjaran = $request->get('tahun_ajaran', '2027/2028');

        $tanggal = SpmbPiketTanggal::with('kelompok')->findOrFail($tanggalId);
        $hariTanggalStr = Carbon::parse($tanggal->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y');

        $pesertas = SpmbPesertaSeleksi::where('spmb_piket_tanggal_id', $tanggalId)
            ->orderBy('nomor_pendaftaran', 'asc')
            ->get();

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

        $kaurUser = User::role('Kaur SPMB')->first() ?? Auth::user();
        $namaKaur = $kaurUser?->name ?? 'Wahyu Rahmat Hidayat, S.Kom., Gr.';

        return view('pages.kaur-spmb.peserta-seleksi.cetak-hadir-siswa', compact(
            'tanggal',
            'tahunAjaran',
            'hariTanggalStr',
            'pesertas',
            'kopBase64',
            'namaKaur'
        ));
    }

    /**
     * Print View: Daftar Hadir Orang Tua / Wali Pendamping (A4 Portrait, Kop Edge-to-Edge).
     */
    public function cetakHadirOrtu(Request $request)
    {
        $tanggalId = $request->get('tanggal_id');
        $tahunAjaran = $request->get('tahun_ajaran', '2027/2028');

        $tanggal = SpmbPiketTanggal::with('kelompok')->findOrFail($tanggalId);
        $hariTanggalStr = Carbon::parse($tanggal->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y');

        $pesertas = SpmbPesertaSeleksi::where('spmb_piket_tanggal_id', $tanggalId)
            ->orderBy('nomor_pendaftaran', 'asc')
            ->get();

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

        $kaurUser = User::role('Kaur SPMB')->first() ?? Auth::user();
        $namaKaur = $kaurUser?->name ?? 'Wahyu Rahmat Hidayat, S.Kom., Gr.';

        return view('pages.kaur-spmb.peserta-seleksi.cetak-hadir-ortu', compact(
            'tanggal',
            'tahunAjaran',
            'hariTanggalStr',
            'pesertas',
            'kopBase64',
            'namaKaur'
        ));
    }
}
