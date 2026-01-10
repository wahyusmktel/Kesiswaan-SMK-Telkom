<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\Keterlambatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaliKelasMentoringController extends Controller
{
    public function store(Request $request, Keterlambatan $keterlambatan)
    {
        $request->validate([
            'catatan_wali_kelas' => 'required|string|min:10',
        ]);

        // Pastikan yang memproses adalah Wali Kelas dari siswa tersebut
        $rombelSiswa = $keterlambatan->siswa->rombels()->first();
        if (!$rombelSiswa || $rombelSiswa->wali_kelas_id !== Auth::id()) {
            abort(403, 'Anda bukan Wali Kelas dari siswa ini.');
        }

        // Hitung total keterlambatan siswa (termasuk yang sekarang)
        $totalTerlambat = Keterlambatan::where('master_siswa_id', $keterlambatan->master_siswa_id)->count();

        $dataUpdate = [
            'catatan_wali_kelas' => $request->catatan_wali_kelas,
            'waktu_pendampingan_wali_kelas' => now(),
        ];

        // Jika sudah 3 kali atau lebih, alihkan ke BK
        if ($totalTerlambat >= 3) {
            $dataUpdate['status'] = 'pembinaan_bk';
            $message = 'Pendampingan berhasil dicatat. Karena siswa sudah terlambat ' . $totalTerlambat . ' kali, alur dilanjutkan ke Guru BK.';
        } else {
            $dataUpdate['status'] = 'selesai';
            $message = 'Pendampingan berhasil dicatat. Status keterlambatan ditandai Selesai.';
        }

        $keterlambatan->update($dataUpdate);

        toast($message, 'success');
        return back();
    }
}
