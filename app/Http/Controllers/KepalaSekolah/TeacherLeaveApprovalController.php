<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\GuruIzin;
use App\Notifications\PengajuanIzinGuruNotification;
use App\Support\EmploymentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherLeaveApprovalController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate(['status' => ['nullable', 'in:menunggu,disetujui,ditolak']]);
        $status = $filters['status'] ?? 'menunggu';
        $izins = GuruIzin::with(['guru.dapodikGuru', 'jadwals.rombel.kelas', 'jadwals.mataPelajaran', 'sdm', 'kepalaSekolah'])
            ->where('status_sdm', 'disetujui')->where('status_kepala_sekolah', $status)
            ->latest('sdm_at')->paginate(15)->withQueryString();

        return view('pages.kepala-sekolah.teacher-leave-approvals', compact('izins', 'status'));
    }

    public function approve(GuruIzin $izin)
    {
        DB::transaction(function () use ($izin) {
            $izin = GuruIzin::with(['guru.user', 'guru.dapodikGuru', 'jadwals.rombel.siswa.user'])->lockForUpdate()->findOrFail($izin->id);
            abort_unless($izin->status_sdm === 'disetujui' && $izin->status_kepala_sekolah === 'menunggu', 409, 'Izin tidak lagi menunggu persetujuan Kepala Sekolah.');
            abort_unless(EmploymentStatus::normalize($izin->guru?->dapodikGuru?->status_kepegawaian) === EmploymentStatus::PERMANENT, 409, 'Persetujuan Kepala Sekolah hanya berlaku untuk Pegawai Tetap.');
            $izin->update(['status_kepala_sekolah' => 'disetujui', 'kepala_sekolah_id' => auth()->id(), 'kepala_sekolah_at' => now(), 'catatan_kepala_sekolah' => null]);
            $this->finalizeApproval($izin);
        });

        return back()->with('success', 'Izin Pegawai Tetap telah disetujui dan absensi diperbarui.');
    }

    public function reject(Request $request, GuruIzin $izin)
    {
        $data = $request->validate(['catatan_kepala_sekolah' => ['required', 'string', 'max:2000']]);
        DB::transaction(function () use ($izin, $data) {
            $izin = GuruIzin::with(['guru.user', 'guru.dapodikGuru'])->lockForUpdate()->findOrFail($izin->id);
            abort_unless($izin->status_sdm === 'disetujui' && $izin->status_kepala_sekolah === 'menunggu', 409, 'Izin tidak lagi menunggu persetujuan Kepala Sekolah.');
            abort_unless(EmploymentStatus::normalize($izin->guru?->dapodikGuru?->status_kepegawaian) === EmploymentStatus::PERMANENT, 409, 'Persetujuan Kepala Sekolah hanya berlaku untuk Pegawai Tetap.');
            $izin->update(['status_kepala_sekolah' => 'ditolak', 'kepala_sekolah_id' => auth()->id(), 'kepala_sekolah_at' => now(), 'catatan_kepala_sekolah' => $data['catatan_kepala_sekolah']]);
            $izin->guru?->user?->notify(new PengajuanIzinGuruNotification($izin, 'status_updated', 'Permohonan izin Anda ditolak oleh Kepala Sekolah.', route('guru.izin.index')));
        });

        return back()->with('success', 'Izin telah ditolak.');
    }

    private function finalizeApproval(GuruIzin $izin): void
    {
        $izin->guru?->user?->notify(new PengajuanIzinGuruNotification($izin, 'status_updated', 'Permohonan izin Anda telah disetujui sepenuhnya oleh Kepala Sekolah.', route('guru.izin.index')));
        foreach ($izin->jadwals as $jadwal) {
            AbsensiGuru::updateOrCreate(['jadwal_pelajaran_id' => $jadwal->id, 'tanggal' => $izin->tanggal_mulai], [
                'status' => 'izin', 'keterangan' => 'Izin Guru: '.$izin->jenis_izin.' ('.$izin->deskripsi.')',
                'waktu_absen' => now(), 'dicatat_oleh' => auth()->id(),
            ]);
            foreach ($jadwal->rombel->siswa as $student) {
                $student->user?->notify(new \App\Notifications\TeacherAbsenceStudentNotification($izin, $jadwal));
            }
        }
    }
}
