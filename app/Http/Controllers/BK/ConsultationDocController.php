<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\BKKonsultasiJadwal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsultationDocController extends Controller
{
    public function printSchedule(BKKonsultasiJadwal $jadwal)
    {
        $this->authorizeAccess($jadwal);

        if (!in_array($jadwal->status, ['approved', 'completed'])) {
            abort(403, 'Jadwal belum disetujui.');
        }

        $jadwal->load(['siswa.rombels.kelas', 'guruBK']);

        $pdf = Pdf::loadView('pdf.konsultasi.schedule', compact('jadwal'));
        return $pdf->stream("Jadwal_Konsultasi_{$jadwal->siswa->nis}.pdf");
    }

    public function printReport(BKKonsultasiJadwal $jadwal)
    {
        $this->authorizeAccess($jadwal);

        if ($jadwal->status !== 'completed') {
            abort(403, 'Konsultasi belum selesai.');
        }

        $jadwal->load(['siswa.rombels.kelas', 'guruBK']);

        $pdf = Pdf::loadView('pdf.konsultasi.report', compact('jadwal'));
        return $pdf->stream("Berita_Acara_Konsultasi_{$jadwal->siswa->nis}.pdf");
    }

    private function authorizeAccess(BKKonsultasiJadwal $jadwal)
    {
        $user = Auth::user();
        
        // BK path
        if ($user->hasRole('Guru BK')) {
            return true;
        }

        // Student path
        if ($user->hasRole('Siswa') && $jadwal->master_siswa_id === $user->masterSiswa->id) {
            return true;
        }

        abort(403);
    }
}
