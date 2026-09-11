<?php

namespace App\Http\Controllers\Kurikulum;

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
        ])->where('kategori_penyetujuan', 'luar')->where('status_piket', 'disetujui')->latest();

        if ($request->filled('status')) {
            $query->where('status_kurikulum', $request->status);
        } else {
            $query->where('status_kurikulum', 'menunggu');
        }

        $izins = $query->paginate(10);

        // Manually load LMS materials and assignments for pivot data
        $this->loadLmsResourcesForIzins($izins);

        return view('pages.kurikulum.izin-guru.index', compact('izins'));
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

    public function approve(GuruIzin $izin, PicketTeacherLeaveDecisionService $decisions)
    {
        $decisions->approveKurikulum($izin, Auth::user());

        return redirect()->back()->with('success', 'Permohonan izin diteruskan ke KAUR SDM.');
    }

    public function reject(Request $request, GuruIzin $izin, PicketTeacherLeaveDecisionService $decisions)
    {
        $request->validate(['catatan_kurikulum' => 'required|string']);
        $decisions->rejectKurikulum($izin, Auth::user(), $request->catatan_kurikulum);

        return redirect()->back()->with('info', 'Permohonan izin telah ditolak oleh Waka Kurikulum.');
    }
}
