<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatCatatanController extends Controller
{
    public function index()
    {
        $siswa = Auth::user()->masterSiswa;
        
        // Ambil data pelanggaran
        $pelanggarans = $siswa->pelanggarans()
            ->with(['peraturan', 'pelapor'])
            ->latest('tanggal')
            ->paginate(10, ['*'], 'pelanggaran_page');

        // Ambil data keterlambatan
        $keterlambatans = $siswa->keterlambatans()
            ->latest('created_at')
            ->paginate(10, ['*'], 'keterlambatan_page');

        // Ambil data prestasi
        $prestasis = $siswa->prestasis()
            ->latest('tanggal')
            ->paginate(10, ['*'], 'prestasi_page');

        // Ambil data pemutihan
        $pemutihans = $siswa->pemutihans()
            ->with(['pengaju', 'penyetuju'])
            ->latest('tanggal')
            ->paginate(10, ['*'], 'pemutihan_page');

        return view('pages.siswa.riwayat-catatan.index', compact('pelanggarans', 'keterlambatans', 'prestasis', 'pemutihans'));
    }
}
