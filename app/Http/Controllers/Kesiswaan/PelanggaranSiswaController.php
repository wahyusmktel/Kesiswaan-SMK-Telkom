<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\MasterSiswa;
use App\Models\PoinPeraturan;
use App\Models\SiswaPelanggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PelanggaranSiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = SiswaPelanggaran::with(['siswa', 'peraturan', 'pelapor'])->latest();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('siswa', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%$search%")->orWhere('nis', 'like', "%$search%");
            });
        }

        $pelanggarans = $query->paginate(10);
        $peraturans = PoinPeraturan::with('category')->get();
        
        return view('kesiswaan.poin.pelanggaran', compact('pelanggarans', 'peraturans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'master_siswa_id' => 'required|exists:master_siswa,id',
            'poin_peraturan_id' => 'required|exists:poin_peraturans,id',
            'tanggal' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        SiswaPelanggaran::create([
            'master_siswa_id' => $request->master_siswa_id,
            'poin_peraturan_id' => $request->poin_peraturan_id,
            'tanggal' => $request->tanggal,
            'catatan' => $request->catatan,
            'pelapor_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Pelanggaran siswa berhasil dicatat.');
    }

    public function destroy(SiswaPelanggaran $input_pelanggaran)
    {
        $input_pelanggaran->delete();
        return redirect()->back()->with('success', 'Catatan pelanggaran berhasil dihapus.');
    }
}
