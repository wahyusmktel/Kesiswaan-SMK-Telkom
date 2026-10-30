<?php

namespace App\Http\Controllers\KaurSpmb;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\MasterGuru;
use App\Models\SpmbPiketKelompok;
use App\Models\SpmbPiketPetugas;
use App\Models\SpmbPiketTanggal;
use App\Models\TahunPelajaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'users'
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
}
