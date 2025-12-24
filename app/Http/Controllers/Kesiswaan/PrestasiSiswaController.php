<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\MasterSiswa;
use App\Models\SiswaPrestasi;
use Illuminate\Http\Request;

class PrestasiSiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = SiswaPrestasi::with(['siswa'])->latest();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('siswa', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%$search%")->orWhere('nis', 'like', "%$search%");
            });
        }

        $prestasis = $query->paginate(10);
        
        return view('kesiswaan.poin.prestasi', compact('prestasis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'master_siswa_id' => 'required|exists:master_siswa,id',
            'nama_prestasi' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'poin_bonus' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        SiswaPrestasi::create($request->all());

        return redirect()->back()->with('success', 'Prestasi siswa berhasil dicatat.');
    }

    public function destroy(SiswaPrestasi $input_prestasi)
    {
        $input_prestasi->delete();
        return redirect()->back()->with('success', 'Catatan prestasi berhasil dihapus.');
    }
}
