<?php

namespace App\Http\Controllers\Piket;

use App\Http\Controllers\Controller;
use App\Models\GuruIzin;
use App\Models\AbsensiGuru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersetujuanIzinGuruController extends Controller
{
    public function index(Request $request)
    {
        $query = GuruIzin::with([
            'guru', 
            'jadwals.rombel.kelas', 
            'jadwals.mataPelajaran'
        ])->latest();
        
        if ($request->filled('status')) {
            $query->where('status_piket', $request->status);
        } else {
            $query->where('status_piket', 'menunggu');
        }

        $izins = $query->paginate(10);
        
        // Manually load LMS materials and assignments for pivot data
        $this->loadLmsResourcesForIzins($izins);
        
        return view('pages.piket.izin-guru.index', compact('izins'));
    }
    
    /**
     * Load LMS materials and assignments for the pivot data of each izin.
     */
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
        $updateData = [
            'status_piket' => 'disetujui',
            'piket_id' => Auth::id(),
            'piket_at' => now(),
        ];

        // Jika kategori 'sekolah', maka otomatis setujui kurikulum dan sdm
        if ($izin->kategori_penyetujuan === 'sekolah') {
            $updateData['status_kurikulum'] = 'disetujui';
            $updateData['status_sdm'] = 'disetujui';
            $updateData['kurikulum_id'] = Auth::id(); // Menggunakan ID piket sebagai penanggung jawab sementara
            $updateData['sdm_id'] = Auth::id();
            $updateData['kurikulum_at'] = now();
            $updateData['sdm_at'] = now();
            
            $izin->update($updateData);

            // Notify Teacher
            $teacherUser = $izin->guru->user;
            if ($teacherUser) {
                $msg = "Izin Anda (Lingkungan Sekolah) telah disetujui sepenuhnya.";
                $url = route('guru.izin.index');
                $teacherUser->notify(new \App\Notifications\PengajuanIzinGuruNotification($izin, 'status_updated', $msg, $url));
            }

            // Sync to AbsensiGuru (Otomatis karena tuntas di Piket)
            foreach ($izin->jadwals as $jadwal) {
                AbsensiGuru::updateOrCreate(
                    [
                        'jadwal_pelajaran_id' => $jadwal->id,
                        'tanggal' => $izin->tanggal_mulai, 
                    ],
                    [
                        'status' => 'izin',
                        'keterangan' => 'Izin Guru (Lingkungan Sekolah): ' . $izin->jenis_izin . ' (' . $izin->deskripsi . ')',
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

            return redirect()->back()->with('success', 'Permohonan izin (Lingkungan Sekolah) telah disetujui sepenuhnya.');
        }

        $izin->update($updateData);

        // Notify Kurikulum
        $approvers = \App\Models\User::role('waka kurikulum')->get();
        $msg = "Ada pengajuan Izin Guru (Luar Sekolah) baru dari " . $izin->guru->nama_lengkap;
        $url = route('kurikulum.persetujuan-izin-guru.index');
        foreach ($approvers as $approver) {
            $approver->notify(new \App\Notifications\PengajuanIzinGuruNotification($izin, 'pending_approval', $msg, $url));
        }

        return redirect()->back()->with('success', 'Permohonan izin diteruskan ke Waka Kurikulum.');
    }

    public function reject(Request $request, GuruIzin $izin)
    {
        $request->validate(['catatan_piket' => 'required|string']);
        
        $izin->update([
            'status_piket' => 'ditolak',
            'piket_id' => Auth::id(),
            'piket_at' => now(),
            'catatan_piket' => $request->catatan_piket,
        ]);

        // Notify Teacher
        $teacherUser = $izin->guru->user;
        if ($teacherUser) {
            $msg = "Permohonan izin Anda ditolak oleh Guru Piket.";
            $url = route('guru.izin.index');
            $teacherUser->notify(new \App\Notifications\PengajuanIzinGuruNotification($izin, 'status_updated', $msg, $url));
        }

        return redirect()->back()->with('info', 'Permohonan izin telah ditolak.');
    }
}
