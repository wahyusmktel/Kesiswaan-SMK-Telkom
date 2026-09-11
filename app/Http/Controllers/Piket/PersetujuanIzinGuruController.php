<?php

namespace App\Http\Controllers\Piket;

use App\Http\Controllers\Controller;
use App\Models\GuruIzin;
use App\Services\PicketTeacherLeaveDecisionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersetujuanIzinGuruController extends Controller
{
    public function index(Request $request)
    {
        $query = GuruIzin::with([
            'guru',
            'jadwals.rombel.kelas',
            'jadwals.mataPelajaran',
        ])->whereIn('kategori_penyetujuan', ['sekolah', 'luar'])->latest();

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

    public function approve(GuruIzin $izin, PicketTeacherLeaveDecisionService $decisions)
    {
        $result = $decisions->approve($izin, Auth::user());

        return redirect()->back()->with('success', $result['complete']
            ? 'Permohonan izin (Lingkungan Sekolah) telah disetujui sepenuhnya.'
            : 'Permohonan izin diteruskan ke Waka Kurikulum.');
    }

    public function reject(Request $request, GuruIzin $izin, PicketTeacherLeaveDecisionService $decisions)
    {
        $request->validate(['catatan_piket' => 'required|string']);
        $decisions->reject($izin, Auth::user(), $request->catatan_piket);

        return redirect()->back()->with('info', 'Permohonan izin telah ditolak.');
    }
}
