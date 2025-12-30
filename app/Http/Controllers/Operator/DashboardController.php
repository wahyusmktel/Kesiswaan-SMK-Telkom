<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\MasterSiswa;
use App\Models\Rombel;
use App\Models\Kelas;
use App\Models\User;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Basic Stats
        $totalStudents = MasterSiswa::count();
        $totalActiveRombel = Rombel::whereHas('tahunPelajaran', function($q) {
            $q->where('is_active', true);
        })->count();
        $totalGuruKelas = User::role('Guru Kelas')->count();
        
        // 2. Students by Gender per Class (Aggregated)
        // We fetch active rombels and their student counts split by gender
        $activeYear = TahunPelajaran::where('is_active', true)->first();
        
        $classStats = [];
        if ($activeYear) {
            $classStats = Rombel::with(['kelas', 'siswa'])
                ->where('tahun_pelajaran_id', $activeYear->id)
                ->get()
                ->map(function ($rombel) {
                    return [
                        'name' => $rombel->kelas->nama_kelas,
                        'total' => $rombel->siswa->count(),
                        'male' => $rombel->siswa->where('jenis_kelamin', 'L')->count(),
                        'female' => $rombel->siswa->where('jenis_kelamin', 'P')->count(),
                    ];
                });
        }

        // 3. Students by Major (Jurusan)
        $majorStats = [];
        if ($activeYear) {
            $majorStats = DB::table('master_siswa')
                ->join('rombel_siswa', 'master_siswa.id', '=', 'rombel_siswa.master_siswa_id')
                ->join('rombels', 'rombel_siswa.rombel_id', '=', 'rombels.id')
                ->join('kelas', 'rombels.kelas_id', '=', 'kelas.id')
                ->where('rombels.tahun_pelajaran_id', $activeYear->id)
                ->select('kelas.jurusan', DB::raw('count(distinct master_siswa.id) as total'))
                ->groupBy('kelas.jurusan')
                ->get();
        }
            
        // 4. Students by Level (X, XI, XII)
        // Assuming nama_kelas starts with X, XI, or XII
        $levelStats = [
            'X' => Kelas::where('nama_kelas', 'like', 'X %')->count(),
            'XI' => Kelas::where('nama_kelas', 'like', 'XI %')->count(),
            'XII' => Kelas::where('nama_kelas', 'like', 'XII %')->count(),
        ];
        
        // Alternative Level Calculation based on students in active rombels
        $studentLevelStats = [
            'X' => 0,
            'XI' => 0,
            'XII' => 0,
        ];
        
        if ($activeYear) {
             $studentLevelStats['X'] = MasterSiswa::whereHas('rombels', function($q) use ($activeYear) {
                $q->where('tahun_pelajaran_id', $activeYear->id)
                  ->whereHas('kelas', function($kq) {
                      $kq->where('nama_kelas', 'like', 'X %');
                  });
            })->count();
            
            $studentLevelStats['XI'] = MasterSiswa::whereHas('rombels', function($q) use ($activeYear) {
                $q->where('tahun_pelajaran_id', $activeYear->id)
                  ->whereHas('kelas', function($kq) {
                      $kq->where('nama_kelas', 'like', 'XI %');
                  });
            })->count();
            
            $studentLevelStats['XII'] = MasterSiswa::whereHas('rombels', function($q) use ($activeYear) {
                $q->where('tahun_pelajaran_id', $activeYear->id)
                  ->whereHas('kelas', function($kq) {
                      $kq->where('nama_kelas', 'like', 'XII %');
                  });
            })->count();
        }

        // 5. Recent Added Students
        $recentStudents = MasterSiswa::latest()->take(5)->get();

        return view('pages.operator.dashboard', compact(
            'totalStudents',
            'totalActiveRombel',
            'totalGuruKelas',
            'classStats',
            'majorStats',
            'studentLevelStats',
            'recentStudents',
            'activeYear'
        ));
    }
}
