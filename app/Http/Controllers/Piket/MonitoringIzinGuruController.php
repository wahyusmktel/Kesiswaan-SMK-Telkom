<?php

namespace App\Http\Controllers\Piket;

use App\Http\Controllers\Controller;
use App\Models\GuruIzin;
use App\Models\MasterGuru;
use Illuminate\Http\Request;

class MonitoringIzinGuruController extends Controller
{
    public function index(Request $request)
    {
        $query = GuruIzin::with([
            'guru', 
            'piket',
            'kurikulum',
            'sdm',
            'jadwals.rombel.kelas', 
            'jadwals.mataPelajaran'
        ])->latest();

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereDate('tanggal_mulai', '>=', $request->start_date)
                  ->whereDate('tanggal_selesai', '<=', $request->end_date);
        }

        // Filter by teacher
        if ($request->filled('guru_id')) {
            $query->where('master_guru_id', $request->guru_id);
        }

        // Filter by status (menunggu, disetujui, ditolak)
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'menunggu':
                    $query->where(function ($q) {
                        $q->where('status_piket', 'menunggu')
                          ->orWhere('status_kurikulum', 'menunggu')
                          ->orWhere('status_sdm', 'menunggu');
                    });
                    break;
                case 'disetujui':
                    $query->where('status_sdm', 'disetujui');
                    break;
                case 'ditolak':
                    $query->where(function ($q) {
                        $q->where('status_piket', 'ditolak')
                          ->orWhere('status_kurikulum', 'ditolak')
                          ->orWhere('status_sdm', 'ditolak');
                    });
                    break;
            }
        }

        // Filter by kategori
        if ($request->filled('kategori')) {
            $query->where('kategori_penyetujuan', $request->kategori);
        }

        $izins = $query->paginate(15)->withQueryString();
        
        // Load LMS resources manually
        $this->loadLmsResourcesForIzins($izins);

        $gurus = MasterGuru::orderBy('nama_lengkap')->get();

        // Stats
        $stats = [
            'total' => GuruIzin::count(),
            'menunggu' => GuruIzin::where('status_piket', 'menunggu')
                            ->orWhere(function($q) {
                                $q->where('status_piket', 'disetujui')
                                  ->where('status_kurikulum', 'menunggu');
                            })
                            ->orWhere(function($q) {
                                $q->where('status_kurikulum', 'disetujui')
                                  ->where('status_sdm', 'menunggu');
                            })->count(),
            'disetujui' => GuruIzin::where('status_sdm', 'disetujui')->count(),
            'ditolak' => GuruIzin::where('status_piket', 'ditolak')
                            ->orWhere('status_kurikulum', 'ditolak')
                            ->orWhere('status_sdm', 'ditolak')->count(),
        ];

        return view('pages.piket.monitoring-izin-guru.index', compact('izins', 'gurus', 'stats'));
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
}
