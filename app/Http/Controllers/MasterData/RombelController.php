<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MasterSiswa;
use App\Models\Rombel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RombelController extends Controller
{
    /**
     * Menampilkan daftar rombel + Data untuk Dropdown Modal
     */
    public function index(Request $request)
    {
        // withCount('siswa') penting agar kita tidak perlu load seluruh data siswa
        // hanya untuk menghitung jumlahnya di halaman index.
        // Pastikan nama relasi di Model Rombel adalah 'siswa' atau 'siswas' (sesuaikan)
        $query = Rombel::with(['kelas', 'waliKelas'])->withCount('siswa');

        // Filter Pencarian
        if ($request->filled('search')) {
            $query->where('tahun_ajaran', 'like', '%' . $request->search . '%')
                ->orWhereHas('kelas', function ($q) use ($request) {
                    $q->where('nama_kelas', 'like', '%' . $request->search . '%');
                });
        }

        $rombel = $query->latest()->paginate(10);

        // Data untuk Dropdown di Modal Tambah/Edit
        $kelas = Kelas::orderBy('nama_kelas')->pluck('nama_kelas', 'id');
        $wali_kelas = User::role('Wali Kelas')->orderBy('name')->pluck('name', 'id');

        return view('pages.master-data.rombel.index', compact('rombel', 'kelas', 'wali_kelas'));
    }

    /**
     * Method create() dan edit() DIHAPUS karena sudah pakai Modal.
     */

    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran' => 'required|string|max:9', // Contoh: 2024/2025
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
            'tahun_ajaran' => 'required|string|max:9',
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
        // Validasi: Jangan hapus jika ada siswa
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

        // 2. Ambil siswa yang tersedia (Belum punya rombel di Tahun Ajaran yang sama)
        // Logika: Siswa tidak boleh ganda di tahun ajaran yang sama
        $siswaTersedia = MasterSiswa::whereNotIn('id', function ($query) use ($rombel) {
            $query->select('master_siswa_id')
                ->from('rombel_siswa')
                ->join('rombels', 'rombels.id', '=', 'rombel_siswa.rombel_id')
                ->where('rombels.tahun_ajaran', $rombel->tahun_ajaran);
        })->orderBy('nama_lengkap')->get();

        return view('pages.master-data.rombel.show', compact('rombel', 'siswaDiRombel', 'siswaTersedia'));
    }

    /**
     * Tambah Siswa ke Rombel
     */
    public function addSiswa(Request $request, Rombel $rombel)
    {
        $request->validate([
            'siswa_ids' => 'required|array',
            'siswa_ids.*' => 'exists:master_siswa,id',
        ]);

        try {
            // attach() menambahkan relasi many-to-many
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
     * Hapus Siswa dari Rombel
     */
    public function removeSiswa(Rombel $rombel, MasterSiswa $siswa)
    {
        try {
            // detach() menghapus relasi
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
