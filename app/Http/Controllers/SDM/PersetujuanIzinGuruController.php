<?php

namespace App\Http\Controllers\SDM;

use App\Http\Controllers\Controller;
use App\Models\GuruIzin;
use App\Models\AbsensiGuru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PersetujuanIzinGuruController extends Controller
{
    public function index(Request $request)
    {
        $query = GuruIzin::with(['guru'])->where('status_kurikulum', 'disetujui')->latest();
        
        if ($request->filled('status')) {
            $query->where('status_sdm', $request->status);
        } else {
            $query->where('status_sdm', 'menunggu');
        }

        $izins = $query->paginate(10);
        return view('pages.sdm.izin-guru.index', compact('izins'));
    }

    public function approve(GuruIzin $izin)
    {
        $izin->update([
            'status_sdm' => 'disetujui',
            'sdm_id' => Auth::id(),
            'sdm_at' => now(),
        ]);

        // Sync to AbsensiGuru
        foreach ($izin->jadwals as $jadwal) {
            AbsensiGuru::updateOrCreate(
                [
                    'jadwal_pelajaran_id' => $jadwal->id,
                    'tanggal' => $izin->tanggal_mulai, // Simplification: assuming single day for now or loop through dates
                ],
                [
                    'status' => 'izin',
                    'keterangan' => 'Izin Guru: ' . $izin->jenis_izin . ' (' . $izin->deskripsi . ')',
                    'waktu_absen' => now(),
                    'dicatat_oleh' => Auth::id(),
                ]
            );
        }

        return redirect()->back()->with('success', 'Permohonan izin telah disetujui sepenuhnya dan absensi telah diperbarui.');
    }

    public function reject(Request $request, GuruIzin $izin)
    {
        $request->validate(['catatan_sdm' => 'required|string']);
        
        $izin->update([
            'status_sdm' => 'ditolak',
            'sdm_id' => Auth::id(),
            'sdm_at' => now(),
            'catatan_sdm' => $request->catatan_sdm,
        ]);

        return redirect()->back()->with('info', 'Permohonan izin telah ditolak oleh KAUR SDM.');
    }

    public function printPdf(GuruIzin $izin)
    {
        if ($izin->status_sdm !== 'disetujui') {
            abort(403, 'Surat izin belum disetujui oleh KAUR SDM.');
        }

        // Security check: If teacher, only allow printing their own permit
        $user = Auth::user();
        if ($user->hasRole('Guru Kelas')) {
            $guru = $user->masterGuru;
            if (!$guru || $izin->master_guru_id !== $guru->id) {
                abort(403, 'Anda tidak memiliki akses untuk mengunduh surat izin ini.');
            }
        }

        $izin->load(['guru', 'piket', 'kurikulum', 'sdm', 'jadwals.rombel.kelas', 'jadwals.mataPelajaran']);
        
        $pdf = Pdf::loadView('pdf.izin-guru', compact('izin'));
        return $pdf->stream('Surat_Izin_Guru_' . str_replace(' ', '_', $izin->guru->nama_lengkap) . '.pdf');
    }
}
