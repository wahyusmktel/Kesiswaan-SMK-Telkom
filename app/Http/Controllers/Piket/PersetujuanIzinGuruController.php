<?php

namespace App\Http\Controllers\Piket;

use App\Http\Controllers\Controller;
use App\Models\GuruIzin;
use App\Models\AbsensiGuru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersetujuanIzinGuruController extends Controller
{
    public function index(Request $request)
    {
        $query = GuruIzin::with(['guru'])->latest();
        
        if ($request->filled('status')) {
            $query->where('status_piket', $request->status);
        } else {
            $query->where('status_piket', 'menunggu');
        }

        $izins = $query->paginate(10);
        return view('pages.piket.izin-guru.index', compact('izins'));
    }

    public function approve(GuruIzin $izin)
    {
        $updateData = [
            'status_piket' => 'disetujui',
            'piket_id' => Auth::id(),
            'piket_at' => now(),
        ];

        // Jika kategori 'sekolah', maka otomatis setujui kurikulum dan sdm
        if ($izin->kategori_penyetujuan === 'sekolah') {
            $updateData['status_kurikulum'] = 'disetujui';
            $updateData['status_sdm'] = 'disetujui';
            $updateData['kurikulum_id'] = Auth::id(); // Menggunakan ID piket sebagai penanggung jawab sementara
            $updateData['sdm_id'] = Auth::id();
            $updateData['kurikulum_at'] = now();
            $updateData['sdm_at'] = now();
            
            $izin->update($updateData);

            // Sync to AbsensiGuru (Otomatis karena tuntas di Piket)
            foreach ($izin->jadwals as $jadwal) {
                AbsensiGuru::updateOrCreate(
                    [
                        'jadwal_pelajaran_id' => $jadwal->id,
                        'tanggal' => $izin->tanggal_mulai, 
                    ],
                    [
                        'status' => 'izin',
                        'keterangan' => 'Izin Guru (Lingkungan Sekolah): ' . $izin->jenis_izin . ' (' . $izin->deskripsi . ')',
                        'waktu_absen' => now(),
                        'dicatat_oleh' => Auth::id(),
                    ]
                );
            }

            return redirect()->back()->with('success', 'Permohonan izin (Lingkungan Sekolah) telah disetujui sepenuhnya.');
        }

        $izin->update($updateData);
        return redirect()->back()->with('success', 'Permohonan izin diteruskan ke Waka Kurikulum.');
    }

    public function reject(Request $request, GuruIzin $izin)
    {
        $request->validate(['catatan_piket' => 'required|string']);
        
        $izin->update([
            'status_piket' => 'ditolak',
            'piket_id' => Auth::id(),
            'piket_at' => now(),
            'catatan_piket' => $request->catatan_piket,
        ]);

        return redirect()->back()->with('info', 'Permohonan izin telah ditolak.');
    }
}
