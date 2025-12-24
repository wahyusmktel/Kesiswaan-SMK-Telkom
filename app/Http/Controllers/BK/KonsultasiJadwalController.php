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
