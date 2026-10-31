<?php

namespace App\Http\Controllers\KaurSpmb;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\DigitalDocument;
use App\Models\MasterGuru;
use App\Models\SpmbPiketKelompok;
use App\Models\SpmbPiketPetugas;
use App\Models\SpmbPiketPengganti;
use App\Models\SpmbPiketTanggal;
use App\Models\TahunPelajaran;
use App\Models\User;
use App\Models\UserDigitalSignature;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class JadwalPiketController extends Controller
{
    /**
     * Display a listing of the resource.
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

        $viewMode = $request->get('view_mode', 'kelompok'); // 'kelompok' | 'tanggal'
        $search = trim($request->get('search', ''));
        $perPage = (int) $request->get('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        // 2. Fetch Kelompoks with Petugas and Tanggals
        $kelompokQuery = SpmbPiketKelompok::where('tahun_ajaran', $selectedYear)
            ->with([
                'petugas.masterGuru',
                'petugas.user',
                'tanggals' => function ($q) {
                    $q->orderBy('tanggal', 'asc');
                },
            ])
            ->orderBy('urutan', 'asc');

        if ($search !== '') {
            $kelompokQuery->where(function ($q) use ($search) {
                $q->where('nama_kelompok', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhereHas('petugas', function ($sub) use ($search) {
                        $sub->where('nama_petugas', 'like', "%{$search}%");
                    })
                    ->orWhereHas('tanggals', function ($sub) use ($search) {
                        $sub->where('tanggal', 'like', "%{$search}%");
                    });
            });
        }

        $kelompoks = $kelompokQuery->get();

        // 3. Tanggal-Based Query (For Table Mode)
        $tanggalQuery = SpmbPiketTanggal::whereHas('kelompok', function ($q) use ($selectedYear) {
            $q->where('tahun_ajaran', $selectedYear);
        })
        ->with(['kelompok.petugas'])
        ->orderBy('tanggal', 'asc');

        if ($search !== '') {
            $tanggalQuery->where(function ($q) use ($search) {
                $q->where('tanggal', 'like', "%{$search}%")
                    ->orWhere('hari', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhereHas('kelompok', function ($sub) use ($search) {
                        $sub->where('nama_kelompok', 'like', "%{$search}%")
                            ->orWhereHas('petugas', function ($pSub) use ($search) {
                                $pSub->where('nama_petugas', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $paginatedTanggals = $tanggalQuery->paginate($perPage)->appends($request->query());

        // 4. Statistics
        $totalKelompok = SpmbPiketKelompok::where('tahun_ajaran', $selectedYear)->count();
        $totalPetugas = SpmbPiketPetugas::whereHas('kelompok', function ($q) use ($selectedYear) {
            $q->where('tahun_ajaran', $selectedYear);
        })->count();
        $totalHariPiket = SpmbPiketTanggal::whereHas('kelompok', function ($q) use ($selectedYear) {
            $q->where('tahun_ajaran', $selectedYear);
        })->count();

        $today = Carbon::today()->toDateString();
        $piketTerdekat = SpmbPiketTanggal::whereHas('kelompok', function ($q) use ($selectedYear) {
            $q->where('tahun_ajaran', $selectedYear);
        })
        ->where('tanggal', '>=', $today)
        ->with('kelompok.petugas')
        ->orderBy('tanggal', 'asc')
        ->first();

        // 5. Staff Master for modal assignment
        $masterGurus = MasterGuru::orderBy('nama_lengkap', 'asc')->get(['id', 'nama_lengkap', 'nik', 'user_id']);
        $users = User::orderBy('name', 'asc')->get(['id', 'name', 'email']);

        // 6. Tanggal options for Cetak Surat Tugas Modal
        $allTanggals = SpmbPiketTanggal::whereHas('kelompok', function ($q) use ($selectedYear) {
            $q->where('tahun_ajaran', $selectedYear);
        })
        ->with([
            'kelompok.petugas' => function ($q) {
                $q->orderBy('urutan', 'asc');
            },
            'penggantis.masterGuru',
        ])
        ->orderBy('tanggal', 'asc')
        ->get();

        $romawiMap = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];

        $suratTugasOptions = $allTanggals->map(function ($tgl) use ($romawiMap) {
            $carbon = Carbon::parse($tgl->tanggal);
            $blnRomawi = $romawiMap[$carbon->month] ?? 'X';
            $thn = $carbon->year;
            $tglSurat = $carbon->copy()->subDay()->locale('id')->translatedFormat('d F Y');

            $petugas = $tgl->kelompok?->petugas?->map(function ($p, $idx) use ($tgl) {
                $urutan = $idx + 1;
                $pengganti = $tgl->penggantis?->firstWhere('spmb_piket_petugas_id', $p->id);
                $namaEfektif = $pengganti ? $pengganti->nama_pengganti : $p->nama_petugas;
                $jabatanEfektif = $pengganti ? ($pengganti->jabatan_pengganti ?: 'Guru') : ($p->jabatan ?: 'Guru');

                return [
                    'id' => $p->id,
                    'urutan' => $urutan,
                    'nama' => $namaEfektif,
                    'nama_asli' => $p->nama_petugas,
                    'jabatan' => $jabatanEfektif,
                    'tugas' => self::defaultTugasForPetugas($urutan, $p->jabatan ?? ''),
                    'is_replaced' => (bool) $pengganti,
                    'pengganti_id' => $pengganti?->id,
                    'pengganti_nama' => $pengganti?->nama_pengganti,
                    'pengganti_master_guru_id' => $pengganti?->master_guru_id,
                    'pengganti_alasan' => $pengganti?->alasan,
                ];
            })->values() ?? collect();

            return [
                'id' => $tgl->id,
                'tanggal' => $tgl->tanggal->toDateString(),
                'label' => Carbon::parse($tgl->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y') . ' — ' . ($tgl->kelompok?->nama_kelompok ?? 'Kelompok'),
                'kelompok_nama' => $tgl->kelompok?->nama_kelompok ?? '',
                'hari_tanggal' => Carbon::parse($tgl->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y'),
                'nomor_surat' => "1774/SMKTEL-LPG/TATA.01/{$blnRomawi}/{$thn}",
                'tanggal_surat' => $tglSurat,
                'waktu' => $tgl->waktu ?: 'Pukul 07.30 sd 12.00 WIB',
                'tempat' => 'SMK Telkom Lampung',
                'petugas' => $petugas,
            ];
        })->values();

        // 7. Check TTE status for Kepala Sekolah
        $kepsekUser = User::whereHas('roles', fn ($r) => $r->where('name', 'Kepala Sekolah'))
            ->where('name', 'like', '%Dedi Eko%')
            ->first()
            ?? User::role('Kepala Sekolah')->first();
        $kepsekSig = $kepsekUser ? UserDigitalSignature::where('user_id', $kepsekUser->id)->first() : null;
        $isTteReady = (bool) ($kepsekSig?->isReady() && $kepsekSig?->auto_sign_surat_tugas_spmb);

        return view('pages.kaur-spmb.jadwal-piket.index', compact(
            'availableYears',
            'selectedYear',
            'viewMode',
            'search',
            'perPage',
            'kelompoks',
            'paginatedTanggals',
            'totalKelompok',
            'totalPetugas',
            'totalHariPiket',
            'piketTerdekat',
            'masterGurus',
            'users',
            'suratTugasOptions',
            'isTteReady',
            'kepsekUser'
        ));
    }

    /**
     * Store a newly created Kelompok.
     */
    public function storeKelompok(Request $request)
    {
        $validated = $request->validate([
            'tahun_ajaran' => 'required|string|max:20',
            'nama_kelompok' => 'required|string|max:100',
            'waktu' => 'required|string|max:50',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $maxUrutan = SpmbPiketKelompok::where('tahun_ajaran', $validated['tahun_ajaran'])->max('urutan') ?? 0;
        $validated['urutan'] = $maxUrutan + 1;

        SpmbPiketKelompok::create($validated);

        return redirect()->back()->with('success', 'Kelompok ' . $validated['nama_kelompok'] . ' berhasil ditambahkan.');
    }

    /**
     * Update the specified Kelompok.
     */
    public function updateKelompok(Request $request, SpmbPiketKelompok $kelompok)
    {
        $validated = $request->validate([
            'nama_kelompok' => 'required|string|max:100',
            'waktu' => 'required|string|max:50',
            'keterangan' => 'nullable|string|max:255',
            'urutan' => 'nullable|integer|min:1',
        ]);

        $kelompok->update($validated);

        return redirect()->back()->with('success', 'Data kelompok berhasil diperbarui.');
    }

    /**
     * Remove the specified Kelompok.
     */
    public function destroyKelompok(SpmbPiketKelompok $kelompok)
    {
        $nama = $kelompok->nama_kelompok;
        $kelompok->delete();

        return redirect()->back()->with('success', "Kelompok {$nama} beserta seluruh anggota dan jadwal tanggal berhasil dihapus.");
    }

    /**
     * Add a Petugas to a Kelompok.
     */
    public function storePetugas(Request $request, SpmbPiketKelompok $kelompok)
    {
        $validated = $request->validate([
            'source_type' => 'required|in:guru,user,manual',
            'master_guru_id' => 'nullable|exists:master_gurus,id',
            'user_id' => 'nullable|exists:users,id',
            'nama_petugas_manual' => 'nullable|string|max:150',
            'jabatan' => 'nullable|string|max:100',
        ]);

        $namaPetugas = '';
        $guruId = null;
        $userId = null;

        if ($validated['source_type'] === 'guru' && ! empty($validated['master_guru_id'])) {
            $guru = MasterGuru::findOrFail($validated['master_guru_id']);
            $namaPetugas = $guru->nama_lengkap;
            $guruId = $guru->id;
            $userId = $guru->user_id;
        } elseif ($validated['source_type'] === 'user' && ! empty($validated['user_id'])) {
            $user = User::findOrFail($validated['user_id']);
            $namaPetugas = $user->name;
            $userId = $user->id;
        } else {
            $namaPetugas = trim($validated['nama_petugas_manual'] ?? '');
            if (empty($namaPetugas)) {
                return redirect()->back()->with('error', 'Nama petugas wajib diisi.');
            }
        }

        $maxUrutan = $kelompok->petugas()->max('urutan') ?? 0;

        SpmbPiketPetugas::create([
            'spmb_piket_kelompok_id' => $kelompok->id,
            'master_guru_id' => $guruId,
            'user_id' => $userId,
            'nama_petugas' => $namaPetugas,
            'jabatan' => $validated['jabatan'] ?? 'Petugas Piket SPMB',
            'urutan' => $maxUrutan + 1,
        ]);

        return redirect()->back()->with('success', "Petugas {$namaPetugas} berhasil ditambahkan ke {$kelompok->nama_kelompok}.");
    }

    /**
     * Remove a Petugas from a Kelompok.
     */
    public function destroyPetugas(SpmbPiketPetugas $petugas)
    {
        $nama = $petugas->nama_petugas;
        $petugas->delete();

        return redirect()->back()->with('success', "Petugas {$nama} berhasil dihapus.");
    }

    /**
     * Add a single date to Kelompok.
     */
    public function storeTanggal(Request $request, SpmbPiketKelompok $kelompok)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'hari' => 'nullable|string|max:20',
            'waktu' => 'nullable|string|max:50',
            'status' => 'required|string|in:Terjadwal,Terlaksana,Libur,Diganti',
            'keterangan' => 'nullable|string|max:255',
        ]);

        // Default day in Indonesian
        if (empty($validated['hari'])) {
            $validated['hari'] = Carbon::parse($validated['tanggal'])->locale('id')->isoFormat('dddd');
        }

        if (empty($validated['waktu'])) {
            $validated['waktu'] = $kelompok->waktu;
        }

        $exists = SpmbPiketTanggal::where('spmb_piket_kelompok_id', $kelompok->id)
            ->where('tanggal', $validated['tanggal'])
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Tanggal tersebut sudah ada di kelompok ini.');
        }

        $kelompok->tanggals()->create($validated);

        return redirect()->back()->with('success', 'Tanggal piket berhasil ditambahkan.');
    }

    /**
     * Remove a Tanggal.
     */
    public function destroyTanggal(SpmbPiketTanggal $tanggal)
    {
        $tanggal->delete();

        return redirect()->back()->with('success', 'Tanggal piket berhasil dihapus.');
    }

    /**
     * Update status of a Tanggal.
     */
    public function updateStatusTanggal(Request $request, SpmbPiketTanggal $tanggal)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Terjadwal,Terlaksana,Libur,Diganti',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $tanggal->update($validated);

        return redirect()->back()->with('success', 'Status piket berhasil diperbarui.');
    }

    /**
     * Auto generate rotating Saturday dates across groups for an academic year.
     */
    public function generateRotatingDates(Request $request)
    {
        $validated = $request->validate([
            'tahun_ajaran' => 'required|string|max:20',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $kelompoks = SpmbPiketKelompok::where('tahun_ajaran', $validated['tahun_ajaran'])
            ->orderBy('urutan', 'asc')
            ->get();

        if ($kelompoks->isEmpty()) {
            return redirect()->back()->with('error', 'Belum ada kelompok pada tahun ajaran ini. Silakan buat kelompok terlebih dahulu.');
        }

        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);

        // Find first Saturday
        while ($start->dayOfWeek !== Carbon::SATURDAY) {
            $start->addDay();
        }

        $groupCount = $kelompoks->count();
        $groupIndex = 0;
        $insertedCount = 0;

        DB::transaction(function () use ($start, $end, $kelompoks, $groupCount, &$groupIndex, &$insertedCount) {
            $current = $start->copy();
            while ($current->lte($end)) {
                $targetKelompok = $kelompoks[$groupIndex % $groupCount];

                $tglStr = $current->toDateString();
                $exists = SpmbPiketTanggal::where('spmb_piket_kelompok_id', $targetKelompok->id)
                    ->where('tanggal', $tglStr)
                    ->exists();

                if (! $exists) {
                    SpmbPiketTanggal::create([
                        'spmb_piket_kelompok_id' => $targetKelompok->id,
                        'tanggal' => $tglStr,
                        'hari' => 'Sabtu',
                        'waktu' => $targetKelompok->waktu,
                        'status' => 'Terjadwal',
                    ]);
                    $insertedCount++;
                }

                $groupIndex++;
                $current->addWeek();
            }
        });

        return redirect()->back()->with('success', "Berhasil menjadwalkan {$insertedCount} hari Sabtu secara bergiliran untuk {$groupCount} kelompok.");
    }

    /**
     * Print View matching the official SK Surat Tugas Piket SPMB format.
     */
    public function print(Request $request)
    {
        $tahunAjaran = $request->get('tahun_ajaran', '2027/2028');

        $kelompoks = SpmbPiketKelompok::where('tahun_ajaran', $tahunAjaran)
            ->with([
                'petugas' => function ($q) {
                    $q->orderBy('urutan', 'asc');
                },
                'tanggals' => function ($q) {
                    $q->orderBy('tanggal', 'asc');
                },
            ])
            ->orderBy('urutan', 'asc')
            ->get();

        // Print header & signature metadata (can be overridden via query params)
        $meta = [
            'judul' => 'JADWAL PIKET SPMB PEGAWAI SMK TELKOM LAMPUNG',
            'tahun_ajaran' => $tahunAjaran,
            'catatan' => $request->get('catatan', 'Perkelompok Nama Wajib Hadir Sesuai Hari dan Tanggal yang telah dijadwalkan'),
            'tempat_ttd' => $request->get('tempat_ttd', 'Pringsewu'),
            'tanggal_ttd' => $request->get('tanggal_ttd', '21 September 2026'),
            'jabatan_ttd' => $request->get('jabatan_ttd', 'Kepala SMK Telkom Lampung'),
            'nama_kepsek' => $request->get('nama_kepsek', 'Dedi Eko Cahyono, S.Kom'),
            'nip_kepsek' => $request->get('nip_kepsek', '22880011'),
        ];

        return view('pages.kaur-spmb.jadwal-piket.print', compact('kelompoks', 'meta', 'tahunAjaran'));
    }

    /**
     * Map default task distribution matching official SPMB guidelines.
     */
    public static function defaultTugasForPetugas(int $urutan, string $jabatan): string
    {
        if (stripos($jabatan, 'TPA') !== false || stripos($jabatan, 'Tenaga') !== false || $urutan >= 17) {
            return "Menyiapkan Ruangan Tes";
        }

        return match ($urutan) {
            1 => "1) Penerima Tamu\n2) Mendata Casis yang hadir\n3) Mengarahkan Casis dan Wali ke Ruang Tes",
            2, 3, 7 => "Tes Buta Warna",
            4, 5 => "Live Sosial Media Tiktok",
            6, 8, 10, 11, 12 => "Wawancara Orang Tua",
            default => "Wawancara Siswa",
        };
    }

    /**
     * Surat Perintah Tugas view (per-pekan/tanggal) with Kop Surat and Digital Signature.
     */
    public function suratTugas(Request $request)
    {
        $tanggalId = $request->get('tanggal_id');
        $tanggal = null;

        if ($tanggalId) {
            $tanggal = SpmbPiketTanggal::with(['kelompok.petugas' => function ($q) {
                $q->orderBy('urutan', 'asc');
            }])->find($tanggalId);
        }

        if (! $tanggal) {
            $tanggal = SpmbPiketTanggal::with(['kelompok.petugas' => function ($q) {
                $q->orderBy('urutan', 'asc');
            }])->orderBy('tanggal', 'asc')->firstOrFail();
        }

        $kelompok = $tanggal->kelompok;
        $tahunAjaran = $kelompok?->tahun_ajaran ?: '2027/2028';

        $carbonTgl = Carbon::parse($tanggal->tanggal);
        $romawiMap = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];
        $blnRomawi = $romawiMap[$carbonTgl->month] ?? 'X';
        $thn = $carbonTgl->year;

        $defaultNomor = "1774/SMKTEL-LPG/TATA.01/{$blnRomawi}/{$thn}";
        $nomorSurat = $request->get('nomor_surat', $defaultNomor);

        $defaultTglSurat = $carbonTgl->copy()->subDay()->locale('id')->translatedFormat('d F Y');
        $tanggalSurat = $request->get('tanggal_surat', $defaultTglSurat);

        $tempat = $request->get('tempat', 'SMK Telkom Lampung');
        $waktu = $request->get('waktu', $tanggal->waktu ?: 'Pukul 07.30 sd 12.00 WIB');
        $hariTanggalStr = Carbon::parse($tanggal->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y');

        // Petugas and their customized/default tasks (with replacement check)
        $customTugas = $request->get('tugas', []);
        $petugasList = [];
        $rawPetugas = $kelompok ? $kelompok->petugas : collect();
        $penggantiMap = SpmbPiketPengganti::where('spmb_piket_tanggal_id', $tanggal->id)
            ->get()
            ->keyBy('spmb_piket_petugas_id');

        foreach ($rawPetugas as $idx => $p) {
            $urutan = $idx + 1;
            $tugasText = $customTugas[$p->id] ?? self::defaultTugasForPetugas($urutan, $p->jabatan ?? '');
            
            $pengganti = $penggantiMap->get($p->id);
            $nama = $pengganti ? $pengganti->nama_pengganti : $p->nama_petugas;
            $jabatan = $pengganti ? ($pengganti->jabatan_pengganti ?: 'Guru') : ($p->jabatan ?: 'Guru');

            $petugasList[] = [
                'no' => $urutan,
                'nama' => $nama,
                'jabatan' => $jabatan,
                'tugas' => $tugasText,
                'is_replaced' => (bool) $pengganti,
                'nama_asli' => $p->nama_petugas,
            ];
        }

        // Kop Surat UKK from AppSetting
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

        // Kepala Sekolah User
        $kepsekUser = User::whereHas('roles', fn ($r) => $r->where('name', 'Kepala Sekolah'))
            ->where('name', 'like', '%Dedi Eko%')
            ->first()
            ?? User::role('Kepala Sekolah')->first();

        $namaKepsek = $kepsekUser ? $kepsekUser->name : 'Dedi Eko Cahyono, S.Kom., Gr.';
        $nipKepsek = '22880011';
        $jabatanKepsek = 'Kepala Sekolah';

        // Auto Digital Sign & Archive
        $sig = $kepsekUser ? UserDigitalSignature::where('user_id', $kepsekUser->id)->first() : null;
        $digitalDoc = null;
        $qrCodeBase64 = null;

        if ($kepsekUser && $sig?->isReady() && $sig->auto_sign_surat_tugas_spmb) {
            $docTitle = "Surat Perintah Tugas Piket SPMB - {$hariTanggalStr}";

            $digitalDoc = DigitalDocument::where('document_type', 'SURAT_TUGAS_PIKET_SPMB')
                ->where('reference_id', $tanggal->id)
                ->where('is_valid', true)
                ->latest()
                ->first();

            if (! $digitalDoc) {
                $hashParts = [
                    'SURAT_TUGAS_PIKET_SPMB',
                    (string) $tanggal->id,
                    $nomorSurat,
                    (string) $tanggal->tanggal->toDateString(),
                    $namaKepsek,
                ];

                $digitalDoc = DigitalDocument::autoSign(
                    $kepsekUser,
                    'SURAT_TUGAS_PIKET_SPMB',
                    $docTitle,
                    $tanggal->id,
                    $hashParts
                );
            }

            if ($digitalDoc) {
                $verifyUrl = route('verifikasi.dokumen', $digitalDoc->token);
                $qrSvg = QrCode::format('svg')
                    ->size(95)
                    ->margin(0)
                    ->generate($verifyUrl);
                $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
            }
        }

        return view('pages.kaur-spmb.jadwal-piket.surat-tugas', compact(
            'tanggal',
            'kelompok',
            'tahunAjaran',
            'nomorSurat',
            'tanggalSurat',
            'hariTanggalStr',
            'tempat',
            'waktu',
            'petugasList',
            'kopBase64',
            'namaKepsek',
            'nipKepsek',
            'jabatanKepsek',
            'digitalDoc',
            'qrCodeBase64'
        ));
    }

    /**
     * Store or update replacement teacher for a specific duty date.
     */
    public function storeOrUpdatePengganti(Request $request)
    {
        $validated = $request->validate([
            'spmb_piket_tanggal_id' => 'required|exists:spmb_piket_tanggals,id',
            'spmb_piket_petugas_id' => 'required|exists:spmb_piket_petugas,id',
            'master_guru_id' => 'required|exists:master_gurus,id',
            'alasan' => 'nullable|string|max:255',
        ]);

        $guru = MasterGuru::with('user')->findOrFail($validated['master_guru_id']);
        $petugas = SpmbPiketPetugas::findOrFail($validated['spmb_piket_petugas_id']);

        $pengganti = SpmbPiketPengganti::updateOrCreate(
            [
                'spmb_piket_tanggal_id' => $validated['spmb_piket_tanggal_id'],
                'spmb_piket_petugas_id' => $petugas->id,
            ],
            [
                'master_guru_id' => $guru->id,
                'user_id' => $guru->user_id,
                'nama_pengganti' => $guru->nama_lengkap,
                'jabatan_pengganti' => 'Guru',
                'alasan' => $validated['alasan'] ?? 'Berhalangan hadir',
                'created_by' => auth()->id(),
            ]
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Petugas {$petugas->nama_petugas} berhasil digantikan oleh {$guru->nama_lengkap}.",
                'pengganti' => $pengganti,
            ]);
        }

        return redirect()->back()->with('success', "Petugas {$petugas->nama_petugas} berhasil digantikan oleh {$guru->nama_lengkap} pada pekan ini.");
    }

    /**
     * Remove replacement and restore original officer.
     */
    public function destroyPengganti(SpmbPiketPengganti $pengganti, Request $request)
    {
        $petugasAsliNama = $pengganti->petugasAsli?->nama_petugas ?? 'Petugas';
        $pengganti->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Penggantian untuk {$petugasAsliNama} berhasil dibatalkan.",
            ]);
        }

        return redirect()->back()->with('success', "Penggantian untuk {$petugasAsliNama} berhasil dibatalkan, petugas kembali ke semula.");
    }
}
