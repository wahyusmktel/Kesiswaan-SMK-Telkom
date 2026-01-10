<?php

namespace App\Http\Controllers\GuruKelas;

use App\Http\Controllers\Controller;
use App\Models\Keterlambatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifikasiTerlambatController extends Controller
{
    /**
     * Memproses hasil scan QR Code dan menampilkan hasilnya.
     */
    public function scanAndVerify($uuid)
    {
        $keterlambatan = Keterlambatan::where('uuid', $uuid)->first();

        // Jika surat tidak ditemukan
        if (!$keterlambatan) {
            return view('pages.guru-kelas.verifikasi-terlambat.hasil', [
                'success' => false,
                'message' => 'Surat Izin Masuk tidak valid atau tidak ditemukan.'
            ]);
        }

        // Jika surat valid dan statusnya 'diverifikasi_piket'
        if ($keterlambatan->status === 'diverifikasi_piket') {
            $keterlambatan->update([
                'status' => 'pendampingan_wali_kelas',
                'verifikasi_oleh_guru_kelas_id' => Auth::id(),
                'waktu_verifikasi_guru_kelas' => now(),
            ]);

            return view('pages.guru-kelas.verifikasi-terlambat.hasil', [
                'success' => true,
                'message' => 'telah ditandai masuk ke dalam kelas.',
                'keterlambatan' => $keterlambatan->load('siswa')
            ]);
        }
        // Jika surat sudah pernah diproses atau statusnya tidak sesuai
        else {
            return view('pages.guru-kelas.verifikasi-terlambat.hasil', [
                'success' => false,
                'message' => 'Status izin ini sudah final atau tidak valid untuk diverifikasi.',
                'keterlambatan' => $keterlambatan->load('siswa')
            ]);
        }
    }
}
