<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\SiswaPanggilan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PanggilanProposalController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'master_siswa_id' => 'required|exists:master_siswa,id',
            'nomor_surat' => 'required|string|unique:siswa_panggilans,nomor_surat',
            'tanggal_panggilan' => 'required|date',
            'jam_panggilan' => 'required',
            'tempat_panggilan' => 'required|string',
            'perihal' => 'required|string',
        ]);

        SiswaPanggilan::create([
            'master_siswa_id' => $request->master_siswa_id,
            'nomor_surat' => $request->nomor_surat,
            'tanggal_panggilan' => $request->tanggal_panggilan,
            'jam_panggilan' => $request->jam_panggilan,
            'tempat_panggilan' => $request->tempat_panggilan,
            'perihal' => $request->perihal,
            'status' => 'diajukan',
            'created_by' => Auth::id(),
        ]);

        toast('Panggilan orang tua berhasil diajukan untuk disetujui Waka Kesiswaan.', 'success');
        
        return redirect()->route('bk.monitoring-catatan.index');
    }
}
