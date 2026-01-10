<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Keterlambatan;
use App\Models\Kelas;
use App\Models\Rombel;
use App\Exports\KeterlambatanExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MonitoringKeterlambatanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Keterlambatan::with(['siswa.user', 'siswa.rombels.kelas', 'security', 'guruPiket']);

        // Role-based Data Scoping
        if ($user->hasRole('Wali Kelas')) {
            // Wali Kelas can only see their own students in active rombels
            $query->whereHas('siswa.rombels', function ($q) use ($user) {
                $q->where('wali_kelas_id', $user->id);
            });
        } elseif ($user->hasRole('Guru Kelas')) {
            // Guru Kelas see their students (assumed same as Wali Kelas for now, or based on teaching schedule)
            // For now, let's assume they see all for the classes they teach or just let them see all if they have the role.
            // Adjust based on project requirements.
        }
        
        // Filter by Date
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('waktu_dicatat_security', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Kelas
        if ($request->filled('kelas_id')) {
            $kelasId = $request->kelas_id;
            $query->whereHas('siswa.rombels', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        // Filter by Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('siswa.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('siswa', function ($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%");
            });
        }

        $data = $query->latest('waktu_dicatat_security')->paginate(15)->withQueryString();
        $kelas = Kelas::orderBy('nama_kelas')->get();

        return view('pages.shared.monitoring-keterlambatan.index', compact('data', 'kelas'));
    }

    public function export(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date', 'status', 'kelas_id']);
        
        if (Auth::user()->hasRole('Wali Kelas')) {
            $filters['wali_kelas_id'] = Auth::id();
        }

        $type = $request->export ?? 'excel';
        $fileName = 'rekap-keterlambatan-' . now()->format('Y-md-His');

        if ($type === 'pdf') {
            $query = Keterlambatan::with(['siswa.user', 'siswa.rombels.kelas', 'security', 'guruPiket']);

            if (isset($filters['start_date']) && isset($filters['end_date'])) {
                $query->whereBetween('waktu_dicatat_security', [$filters['start_date'] . ' 00:00:00', $filters['end_date'] . ' 23:59:59']);
            }

            if (isset($filters['status']) && $filters['status']) {
                $query->where('status', $filters['status']);
            }

            if (isset($filters['kelas_id']) && $filters['kelas_id']) {
                $kelasId = $filters['kelas_id'];
                $query->whereHas('siswa.rombels', function ($q) use ($kelasId) {
                    $q->where('kelas_id', $kelasId);
                });
            }

            if (isset($filters['wali_kelas_id'])) {
                $query->whereHas('siswa.rombels', function ($q) use ($filters) {
                    $q->where('wali_kelas_id', $filters['wali_kelas_id']);
                });
            }

            $data = $query->latest('waktu_dicatat_security')->get();
            $pdf = Pdf::loadView('pdf.rekap-keterlambatan', compact('data', 'filters'));
            return $pdf->download($fileName . '.pdf');
        }
        
        return Excel::download(new KeterlambatanExport($filters), $fileName . '.xlsx');
    }

    public function show(Keterlambatan $keterlambatan)
    {
        $keterlambatan->load(['siswa.user', 'siswa.rombels.kelas', 'security', 'guruPiket', 'guruKelasVerifier', 'bkProcessor']);
        return view('pages.shared.monitoring-keterlambatan.show', compact('keterlambatan'));
    }

    public function printSlip(Keterlambatan $keterlambatan)
    {
        $keterlambatan->load(['siswa.user', 'siswa.rombels.kelas', 'security', 'guruPiket', 'jadwalPelajaran.mataPelajaran', 'jadwalPelajaran.guru']);
        
        // Generate QR Codes required by the template
        $publicUrl = route('verifikasi.surat-terlambat', $keterlambatan->uuid);
        $publicQrCode = 'data:image/svg+xml;base64,' . base64_encode(QrCode::format('svg')->size(60)->generate($publicUrl));

        $guruKelasUrl = route('guru-kelas.verifikasi-terlambat.scan', $keterlambatan->uuid);
        $guruKelasQrCode = 'data:image/svg+xml;base64,' . base64_encode(QrCode::format('svg')->size(60)->generate($guruKelasUrl));

        // Use the existing PDF template
        $pdf = Pdf::loadView('pdf.surat-izin-masuk-kelas', compact('keterlambatan', 'publicQrCode', 'guruKelasQrCode'));
        return $pdf->stream('surat-izin-masuk-' . $keterlambatan->siswa->user->name . '.pdf');
    }
}
