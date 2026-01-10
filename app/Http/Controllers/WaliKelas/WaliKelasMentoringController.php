<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\Keterlambatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\KeterlambatanCoaching;
use Barryvdh\DomPDF\Facade\Pdf;

class WaliKelasMentoringController extends Controller
{
    public function store(Request $request, Keterlambatan $keterlambatan)
    {
        $request->validate([
            'tanggal_coaching' => 'required|date',
            'lokasi' => 'required|in:langsung,online',
            'goal_response' => 'required|string|min:5',
            'reality_response' => 'required|string|min:5',
            'options_response' => 'required|string|min:5',
            'will_response' => 'required|string|min:5',
            'rencana_aksi' => 'required|string|min:10',
            'konsekuensi_logis' => 'required|string|min:5',
        ]);

        // Pastikan yang memproses adalah Wali Kelas dari siswa tersebut
        $rombelSiswa = $keterlambatan->siswa->rombels()->first();
        if (!$rombelSiswa || $rombelSiswa->wali_kelas_id !== Auth::id()) {
            abort(403, 'Anda bukan Wali Kelas dari siswa ini.');
        }

        // Simpan Data Coaching
        KeterlambatanCoaching::create([
            'keterlambatan_id' => $keterlambatan->id,
            'tanggal_coaching' => $request->tanggal_coaching,
            'lokasi' => $request->lokasi,
            'goal_response' => $request->goal_response,
            'reality_response' => $request->reality_response,
            'options_response' => $request->options_response,
            'will_response' => $request->will_response,
            'rencana_aksi' => $request->rencana_aksi,
            'konsekuensi_logis' => $request->konsekuensi_logis,
        ]);

        // Hitung total keterlambatan siswa (termasuk yang sekarang)
        $totalTerlambat = Keterlambatan::where('master_siswa_id', $keterlambatan->master_siswa_id)->count();

        $dataUpdate = [
            'catatan_wali_kelas' => 'Siswa telah mengikuti sesi coaching GROW pada ' . $request->tanggal_coaching,
            'waktu_pendampingan_wali_kelas' => now(),
        ];

        // Jika sudah 3 kali atau lebih, alihkan ke BK
        if ($totalTerlambat >= 3) {
            $dataUpdate['status'] = 'pembinaan_bk';
            $message = 'Pendampingan (Coaching GROW) berhasil dicatat. Karena siswa sudah terlambat ' . $totalTerlambat . ' kali, alur dilanjutkan ke Guru BK.';
        } else {
            $dataUpdate['status'] = 'selesai';
            $message = 'Pendampingan (Coaching GROW) berhasil dicatat. Status keterlambatan ditandai Selesai.';
        }

        $keterlambatan->update($dataUpdate);

        toast($message, 'success');
        return back();
    }

    public function downloadCoaching(Keterlambatan $keterlambatan)
    {
        $keterlambatan->load(['siswa.rombels.kelas', 'coaching']);
        
        if (!$keterlambatan->coaching) {
            abort(404, 'Data coaching tidak ditemukan.');
        }

        // Hitung frekuensi terlambat bulan ini
        $now = now();
        $frekuensiBulanIni = Keterlambatan::where('master_siswa_id', $keterlambatan->master_siswa_id)
            ->whereMonth('waktu_dicatat_security', $now->month)
            ->whereYear('waktu_dicatat_security', $now->year)
            ->count();

        $data = [
            'keterlambatan' => $keterlambatan,
            'frekuensiBulanIni' => $frekuensiBulanIni,
            'wali_kelas' => Auth::user()->name, 
        ];

        $pdf = Pdf::loadView('pages.wali-kelas.keterlambatan.coaching-pdf', $data);
        
        return $pdf->download('Lembar_Coaching_' . $keterlambatan->siswa->nama_lengkap . '_' . date('Ymd') . '.pdf');
    }
}
