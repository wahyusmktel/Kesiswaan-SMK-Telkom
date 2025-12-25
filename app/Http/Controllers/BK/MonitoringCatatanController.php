<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MasterSiswa;
use App\Models\SiswaPelanggaran;
use App\Models\Keterlambatan;
use Illuminate\Http\Request;

class MonitoringCatatanController extends Controller
{
    public function index(Request $request)
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();
        
        $query = MasterSiswa::with(['rombels.kelas', 'user']);

        if ($request->filled('kelas_id')) {
            $query->whereHas('rombels', function($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        if ($request->filled('search')) {
            $query->where('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('nisn', 'like', '%' . $request->search . '%');
        }

        $siswas = $query->paginate(15);

        return view('pages.bk.monitoring-catatan.index', compact('siswas', 'kelas'));
    }

    public function show(MasterSiswa $siswa)
    {
        $siswa->load(['rombels.kelas', 'user']);
        
        $pelanggarans = $siswa->pelanggarans()
            ->with(['peraturan', 'pelapor'])
            ->latest('tanggal')
            ->get();

        $keterlambatans = $siswa->keterlambatans()
            ->with(['security', 'guruPiket'])
            ->latest('created_at')
            ->get();

        $panggilans = $siswa->panggilans()
            ->with(['creator', 'approver'])
            ->latest()
            ->get();

        return view('pages.bk.monitoring-catatan.show', compact('siswa', 'pelanggarans', 'keterlambatans', 'panggilans'));
    }
}
