<?php

namespace App\Http\Controllers\SDM;

use App\Http\Controllers\Controller;
use App\Models\GuruIzin;
use App\Models\MasterGuru;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class RekapitulasiController extends Controller
{
    public function index(Request $request)
    {
        $query = GuruIzin::with(['guru'])->where('status_sdm', 'disetujui');

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_mulai', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_selesai', '<=', $request->end_date);
        }
        if ($request->filled('guru_id')) {
            $query->where('master_guru_id', $request->guru_id);
        }
        if ($request->filled('kategori')) {
            $query->where('kategori_penyetujuan', $request->kategori);
        }

        $izins = $query->latest()->paginate(20)->withQueryString();
        $gurus = MasterGuru::all();

        // Generate Chart Data based on filters
        $startDate = $request->filled('start_date') ? \Carbon\Carbon::parse($request->start_date) : now()->subDays(6);
        $endDate = $request->filled('end_date') ? \Carbon\Carbon::parse($request->end_date) : now();
        
        // Limit range to prevent performance issues if too long
        if ($startDate->diffInDays($endDate) > 31) {
            $startDate = $endDate->copy()->subDays(31);
        }

        $chartData = [
            'labels' => [],
            'izin_sekolah' => [],
            'izin_luar' => [],
            'terlambat' => [],
        ];

        $current = $startDate->copy();
        while ($current <= $endDate) {
            $dateStr = $current->format('Y-m-d');
            $chartData['labels'][] = $current->translatedFormat('d M');
            
            // Base query for each day respects guru filter
            $dayQuery = GuruIzin::whereDate('tanggal_mulai', $dateStr)->where('status_sdm', 'disetujui');
            if ($request->filled('guru_id')) {
                $dayQuery->where('master_guru_id', $request->guru_id);
            }

            // If a specific category is filtered, only count that category
            $filteredKategori = $request->kategori;
            
            $chartData['izin_sekolah'][] = (!$filteredKategori || $filteredKategori === 'sekolah') 
                ? (clone $dayQuery)->where('kategori_penyetujuan', 'sekolah')->count() : 0;
            
            $chartData['izin_luar'][] = (!$filteredKategori || $filteredKategori === 'luar') 
                ? (clone $dayQuery)->where('kategori_penyetujuan', 'luar')->count() : 0;
            
            $chartData['terlambat'][] = (!$filteredKategori || $filteredKategori === 'terlambat') 
                ? (clone $dayQuery)->where('kategori_penyetujuan', 'terlambat')->count() : 0;

            $current->addDay();
        }

        return view('pages.sdm.monitoring.rekapitulasi', compact('izins', 'gurus', 'chartData'));
    }

    public function exportPdf(Request $request)
    {
        $query = GuruIzin::with(['guru'])->where('status_sdm', 'disetujui');

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_mulai', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_selesai', '<=', $request->end_date);
        }
        if ($request->filled('guru_id')) {
            $query->where('master_guru_id', $request->guru_id);
        }
        if ($request->filled('kategori')) {
            $query->where('kategori_penyetujuan', $request->kategori);
        }

        $izins = $query->latest()->get();
        
        $pdf = Pdf::loadView('pdf.sdm.rekapitulasi_izin', [
            'izins' => $izins,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'sdm_name' => Auth::user()->name
        ]);

        return $pdf->download('Rekapitulasi_Izin_Guru_' . now()->format('YmdHis') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        // For simplicity, we'll use a standard HTML table for Excel export 
        // to avoid dependency on complicated Excel library if not already set up.
        // But the user requested Excel, so let's try to output CSV or a simple TSV if needed.
        // In Laravel, most people use Maatwebsite/Laravel-Excel. 
        // I'll provide a simple CSV export as a fallback.
        
        $query = GuruIzin::with(['guru'])->where('status_sdm', 'disetujui');

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_mulai', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_selesai', '<=', $request->end_date);
        }
        if ($request->filled('guru_id')) {
            $query->where('master_guru_id', $request->guru_id);
        }
        if ($request->filled('kategori')) {
            $query->where('kategori_penyetujuan', $request->kategori);
        }
        
        $izins = $query->latest()->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=rekapitulasi_izin_guru_" . now()->format('YmdHis') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Nama Guru', 'Kategori', 'Jenis Izin', 'Tanggal Mulai', 'Tanggal Selesai', 'Deskripsi'];

        $callback = function() use($izins, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($izins as $izin) {
                fputcsv($file, [
                    $izin->id,
                    $izin->guru->nama_lengkap,
                    $izin->kategori_penyetujuan === 'sekolah' ? 'Lingkungan Sekolah' : ($izin->kategori_penyetujuan === 'terlambat' ? 'Terlambat' : 'Luar Sekolah'),
                    $izin->jenis_izin,
                    $izin->tanggal_mulai->format('Y-m-d H:i'),
                    $izin->tanggal_selesai->format('Y-m-d H:i'),
                    $izin->deskripsi,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
