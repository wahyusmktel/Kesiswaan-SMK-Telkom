<?php

namespace App\Http\Controllers;

use App\Models\MasterSiswa;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PengaduanController extends Controller
{
    /**
     * Menampilkan daftar pengaduan (untuk Admin/Kesiswaan).
     */
    public function index(Request $request)
    {
        try {
            $query = Pengaduan::query();

            // Filter Pencarian
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama_pelapor', 'like', "%$search%")
                      ->orWhere('nama_siswa', 'like', "%$search%")
                      ->orWhere('isi_pengaduan', 'like', "%$search%");
                });
            }

            // Filter Status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $pengaduans = $query->latest()->paginate(10);

            return view('pages.kesiswaan.pengaduan.index', compact('pengaduans'));
        } catch (\Exception $e) {
            Log::error('Error fetching pengaduan: ' . $e->getMessage());
            toast('Gagal memuat data pengaduan.', 'error');
            return redirect()->back();
        }
    }

    /**
     * Menampilkan form pengaduan (untuk Publik).
     */
    public function create()
    {
        $siswa = MasterSiswa::with('rombels.kelas')->get();
        return view('pages.publik.pengaduan', compact('siswa'));
    }

    /**
     * Menyimpan pengaduan baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_pelapor' => 'required|string|max:255',
            'hubungan' => 'required|string',
            'nomor_wa' => 'required|string|max:20',
            'nama_siswa' => 'required|string|max:255',
            'kelas_siswa' => 'required|string|max:50',
            'kategori' => 'required|string',
            'isi_pengaduan' => 'required|string|min:20',
        ]);

        if ($validator->fails()) {
            toast($validator->errors()->first(), 'error');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            Pengaduan::create($request->all());

            toast('Terima kasih! Pengaduan Anda berhasil dikirim dan akan segera kami proses.', 'success');
            return redirect()->route('welcome');
        } catch (\Exception $e) {
            Log::error('Error saving pengaduan: ' . $e->getMessage());
            toast('Terjadi kesalahan saat mengirim pengaduan.', 'error');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Update status pengaduan (untuk Admin).
     */
    public function updateStatus(Request $request, Pengaduan $pengaduan)
    {
        $request->validate([
            'status' => 'required|in:pending,diproses,selesai',
            'catatan_petugas' => 'nullable|string'
        ]);

        try {
            $pengaduan->update([
                'status' => $request->status,
                'catatan_petugas' => $request->catatan_petugas
            ]);

            toast('Status pengaduan berhasil diperbarui.', 'success');
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Error updating pengaduan status: ' . $e->getMessage());
            toast('Gagal memperbarui status pengaduan.', 'error');
            return redirect()->back();
        }
    }
}
