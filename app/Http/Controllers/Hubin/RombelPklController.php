<?php

namespace App\Http\Controllers\Hubin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MasterGuru;
use App\Models\MasterSiswa;
use App\Models\PrakerinIndustri;
use App\Models\PrakerinPembimbing;
use App\Models\PrakerinPenempatan;
use App\Models\PrakerinRombel;
use App\Models\PrakerinSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class RombelPklController extends Controller
{
    /**
     * Tampilkan daftar Rombel PKL beserta metrik KPI dan filter.
     */
    public function index(Request $request)
    {
        // 1. KPI Metrik
        $totalRombel = PrakerinRombel::count();
        $totalSiswaDitempatkan = PrakerinPenempatan::whereNotNull('prakerin_rombel_id')
            ->distinct('master_siswa_id')
            ->count('master_siswa_id');

        $mappedStudentIds = PrakerinPenempatan::whereNotNull('prakerin_rombel_id')->pluck('master_siswa_id');

        $totalSiswaXiiBelumDitempatkan = MasterSiswa::whereHas('rombels.kelas', function ($q) {
            $q->where('nama_kelas', 'like', 'XII%')->orWhere('nama_kelas', 'like', '12%');
        })->whereNotIn('id', $mappedStudentIds)->count();

        $totalIndustriDigunakan = PrakerinRombel::distinct('prakerin_industri_id')->count('prakerin_industri_id');

        // 2. Query Data Rombel PKL
        $query = PrakerinRombel::with([
            'industri',
            'pembimbingInternal.guru.user',
            'pembimbingExternal',
        ])->withCount('penempatans');

        // Pencarian
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('nama_rombel', 'like', "%{$s}%")
                    ->orWhereHas('industri', fn ($sub) => $sub->where('nama_industri', 'like', "%{$s}%"))
                    ->orWhereHas('pembimbingInternal', fn ($sub) => $sub->where('nama', 'like', "%{$s}%"))
                    ->orWhereHas('pembimbingExternal', fn ($sub) => $sub->where('nama', 'like', "%{$s}%"));
            });
        }

        // Filter Industri
        if ($request->filled('prakerin_industri_id')) {
            $query->where('prakerin_industri_id', $request->prakerin_industri_id);
        }

        // Filter Status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $rombels = $query->latest()->paginate(10)->withQueryString();

        // 3. Opsi Dropdown untuk Form Modal
        $industriList = PrakerinIndustri::orderBy('nama_industri')->get();
        $guruList = MasterGuru::with('user')->orderBy('nama_lengkap')->get();
        $externalList = PrakerinPembimbing::with('industri')
            ->where('tipe', 'external')
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        return view('pages.hubin.rombel.index', compact(
            'rombels',
            'totalRombel',
            'totalSiswaDitempatkan',
            'totalSiswaXiiBelumDitempatkan',
            'totalIndustriDigunakan',
            'industriList',
            'guruList',
            'externalList'
        ));
    }

    /**
     * Simpan data Rombel PKL baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_rombel' => 'required|string|max:255',
            'prakerin_industri_id' => 'required|exists:prakerin_industris,id',
            'master_guru_id' => 'required|exists:master_gurus,id',
            'pembimbing_external_id' => 'nullable|exists:prakerin_pembimbings,id',
            'pembimbing_external_nama' => 'nullable|string|max:255',
            'pembimbing_external_telepon' => 'nullable|string|max:30',
            'pembimbing_external_jabatan' => 'nullable|string|max:100',
            'gunakan_periode_kustom' => 'nullable|boolean',
            'tanggal_mulai' => 'required_if:gunakan_periode_kustom,1|nullable|date',
            'tanggal_selesai' => 'required_if:gunakan_periode_kustom,1|nullable|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:draft,aktif,selesai',
        ], [
            'nama_rombel.required' => 'Nama rombel PKL wajib diisi.',
            'prakerin_industri_id.required' => 'Pilih industri mitra PKL.',
            'master_guru_id.required' => 'Pilih guru sebagai pembimbing internal.',
            'tanggal_mulai.required_if' => 'Tanggal mulai wajib diisi jika periode kustom diaktifkan.',
            'tanggal_selesai.required_if' => 'Tanggal selesai wajib diisi jika periode kustom diaktifkan.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ]);

        // 1. Hubungkan / buat Pembimbing Internal dari MasterGuru
        $guru = MasterGuru::findOrFail($validated['master_guru_id']);
        $pembimbingInternal = PrakerinPembimbing::firstOrCreate(
            ['tipe' => 'internal', 'master_guru_id' => $guru->id],
            [
                'nama' => $guru->nama_lengkap,
                'telepon' => $guru->user?->phone ?? null,
                'email' => $guru->user?->email ?? null,
                'is_active' => true,
            ]
        );

        // 2. Hubungkan / buat Pembimbing Eksternal
        $pembimbingExternalId = null;
        if (!empty($validated['pembimbing_external_id'])) {
            $pembimbingExternalId = $validated['pembimbing_external_id'];
        } elseif (!empty($validated['pembimbing_external_nama'])) {
            $external = PrakerinPembimbing::firstOrCreate(
                [
                    'tipe' => 'external',
                    'prakerin_industri_id' => $validated['prakerin_industri_id'],
                    'nama' => trim($validated['pembimbing_external_nama']),
                ],
                [
                    'telepon' => $validated['pembimbing_external_telepon'] ?? null,
                    'jabatan' => $validated['pembimbing_external_jabatan'] ?? 'Pembimbing Industri',
                    'is_active' => true,
                ]
            );
            $pembimbingExternalId = $external->id;
        }

        $gunakanPeriode = $request->boolean('gunakan_periode_kustom');

        PrakerinRombel::create([
            'nama_rombel' => $validated['nama_rombel'],
            'prakerin_industri_id' => $validated['prakerin_industri_id'],
            'pembimbing_internal_id' => $pembimbingInternal->id,
            'pembimbing_external_id' => $pembimbingExternalId,
            'gunakan_periode_kustom' => $gunakanPeriode,
            'tanggal_mulai' => $gunakanPeriode ? $validated['tanggal_mulai'] : null,
            'tanggal_selesai' => $gunakanPeriode ? $validated['tanggal_selesai'] : null,
            'status' => $validated['status'],
        ]);

        Alert::success('Berhasil', 'Rombel PKL berhasil dibuat.');

        return redirect()->route('hubin.rombel-pkl.index');
    }

    /**
     * Perbarui data Rombel PKL.
     */
    public function update(Request $request, PrakerinRombel $rombel)
    {
        $validated = $request->validate([
            'nama_rombel' => 'required|string|max:255',
            'prakerin_industri_id' => 'required|exists:prakerin_industris,id',
            'master_guru_id' => 'required|exists:master_gurus,id',
            'pembimbing_external_id' => 'nullable|exists:prakerin_pembimbings,id',
            'pembimbing_external_nama' => 'nullable|string|max:255',
            'pembimbing_external_telepon' => 'nullable|string|max:30',
            'pembimbing_external_jabatan' => 'nullable|string|max:100',
            'gunakan_periode_kustom' => 'nullable|boolean',
            'tanggal_mulai' => 'required_if:gunakan_periode_kustom,1|nullable|date',
            'tanggal_selesai' => 'required_if:gunakan_periode_kustom,1|nullable|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:draft,aktif,selesai',
        ]);

        // 1. Hubungkan / buat Pembimbing Internal dari MasterGuru
        $guru = MasterGuru::findOrFail($validated['master_guru_id']);
        $pembimbingInternal = PrakerinPembimbing::firstOrCreate(
            ['tipe' => 'internal', 'master_guru_id' => $guru->id],
            [
                'nama' => $guru->nama_lengkap,
                'telepon' => $guru->user?->phone ?? null,
                'email' => $guru->user?->email ?? null,
                'is_active' => true,
            ]
        );

        // 2. Hubungkan / buat Pembimbing Eksternal
        $pembimbingExternalId = null;
        if (!empty($validated['pembimbing_external_id'])) {
            $pembimbingExternalId = $validated['pembimbing_external_id'];
        } elseif (!empty($validated['pembimbing_external_nama'])) {
            $external = PrakerinPembimbing::firstOrCreate(
                [
                    'tipe' => 'external',
                    'prakerin_industri_id' => $validated['prakerin_industri_id'],
                    'nama' => trim($validated['pembimbing_external_nama']),
                ],
                [
                    'telepon' => $validated['pembimbing_external_telepon'] ?? null,
                    'jabatan' => $validated['pembimbing_external_jabatan'] ?? 'Pembimbing Industri',
                    'is_active' => true,
                ]
            );
            $pembimbingExternalId = $external->id;
        }

        $gunakanPeriode = $request->boolean('gunakan_periode_kustom');

        $rombel->update([
            'nama_rombel' => $validated['nama_rombel'],
            'prakerin_industri_id' => $validated['prakerin_industri_id'],
            'pembimbing_internal_id' => $pembimbingInternal->id,
            'pembimbing_external_id' => $pembimbingExternalId,
            'gunakan_periode_kustom' => $gunakanPeriode,
            'tanggal_mulai' => $gunakanPeriode ? $validated['tanggal_mulai'] : null,
            'tanggal_selesai' => $gunakanPeriode ? $validated['tanggal_selesai'] : null,
            'status' => $validated['status'],
        ]);

        // Perbarui data penempatan siswa di bawah rombel ini jika ada
        PrakerinPenempatan::where('prakerin_rombel_id', $rombel->id)->update([
            'prakerin_industri_id' => $validated['prakerin_industri_id'],
            'master_guru_id' => $guru->id,
            'nama_pembimbing_industri' => $rombel->pembimbingExternal?->nama ?? '-',
        ]);

        Alert::success('Berhasil', 'Data Rombel PKL berhasil diperbarui.');

        return redirect()->route('hubin.rombel-pkl.index');
    }

    /**
     * Hapus data Rombel PKL beserta relasi penempatannya.
     */
    public function destroy(PrakerinRombel $rombel)
    {
        $rombel->penempatans()->delete();
        $rombel->delete();

        Alert::success('Berhasil', 'Rombel PKL berhasil dihapus.');

        return back();
    }

    /**
     * Halaman Mapping Siswa Khusus Kelas XII ke Rombel PKL.
     */
    public function mapping(Request $request, PrakerinRombel $rombel)
    {
        $rombel->load([
            'industri',
            'pembimbingInternal.guru',
            'pembimbingExternal',
            'penempatans.siswa.rombels.kelas',
        ]);

        // Daftar kelas XII saja
        $kelasXii = Kelas::where(function ($q) {
            $q->where('nama_kelas', 'like', 'XII%')
                ->orWhere('nama_kelas', 'like', '12%');
        })->orderBy('nama_kelas')->get();

        // Ambil semua ID siswa yang sudah masuk rombel PKL manapun
        $mappedIds = PrakerinPenempatan::whereNotNull('prakerin_rombel_id')->pluck('master_siswa_id');

        // Query siswa khusus Kelas XII yang BELUM masuk rombel PKL manapun
        $siswaQuery = MasterSiswa::with(['rombels.kelas', 'user'])
            ->whereNotIn('id', $mappedIds)
            ->whereHas('rombels.kelas', function ($q) {
                $q->where('nama_kelas', 'like', 'XII%')
                    ->orWhere('nama_kelas', 'like', '12%');
            });

        // Filter Kelas XII tertentu
        if ($request->filled('kelas_id')) {
            $siswaQuery->whereHas('rombels', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        // Pencarian Nama atau NIS
        if ($request->filled('search')) {
            $search = trim($request->search);
            $siswaQuery->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $siswa = $siswaQuery->orderBy('nama_lengkap')->paginate(15)->withQueryString();

        return view('pages.hubin.rombel.mapping', compact('rombel', 'kelasXii', 'siswa'));
    }

    /**
     * Tambahkan siswa terpilih ke Rombel PKL.
     */
    public function storeMapping(Request $request, PrakerinRombel $rombel)
    {
        $data = $request->validate([
            'master_siswa_ids' => 'required|array|min:1',
            'master_siswa_ids.*' => 'exists:master_siswa,id',
        ], [
            'master_siswa_ids.required' => 'Pilih minimal satu siswa untuk ditambahkan ke rombel PKL.',
            'master_siswa_ids.min' => 'Pilih minimal satu siswa.',
        ]);

        $tanggalMulai = $rombel->tanggal_mulai?->toDateString()
            ?? PrakerinSetting::first()?->tanggal_mulai?->toDateString()
            ?? now()->toDateString();

        $tanggalSelesai = $rombel->tanggal_selesai?->toDateString()
            ?? PrakerinSetting::first()?->tanggal_selesai?->toDateString()
            ?? now()->addMonths(3)->toDateString();

        $namaPembimbingIndustri = $rombel->pembimbingExternal?->nama
            ?? $rombel->industri?->nama_pic
            ?? '-';

        $guruId = $rombel->pembimbingInternal?->master_guru_id
            ?? MasterGuru::where('is_active', true)->first()?->id
            ?? MasterGuru::first()?->id;

        DB::transaction(function () use ($data, $rombel, $tanggalMulai, $tanggalSelesai, $namaPembimbingIndustri, $guruId) {
            foreach ($data['master_siswa_ids'] as $siswaId) {
                PrakerinPenempatan::updateOrCreate(
                    ['master_siswa_id' => $siswaId],
                    [
                        'prakerin_rombel_id' => $rombel->id,
                        'prakerin_industri_id' => $rombel->prakerin_industri_id,
                        'master_guru_id' => $guruId,
                        'nama_pembimbing_industri' => $namaPembimbingIndustri,
                        'tanggal_mulai' => $tanggalMulai,
                        'tanggal_selesai' => $tanggalSelesai,
                        'status' => 'aktif',
                    ]
                );
            }
        });

        Alert::success('Berhasil', count($data['master_siswa_ids']) . ' siswa berhasil ditambahkan ke rombel PKL.');

        return back();
    }

    /**
     * Lepas siswa dari Rombel PKL (siswa kembali tersedia untuk dimapping).
     */
    public function removeMapping(PrakerinRombel $rombel, PrakerinPenempatan $penempatan)
    {
        abort_unless($penempatan->prakerin_rombel_id === $rombel->id, 404);

        $penempatan->delete();

        Alert::success('Berhasil', 'Siswa berhasil dilepas dari rombel PKL.');

        return back();
    }
}
