<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\BKPembinaanRutin;
use App\Models\MasterSiswa;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembinaanRutinController extends Controller
{
    public function index(Request $request)
    {
        $pembinaans = BKPembinaanRutin::with(['siswa', 'tahunPelajaran', 'guruBK'])
            ->latest()
            ->paginate(10);
        
        $siswas = MasterSiswa::with('rombels.kelas')->get();
        $tahunPelajarans = TahunPelajaran::orderBy('tahun', 'desc')->get();
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        return view('pages.bk.pembinaan.index', compact('pembinaans', 'siswas', 'tahunPelajarans', 'tahunAktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'master_siswa_id' => 'required|exists:master_siswa,id',
            'semester' => 'required|string',
            'tahun_pelajaran_id' => 'required|exists:tahun_pelajaran,id',
            'tanggal' => 'required|date',
            'kondisi_siswa' => 'nullable|string',
            'catatan_pembinaan' => 'nullable|string',
        ]);

        BKPembinaanRutin::create([
            ...$request->all(),
            'guru_bk_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Data pembinaan rutin berhasil disimpan.');
    }

    public function destroy(BKPembinaanRutin $pembinaan)
    {
        $pembinaan->delete();
        return redirect()->back()->with('success', 'Data pembinaan rutin berhasil dihapus.');
    }
}
