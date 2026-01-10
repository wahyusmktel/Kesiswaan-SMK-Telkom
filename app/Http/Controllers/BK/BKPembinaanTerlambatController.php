<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\Keterlambatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\KeterlambatanBKCoaching;
use Barryvdh\DomPDF\Facade\Pdf;

class BKPembinaanTerlambatController extends Controller
{
    public function store(Request $request, Keterlambatan $keterlambatan)
    {
        $request->validate([
            'tanggal_konseling' => 'required|date',
            'evaluasi_sebelumnya' => 'required|string|min:10',
            'faktor_penghambat' => 'required|string|min:10',
            'analisis_dampak' => 'required|string|min:10',
            'jam_bangun' => 'required',
            'jam_berangkat' => 'required',
            'durasi_perjalanan' => 'required|integer',
            'strategi_pendukung' => 'required|array',
            'sanksi_disepakati' => 'required|string|min:5',
        ]);

        if (!Auth::user()->hasRole('Guru BK')) {
            abort(403, 'Akses ditolak. Hanya Guru BK yang dapat melakukan pembinaan lanjutan.');
        }

        // Simpan Data Coaching BK
        KeterlambatanBKCoaching::create([
            'keterlambatan_id' => $keterlambatan->id,
            'pencatat_id' => Auth::id(),
            'tanggal_konseling' => $request->tanggal_konseling,
            'evaluasi_sebelumnya' => $request->evaluasi_sebelumnya,
            'faktor_penghambat' => $request->faktor_penghambat,
            'analisis_dampak' => $request->analisis_dampak,
            'jam_bangun' => $request->jam_bangun,
            'jam_berangkat' => $request->jam_berangkat,
            'durasi_perjalanan' => $request->durasi_perjalanan,
            'strategi_pendukung' => $request->strategi_pendukung,
            'hp_limit_time' => $request->hp_limit_time,
            'sanksi_disepakati' => $request->sanksi_disepakati,
        ]);

        $keterlambatan->update([
            'catatan_bk' => 'Siswa telah melaksanakan sesi konseling mendalam dan kontrak perilaku dengan Guru BK pada ' . $request->tanggal_konseling,
            'pembinaan_oleh_bk_id' => Auth::id(),
            'waktu_pembinaan_bk' => now(),
            'status' => 'selesai',
        ]);

        toast('Pembinaan & Kontrak Perilaku BK berhasil dicatat. Status keterlambatan ditandai Selesai.', 'success');
        return back();
    }

    public function downloadCoaching(Keterlambatan $keterlambatan)
    {
        $keterlambatan->load(['siswa.rombels.kelas', 'bkCoaching', 'siswa.rombels.waliKelas']);
        
        if (!$keterlambatan->bkCoaching) {
            abort(404, 'Data coaching BK tidak ditemukan.');
        }

        // Ambil riwayat keterlambatan (biasanya yang ke 1, 2, dan 3)
        $history = Keterlambatan::where('master_siswa_id', $keterlambatan->master_siswa_id)
            ->where('waktu_dicatat_security', '<=', $keterlambatan->waktu_dicatat_security)
            ->orderBy('waktu_dicatat_security', 'asc')
            ->take(3)
            ->get();

        $data = [
            'keterlambatan' => $keterlambatan,
            'history' => $history,
            'bk_teacher' => $keterlambatan->bkCoaching->pencatat->name ?? '-',
            'wali_kelas' => $keterlambatan->siswa->rombels->first()?->waliKelas->name ?? '-',
        ];

        $pdf = Pdf::loadView('pages.bk.keterlambatan.coaching-pdf', $data);
        
        return $pdf->download('Kontrak_Perilaku_' . $keterlambatan->siswa->nama_lengkap . '_' . date('Ymd') . '.pdf');
    }
}
