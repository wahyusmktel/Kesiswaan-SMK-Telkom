<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\BKKonsultasiJadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KonsultasiJadwalController extends Controller
{
    public function index()
    {
        $jadwals = BKKonsultasiJadwal::with(['siswa.rombels.kelas'])
            ->latest()
            ->paginate(10);
        
        return view('pages.bk.konsultasi.index', compact('jadwals'));
    }

    public function storeByBK(Request $request)
    {
        $request->validate([
            'master_siswa_id' => 'required|exists:master_siswa,id',
            'perihal' => 'required|string',
            'tanggal_rencana' => 'required|date',
            'jam_rencana' => 'required',
            'tempat' => 'nullable|string',
        ]);

        BKKonsultasiJadwal::create([
            'master_siswa_id' => $request->master_siswa_id,
            'guru_bk_id' => Auth::id(),
            'perihal' => $request->perihal,
            'tanggal_rencana' => $request->tanggal_rencana,
            'jam_rencana' => $request->jam_rencana,
            'tempat' => $request->tempat ?? 'Ruang BK',
            'status' => 'approved', // Langsung approved karena dibuat oleh BK
        ]);

        return redirect()->back()->with('success', 'Jadwal pembinaan/konsultasi berhasil dibuat.');
    }

    public function updateStatus(Request $request, BKKonsultasiJadwal $jadwal)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,completed',
            'tempat' => 'nullable|string',
            'catatan_bk' => 'nullable|string',
        ]);

        $updateData = [
            'status' => $request->status,
            'guru_bk_id' => Auth::id(),
        ];

        if ($request->has('tempat')) $updateData['tempat'] = $request->tempat;
        if ($request->has('catatan_bk')) $updateData['catatan_bk'] = $request->catatan_bk;

        $jadwal->update($updateData);

        return redirect()->back()->with('success', 'Status konsultasi berhasil diperbarui.');
    }
}
