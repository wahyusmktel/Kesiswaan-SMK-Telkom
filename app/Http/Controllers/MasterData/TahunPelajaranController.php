<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TahunPelajaranController extends Controller
{
    public function index()
    {
        $tahunPelajaran = TahunPelajaran::latest()->paginate(10);
        return view('pages.master-data.tahun-pelajaran.index', compact('tahunPelajaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|string|max:9', // Validasi format bisa ditambah regex
            'semester' => 'required|in:Ganjil,Genap',
        ]);

        try {
            // Jika status aktif dicentang, nonaktifkan tahun lain
            if ($request->has('is_active')) {
                TahunPelajaran::query()->update(['is_active' => false]);
            }

            TahunPelajaran::create([
                'tahun' => $request->tahun,
                'semester' => $request->semester,
                'is_active' => $request->has('is_active'),
            ]);

            toast('Tahun pelajaran berhasil ditambahkan.', 'success');
            return back();
        } catch (\Exception $e) {
            Log::error('Error storing academic year: ' . $e->getMessage());
            toast('Gagal menambahkan tahun pelajaran.', 'error');
            return back();
        }
    }

    public function update(Request $request, TahunPelajaran $tahunPelajaran)
    {
        $request->validate([
            'tahun' => 'required|string|max:9',
            'semester' => 'required|in:Ganjil,Genap',
        ]);

        try {
            if ($request->has('is_active') && !$tahunPelajaran->is_active) {
                TahunPelajaran::query()->update(['is_active' => false]);
            }

            $tahunPelajaran->update([
                'tahun' => $request->tahun,
                'semester' => $request->semester,
                'is_active' => $request->has('is_active') ? true : $tahunPelajaran->is_active, // Cegah uncheck manual jika perlu
            ]);

            toast('Tahun pelajaran berhasil diperbarui.', 'success');
            return back();
        } catch (\Exception $e) {
            Log::error('Error updating academic year: ' . $e->getMessage());
            toast('Gagal memperbarui tahun pelajaran.', 'error');
            return back();
        }
    }

    public function destroy(TahunPelajaran $tahunPelajaran)
    {
        if ($tahunPelajaran->is_active) {
            toast('Tidak bisa menghapus tahun pelajaran yang sedang aktif.', 'error');
            return back();
        }

        try {
            $tahunPelajaran->delete();
            toast('Tahun pelajaran berhasil dihapus.', 'success');
            return back();
        } catch (\Exception $e) {
            toast('Gagal menghapus data.', 'error');
            return back();
        }
    }

    // Method khusus untuk mengaktifkan via tombol tabel
    public function activate(TahunPelajaran $tahunPelajaran)
    {
        try {
            TahunPelajaran::query()->update(['is_active' => false]);
            $tahunPelajaran->update(['is_active' => true]);

            toast('Tahun pelajaran ' . $tahunPelajaran->tahun . ' ' . $tahunPelajaran->semester . ' kini AKTIF.', 'success');
            return back();
        } catch (\Exception $e) {
            toast('Gagal mengaktifkan tahun pelajaran.', 'error');
            return back();
        }
    }
}
