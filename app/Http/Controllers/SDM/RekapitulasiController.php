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

        $izins = $query->latest()->paginate(20)->withQueryString();
        $gurus = MasterGuru::all();

        return view('pages.sdm.monitoring.rekapitulasi', compact('izins', 'gurus'));
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

        $izins = $query->latest()->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=rekapitulasi_izin_guru_" . now()->format('YmdHis') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Nama Guru', 'Jenis Izin', 'Tanggal Mulai', 'Tanggal Selesai', 'Deskripsi'];

        $callback = function() use($izins, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($izins as $izin) {
                fputcsv($file, [
                    $izin->id,
                    $izin->guru->nama_lengkap,
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
