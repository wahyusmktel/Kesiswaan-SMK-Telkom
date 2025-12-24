<?php

namespace App\Http\Controllers\Kurikulum;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\MasterGuru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonitoringAbsensiGuruController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->format('Y-m-d');
        $guruId = $request->guru_id;
        $status = $request->status;

        $query = AbsensiGuru::with(['jadwalPelajaran.guru', 'jadwalPelajaran.mataPelajaran', 'jadwalPelajaran.rombel.kelas'])
            ->whereBetween('tanggal', [$startDate, $endDate]);

        if ($guruId) {
            $query->whereHas('jadwalPelajaran', function($q) use ($guruId) {
                $q->where('master_guru_id', $guruId);
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        // Clone query for statistics before pagination
        $statsQuery = clone $query;
        $totalHadir = (clone $statsQuery)->where('status', 'hadir')->count();
        $totalTerlambat = (clone $statsQuery)->where('status', 'terlambat')->count();
        $totalTidakHadir = (clone $statsQuery)->where('status', 'tidak_hadir')->count();
        $totalIzin = (clone $statsQuery)->where('status', 'izin')->count();

        $absensi = $query->latest('tanggal')->latest('waktu_absen')->paginate(10);
        $gurus = MasterGuru::orderBy('nama_lengkap')->get();

        return view('pages.kurikulum.monitoring-absensi-guru.index', compact(
            'absensi', 'gurus', 'startDate', 'endDate', 'guruId', 'status',
            'totalHadir', 'totalTerlambat', 'totalTidakHadir', 'totalIzin'
        ));
    }

    public function export(Request $request) 
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->format('Y-m-d');
        $guruId = $request->guru_id;
        $status = $request->status;

        $fileName = 'Laporan_Absensi_Guru_' . Carbon::parse($startDate)->format('d_m_Y') . '_sd_' . Carbon::parse($endDate)->format('d_m_Y') . '.xlsx';

        return (new \App\Exports\AbsensiGuruExport($startDate, $endDate, $guruId, $status))->download($fileName);
    }
}
