<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\MasterSiswa;
use App\Models\SiswaPanggilan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PanggilanOrangTuaController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Siswa yang poinnya >= 100 (Kritis)
        $butuhPanggilan = MasterSiswa::with(['rombels.kelas'])
            ->get()
            ->filter(function($siswa) {
                return $siswa->getCurrentPoints() >= 100;
            });

        // 2. Ambil Riwayat Panggilan
        $panggilans = SiswaPanggilan::with(['siswa.rombels.kelas', 'creator'])->latest()->paginate(10);

        return view('kesiswaan.poin.panggilan', compact('butuhPanggilan', 'panggilans'));
    }

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
            'created_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Surat panggilan berhasil dicatat.');
    }

    public function printPdf(SiswaPanggilan $panggilan)
    {
        $user = Auth::user();
        
        // Security check: Siswa hanya boleh cetak surat miliknya sendiri
        if ($user->hasRole('Siswa')) {
            if (!$user->masterSiswa || $user->masterSiswa->id !== $panggilan->master_siswa_id) {
                abort(403, 'Anda tidak memiliki akses ke surat ini.');
            }
        }

        $panggilan->load(['siswa.rombels.kelas', 'creator']);
        $pdf = Pdf::loadView('pdf.surat-panggilan-ortu', compact('panggilan'));
        
        return $pdf->stream('surat-panggilan-' . $panggilan->siswa->nama_lengkap . '.pdf');
    }

    public function updateStatus(Request $request, SiswaPanggilan $panggilan)
    {
        $request->validate(['status' => 'required|in:terkirim,hadir,tidak_hadir']);
        $panggilan->update(['status' => $request->status]);
        
        return redirect()->back()->with('success', 'Status panggilan berhasil diperbarui.');
    }

    public function destroy(SiswaPanggilan $panggilan)
    {
        $panggilan->delete();
        return redirect()->back()->with('success', 'Catatan panggilan dihapus.');
    }
}
