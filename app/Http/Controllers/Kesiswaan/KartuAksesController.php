<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\MasterSiswa;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;

class KartuAksesController extends Controller
{
    /**
     * Display the Stella Access Card generator page
     */
    public function index(Request $request)
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        
        $rombels = Rombel::with('kelas')
            ->where('tahun_pelajaran_id', $tahunAktif?->id)
            ->get()
            ->sortBy(fn($r) => $r->kelas->nama_kelas ?? '');

        $siswaQuery = MasterSiswa::query()
            ->whereHas('rombels', function ($q) use ($tahunAktif) {
                $q->where('tahun_pelajaran_id', $tahunAktif?->id);
            })
            ->with(['rombels' => function ($q) use ($tahunAktif) {
                $q->where('tahun_pelajaran_id', $tahunAktif?->id)->with('kelas');
            }]);

        // Filter by rombel if provided
        if ($request->filled('rombel_id')) {
            $siswaQuery->whereHas('rombels', function ($q) use ($request) {
                $q->where('rombels.id', $request->rombel_id);
            });
        }

        // Search by name or NIS
        if ($request->filled('search')) {
            $siswaQuery->where(function ($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('nis', 'like', '%' . $request->search . '%');
            });
        }

        $siswa = $siswaQuery->orderBy('nama_lengkap')->paginate(20)->withQueryString();

        return view('pages.kesiswaan.kartu-akses.index', compact('rombels', 'siswa'));
    }

    /**
     * Show single card preview
     */
    public function show(MasterSiswa $siswa)
    {
        $siswa->load(['rombels' => function ($q) {
            $tahunAktif = TahunPelajaran::where('is_active', true)->first();
            $q->where('tahun_pelajaran_id', $tahunAktif?->id)->with('kelas');
        }]);

        // Generate barcode
        $generator = new BarcodeGeneratorPNG();
        $barcode = base64_encode($generator->getBarcode($siswa->nis, $generator::TYPE_CODE_128, 2, 50));

        return view('pages.kesiswaan.kartu-akses.show', compact('siswa', 'barcode'));
    }

    /**
     * Print single card
     */
    public function cetak(MasterSiswa $siswa)
    {
        $siswa->load(['rombels' => function ($q) {
            $tahunAktif = TahunPelajaran::where('is_active', true)->first();
            $q->where('tahun_pelajaran_id', $tahunAktif?->id)->with('kelas');
        }]);

        // Generate barcode
        $generator = new BarcodeGeneratorPNG();
        $barcode = base64_encode($generator->getBarcode($siswa->nis, $generator::TYPE_CODE_128, 2, 50));

        return view('pages.kesiswaan.kartu-akses.cetak', compact('siswa', 'barcode'));
    }

    /**
     * Mass print cards filtered by class
     */
    public function cetakMasal(Request $request)
    {
        $request->validate([
            'rombel_id' => 'required|exists:rombels,id',
        ]);

        $rombel = Rombel::with('kelas')->findOrFail($request->rombel_id);
        
        $siswaList = MasterSiswa::whereHas('rombels', function ($q) use ($request) {
            $q->where('rombels.id', $request->rombel_id);
        })
        ->with(['rombels' => function ($q) use ($request) {
            $q->where('rombels.id', $request->rombel_id)->with('kelas');
        }])
        ->orderBy('nama_lengkap')
        ->get();

        // Generate barcodes for all students
        $generator = new BarcodeGeneratorPNG();
        $barcodes = [];
        foreach ($siswaList as $siswa) {
            $barcodes[$siswa->id] = base64_encode($generator->getBarcode($siswa->nis, $generator::TYPE_CODE_128, 2, 50));
        }

        return view('pages.kesiswaan.kartu-akses.cetak-masal', compact('siswaList', 'barcodes', 'rombel'));
    }
}
