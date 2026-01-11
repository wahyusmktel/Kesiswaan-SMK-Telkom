<?php

namespace App\Http\Controllers\SDM;

use App\Http\Controllers\Controller;
use App\Models\GuruIzin;
use App\Models\AbsensiGuru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AppSetting;
use Barryvdh\DomPDF\Facade\Pdf;

class PersetujuanIzinGuruController extends Controller
{
    public function index(Request $request)
    {
        $query = GuruIzin::with([
            'guru', 
            'jadwals.rombel.kelas', 
            'jadwals.mataPelajaran'
        ])->where('status_kurikulum', 'disetujui')->latest();
        
        if ($request->filled('status')) {
            $query->where('status_sdm', $request->status);
        } else {
            $query->where('status_sdm', 'menunggu');
        }

        $izins = $query->paginate(10);
        
        // Manually load LMS materials and assignments for pivot data
        $this->loadLmsResourcesForIzins($izins);
        
        return view('pages.sdm.izin-guru.index', compact('izins'));
    }
    
    private function loadLmsResourcesForIzins($izins)
    {
        $materialIds = [];
        $assignmentIds = [];
        
        foreach ($izins as $izin) {
            foreach ($izin->jadwals as $jadwal) {
                if ($jadwal->pivot->lms_material_id) {
                    $materialIds[] = $jadwal->pivot->lms_material_id;
                }
                if ($jadwal->pivot->lms_assignment_id) {
                    $assignmentIds[] = $jadwal->pivot->lms_assignment_id;
                }
            }
        }
        
        $materials = \App\Models\LmsMaterial::whereIn('id', array_unique($materialIds))->get()->keyBy('id');
        $assignments = \App\Models\LmsAssignment::whereIn('id', array_unique($assignmentIds))->get()->keyBy('id');
        
        foreach ($izins as $izin) {
            foreach ($izin->jadwals as $jadwal) {
                $jadwal->pivot->loadedMaterial = $jadwal->pivot->lms_material_id 
                    ? $materials->get($jadwal->pivot->lms_material_id) 
                    : null;
                $jadwal->pivot->loadedAssignment = $jadwal->pivot->lms_assignment_id 
                    ? $assignments->get($jadwal->pivot->lms_assignment_id) 
                    : null;
            }
        }
    }

    public function approve(GuruIzin $izin)
    {
        $izin->update([
            'status_sdm' => 'disetujui',
            'sdm_id' => Auth::id(),
            'sdm_at' => now(),
        ]);

        // Notify Teacher
        $teacherUser = $izin->guru->user;
        if ($teacherUser) {
            $msg = "Permohonan izin Anda telah disetujui sepenuhnya oleh KAUR SDM.";
            $url = route('guru.izin.index');
            $teacherUser->notify(new \App\Notifications\PengajuanIzinGuruNotification($izin, 'status_updated', $msg, $url));
        }

        // Sync to AbsensiGuru
        foreach ($izin->jadwals as $jadwal) {
            AbsensiGuru::updateOrCreate(
                [
                    'jadwal_pelajaran_id' => $jadwal->id,
                    'tanggal' => $izin->tanggal_mulai, 
                ],
                [
                    'status' => 'izin',
                    'keterangan' => 'Izin Guru: ' . $izin->jenis_izin . ' (' . $izin->deskripsi . ')',
                    'waktu_absen' => now(),
                    'dicatat_oleh' => Auth::id(),
                ]
            );

            // Notify Students
            $students = $jadwal->rombel->siswa()->with('user')->get();
            foreach ($students as $student) {
                if ($student->user) {
                    $student->user->notify(new \App\Notifications\TeacherAbsenceStudentNotification($izin, $jadwal));
                }
            }
        }

        return redirect()->back()->with('success', 'Permohonan izin telah disetujui sepenuhnya dan absensi telah diperbarui.');
    }

    public function reject(Request $request, GuruIzin $izin)
    {
        $request->validate(['catatan_sdm' => 'required|string']);
        
        $izin->update([
            'status_sdm' => 'ditolak',
            'sdm_id' => Auth::id(),
            'sdm_at' => now(),
            'catatan_sdm' => $request->catatan_sdm,
        ]);

        // Notify Teacher
        $teacherUser = $izin->guru->user;
        if ($teacherUser) {
            $msg = "Permohonan izin Anda ditolak oleh KAUR SDM.";
            $url = route('guru.izin.index');
            $teacherUser->notify(new \App\Notifications\PengajuanIzinGuruNotification($izin, 'status_updated', $msg, $url));
        }

        return redirect()->back()->with('info', 'Permohonan izin telah ditolak oleh KAUR SDM.');
    }

    public function printPdf(GuruIzin $izin)
    {
        if ($izin->status_sdm !== 'disetujui') {
            abort(403, 'Surat izin belum disetujui oleh KAUR SDM.');
        }

        // Security check: If teacher, only allow printing their own permit
        // Bypass this if user also has KAUR SDM role
        $user = Auth::user();
        if ($user->hasRole('Guru Kelas') && !$user->hasRole('KAUR SDM')) {
            $guru = $user->masterGuru;
            if (!$guru || $izin->master_guru_id !== $guru->id) {
                abort(403, 'Anda tidak memiliki akses untuk mengunduh surat izin ini.');
            }
        }

        $izin->load([
            'guru', 
            'piket', 
            'kurikulum', 
            'sdm', 
            'jadwals.rombel.kelas', 
            'jadwals.mataPelajaran'
        ]);
        
        $settings = AppSetting::first();
        
        $pdf = Pdf::loadView('pdf.izin-guru', compact('izin', 'settings'));
        return $pdf->stream('Surat_Izin_Guru_' . str_replace(' ', '_', $izin->guru->nama_lengkap) . '.pdf');
    }
}
