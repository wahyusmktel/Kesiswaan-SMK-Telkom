<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MasterSiswa;
use App\Models\Rombel;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\TahunPelajaran; // Pastikan Model ini di-import
use Illuminate\Support\Facades\Log;

class RombelController extends Controller
{
    /**
     * Menampilkan daftar rombel + Data untuk Dropdown Modal
     */
    public function index(Request $request)
    {
        // 1. Eager Load 'tahunPelajaran' (Relasi)
        $query = Rombel::with(['kelas', 'waliKelas', 'tahunPelajaran'])->withCount('siswa');

        // 2. Filter Pencarian
        if ($request->filled('search')) {
            $query->whereHas('kelas', function ($q) use ($request) {
                $q->where('nama_kelas', 'like', '%' . $request->search . '%');
            })
                ->orWhereHas('waliKelas', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                })
                ->orWhereHas('tahunPelajaran', function ($q) use ($request) {
                    $q->where('tahun', 'like', '%' . $request->search . '%');
                });
        }

        $rombel = $query->latest()->paginate(10);

        // 3. Data Dropdown Tahun Pelajaran
        // PENTING: Gunakan nama variabel '$tahun_pelajaran' agar sesuai dengan View
        // Kita ambil Collection object (get) bukan array (pluck/mapWithKeys)
        // agar di Blade bisa akses $tp->id, $tp->tahun, dll.
        $tahun_pelajaran = TahunPelajaran::orderBy('tahun', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        // Ambil ID Tahun Aktif sebagai default value
        $tahun_aktif_id = TahunPelajaran::where('is_active', true)->value('id');

        $kelas = Kelas::orderBy('nama_kelas')->pluck('nama_kelas', 'id');
        $wali_kelas = User::role('Wali Kelas')->orderBy('name')->pluck('name', 'id');

        // Kirim variabel '$tahun_pelajaran' ke view
        return view('pages.master-data.rombel.index', compact(
            'rombel',
            'kelas',
            'wali_kelas',
            'tahun_pelajaran', // <--- Pastikan ini ada!
            'tahun_aktif_id'
        ));
    }

    public function store(Request $request)
    {
        // Validasi menggunakan ID Relasi
        $request->validate([
            'tahun_pelajaran_id' => 'required|exists:tahun_pelajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
            'wali_kelas_id' => 'required|exists:users,id',
        ]);

        try {
            Rombel::create($request->all());
            toast('Data rombel berhasil ditambahkan.', 'success');
            return redirect()->route('master-data.rombel.index');
        } catch (\Exception $e) {
            Log::error('Error storing rombel: ' . $e->getMessage());
            toast('Gagal menambahkan data rombel.', 'error');
            return back()->withInput();
        }
    }

    public function update(Request $request, Rombel $rombel)
    {
        $request->validate([
            'tahun_pelajaran_id' => 'required|exists:tahun_pelajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
            'wali_kelas_id' => 'required|exists:users,id',
        ]);

        try {
            $rombel->update($request->all());
            toast('Data rombel berhasil diperbarui.', 'success');
            return redirect()->route('master-data.rombel.index');
        } catch (\Exception $e) {
            Log::error('Error updating rombel: ' . $e->getMessage());
            toast('Gagal memperbarui data rombel.', 'error');
            return back()->withInput();
        }
    }

    public function destroy(Rombel $rombel)
    {
        if ($rombel->siswa()->exists()) {
            toast('Gagal menghapus! Masih ada siswa terdaftar di rombel ini.', 'error');
            return back();
        }

        try {
            $rombel->delete();
            toast('Data rombel berhasil dihapus.', 'success');
            return redirect()->route('master-data.rombel.index');
        } catch (\Exception $e) {
            Log::error('Error deleting rombel: ' . $e->getMessage());
            toast('Gagal menghapus data rombel.', 'error');
            return back();
        }
    }

    /**
     * Halaman Detail untuk Add/Remove Siswa
     */
    public function show(Rombel $rombel)
    {
        // 1. Ambil siswa yang sudah ada di rombel ini
        $siswaDiRombel = $rombel->siswa()->orderBy('nama_lengkap')->get();

        // 2. Ambil siswa yang tersedia
        // Logic: Siswa belum punya rombel DI TAHUN PELAJARAN YANG SAMA
        // (Boleh punya rombel di tahun lalu, tapi tahun ini harus kosong)

        $currentTahunId = $rombel->tahun_pelajaran_id;

        $siswaTersedia = MasterSiswa::whereNotIn('id', function ($query) use ($currentTahunId) {
            $query->select('master_siswa_id')
                ->from('rombel_siswa')
                ->join('rombels', 'rombels.id', '=', 'rombel_siswa.rombel_id')
                ->where('rombels.tahun_pelajaran_id', $currentTahunId); // Filter by ID Tahun
        })->orderBy('nama_lengkap')->get();

        return view('pages.master-data.rombel.show', compact('rombel', 'siswaDiRombel', 'siswaTersedia'));
    }

    /**
     * Tambah Siswa ke Rombel (Logic Tetap Sama)
     */
    public function addSiswa(Request $request, Rombel $rombel)
    {
        $request->validate([
            'siswa_ids' => 'required|array',
            'siswa_ids.*' => 'exists:master_siswa,id',
        ]);

        try {
            $rombel->siswa()->attach($request->siswa_ids);
            toast(count($request->siswa_ids) . ' Siswa berhasil ditambahkan.', 'success');
            return back();
        } catch (\Exception $e) {
            Log::error('Error adding students to rombel: ' . $e->getMessage());
            toast('Gagal menambahkan siswa.', 'error');
            return back();
        }
    }

    /**
     * Hapus Siswa dari Rombel (Logic Tetap Sama)
     */
    public function removeSiswa(Rombel $rombel, MasterSiswa $siswa)
    {
        try {
            $rombel->siswa()->detach($siswa->id);
            toast('Siswa berhasil dikeluarkan dari rombel.', 'success');
            return back();
        } catch (\Exception $e) {
            Log::error('Error removing student from rombel: ' . $e->getMessage());
            toast('Gagal mengeluarkan siswa.', 'error');
            return back();
        }
    }
}
