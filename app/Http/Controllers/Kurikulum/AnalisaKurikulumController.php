<?php

namespace App\Http\Controllers\Kurikulum;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\MasterGuru;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalisaKurikulumController extends Controller
{
    public function index()
    {
        // 1. Dapatkan Tahun Pelajaran Aktif
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        
        if (!$tahunAktif) {
            return view('pages.kurikulum.analisa.index', [
                'error' => 'Tidak ada Tahun Pelajaran aktif found.'
            ]);
        }

        // 2. Tentukan Rentang Tanggal Semester (Asumsi: 6 bulan)
        // Jika tidak ada data spesifik, kita gunakan rentang dari data absensi tertua di tahun ini
        $startDate = Carbon::now()->startOfYear();
        $endDate = Carbon::now()->endOfYear();

        // 3. Ambil data absensi semester ini
        $absensiRaw = AbsensiGuru::with(['jadwalPelajaran.guru'])
            ->whereHas('jadwalPelajaran.rombel', function($q) use ($tahunAktif) {
                $q->where('tahun_pelajaran_id', $tahunAktif->id);
            })
            ->get();

        // 4. Statistik Utama
        $totalRecord = $absensiRaw->count();
        $totalHadir = $absensiRaw->where('status', 'hadir')->count();
        $totalTerlambat = $absensiRaw->where('status', 'terlambat')->count();
        $totalIzin = $absensiRaw->where('status', 'izin')->count();
        $totalAlpa = $absensiRaw->where('status', 'tidak_hadir')->count();

        $kehadiranPersen = $totalRecord > 0 ? round(($totalHadir / $totalRecord) * 100, 1) : 0;

        // 5. Tren Bulanan (Chart.js)
        $trends = $absensiRaw->groupBy(function($item) {
            return $item->tanggal->format('M Y');
        })->map(function($monthData) {
            return [
                'hadir' => $monthData->where('status', 'hadir')->count(),
                'terlambat' => $monthData->where('status', 'terlambat')->count(),
                'izin' => $monthData->where('status', 'izin')->count(),
                'total' => $monthData->count(),
            ];
        });

        // 6. Top 5 Guru (Paling Disiplin & Paling Sering Terlambat)
        $guruStats = $absensiRaw->groupBy('jadwalPelajaran.master_guru_id')->map(function($items) {
            $guru = $items->first()->jadwalPelajaran->guru;
            return [
                'nama' => $guru->nama_lengkap ?? 'Unknown',
                'hadir' => $items->where('status', 'hadir')->count(),
                'terlambat' => $items->where('status', 'terlambat')->count(),
                'total' => $items->count(),
                'persentase' => round(($items->where('status', 'hadir')->count() / $items->count()) * 100, 1)
            ];
        })->values();

        $topDisiplin = $guruStats->sortByDesc('persentase')->take(5);
        $topTerlambat = $guruStats->sortByDesc('terlambat')->take(5);

        return view('pages.kurikulum.analisa.index', compact(
            'tahunAktif', 'kehadiranPersen', 'totalHadir', 'totalTerlambat', 'totalIzin', 'totalAlpa',
            'trends', 'topDisiplin', 'topTerlambat'
        ));
    }

    public function exportPdf()
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        
        if (!$tahunAktif) {
            return redirect()->back()->with('error', 'Tidak ada Tahun Pelajaran aktif.');
        }

        $absensiRaw = AbsensiGuru::with(['jadwalPelajaran.guru', 'jadwalPelajaran.mataPelajaran'])
            ->whereHas('jadwalPelajaran.rombel', function($q) use ($tahunAktif) {
                $q->where('tahun_pelajaran_id', $tahunAktif->id);
            })
            ->get();

        $totalRecord = $absensiRaw->count();
        $totalHadir = $absensiRaw->where('status', 'hadir')->count();
        $totalTerlambat = $absensiRaw->where('status', 'terlambat')->count();
        $totalIzin = $absensiRaw->where('status', 'izin')->count();
        $totalAlpa = $absensiRaw->where('status', 'tidak_hadir')->count();

        $kehadiranPersen = $totalRecord > 0 ? round(($totalHadir / $totalRecord) * 100, 1) : 0;

        $trends = $absensiRaw->groupBy(function($item) {
            return $item->tanggal->format('M Y');
        })->map(function($monthData) {
            return [
                'hadir' => $monthData->where('status', 'hadir')->count(),
                'terlambat' => $monthData->where('status', 'terlambat')->count(),
                'izin' => $monthData->where('status', 'izin')->count(),
                'total' => $monthData->count(),
            ];
        });

        $guruStats = $absensiRaw->groupBy('jadwalPelajaran.master_guru_id')->map(function($items) {
            $guru = $items->first()->jadwalPelajaran->guru;
            return [
                'nama' => $guru->nama_lengkap ?? 'Unknown',
                'hadir' => $items->where('status', 'hadir')->count(),
                'terlambat' => $items->where('status', 'terlambat')->count(),
                'total' => $items->count(),
                'persentase' => round(($items->where('status', 'hadir')->count() / $items->count()) * 100, 1)
            ];
        })->sortByDesc('persentase')->values();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.kurikulum.analisa_semester', compact(
            'tahunAktif', 'kehadiranPersen', 'totalHadir', 'totalTerlambat', 'totalIzin', 'totalAlpa',
            'trends', 'guruStats', 'totalRecord'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('Audit_Report_Kurikulum_' . str_replace('/', '-', $tahunAktif->tahun_ajaran) . '.pdf');
    }

    public function export()
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $fileName = 'Audit_Report_Kurikulum_' . str_replace('/', '-', $tahunAktif->tahun_ajaran) . '.xlsx';
        
        return (new \App\Exports\SemesterAuditExport($tahunAktif->id))->download($fileName);
    }
}
