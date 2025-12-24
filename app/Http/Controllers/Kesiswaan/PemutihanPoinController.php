<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\MasterSiswa;
use App\Models\SiswaPemutihan;
use Illuminate\Http\Request;

class PemutihanPoinController extends Controller
{
    public function index(Request $request)
    {
        $query = SiswaPemutihan::with(['siswa'])->latest();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('siswa', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%$search%")->orWhere('nis', 'like', "%$search%");
            });
        }

        $pemutihans = $query->paginate(10);
        
        return view('kesiswaan.poin.pemutihan', compact('pemutihans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'master_siswa_id' => 'required|exists:master_siswa,id',
            'tanggal' => 'required|date',
            'poin_dikurangi' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        SiswaPemutihan::create($request->all());

        return redirect()->back()->with('success', 'Pemutihan poin siswa berhasil dicatat.');
    }

    public function destroy(SiswaPemutihan $input_pemutihan)
    {
        $input_pemutihan->delete();
        return redirect()->back()->with('success', 'Catatan pemutihan berhasil dihapus.');
    }
}
