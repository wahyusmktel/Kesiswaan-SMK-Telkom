<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MasterSiswa;
use App\Models\TahunPelajaran;
use App\Models\SiswaPelanggaran;
use App\Models\Keterlambatan;
use Illuminate\Http\Request;

class MonitoringCatatanController extends Controller
{
    public function index(Request $request)
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $kelas = Kelas::orderBy('nama_kelas')->get();
        
        $query = MasterSiswa::active()
            ->whereNull('graduated_at')
            ->with([
                'rombels' => function ($q) use ($tahunAktif) {
                    if ($tahunAktif) {
                        $q->orderByRaw("CASE WHEN tahun_pelajaran_id = {$tahunAktif->id} THEN 0 ELSE 1 END");
                    }
                    $q->orderByDesc('tahun_pelajaran_id')->with(['kelas', 'waliKelas']);
                },
                'user'
            ]);

        if ($request->filled('kelas_id')) {
            $query->whereHas('rombels', function($q) use ($request, $tahunAktif) {
                $q->where('kelas_id', $request->kelas_id);
                if ($tahunAktif) {
                    $q->where('tahun_pelajaran_id', $tahunAktif->id);
                }
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', '%' . $search . '%')
                  ->orWhere('nis', 'like', '%' . $search . '%');
            });
        }

        $siswas = $query->paginate(15)->withQueryString();

        return view('pages.bk.monitoring-catatan.index', compact('siswas', 'kelas'));
    }

    public function show(MasterSiswa $siswa)
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $siswa->load([
            'rombels' => function ($q) use ($tahunAktif) {
                if ($tahunAktif) {
                    $q->orderByRaw("CASE WHEN tahun_pelajaran_id = {$tahunAktif->id} THEN 0 ELSE 1 END");
                }
                $q->orderByDesc('tahun_pelajaran_id')->with(['kelas', 'waliKelas']);
            },
            'user'
        ]);
        
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
