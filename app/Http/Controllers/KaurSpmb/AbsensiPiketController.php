<?php

namespace App\Http\Controllers\KaurSpmb;

use App\Exports\SpmbPiketAbsensiExport;
use App\Http\Controllers\Controller;
use App\Models\SpmbPiketAbsensi;
use App\Models\SpmbPiketKelompok;
use App\Models\SpmbPiketPetugas;
use App\Models\SpmbPiketTanggal;
use App\Models\TahunPelajaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class AbsensiPiketController extends Controller
{
    /**
     * Display listing of SPMB duty attendances.
     */
    public function index(Request $request)
    {
        // 1. Resolve Academic Year
        $dbYears = SpmbPiketKelompok::distinct()->pluck('tahun_ajaran')->toArray();
        $tpYears = TahunPelajaran::distinct()->pluck('tahun')->toArray();
        $availableYears = array_unique(array_filter(array_merge($dbYears, $tpYears, ['2027/2028', '2026/2027', '2025/2026'])));
        rsort($availableYears);

        $selectedYear = $request->get('tahun_ajaran');
        if (! $selectedYear || ! in_array($selectedYear, $availableYears)) {
            $selectedYear = in_array('2027/2028', $availableYears) ? '2027/2028' : ($availableYears[0] ?? '2027/2028');
        }

        $activeTab = $request->get('tab', 'daftar'); // 'daftar' | 'rekap'
        $selectedTanggalId = $request->get('tanggal_id');
        $selectedStatus = $request->get('status');
        $search = trim($request->get('search', ''));
        $perPage = (int) $request->get('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        // Available scheduled Saturdays for dropdown
        $allTanggals = SpmbPiketTanggal::whereHas('kelompok', function ($q) use ($selectedYear) {
            $q->where('tahun_ajaran', $selectedYear);
        })
        ->with('kelompok')
        ->orderBy('tanggal', 'asc')
        ->get();

        // 2. Query Attendances
        $absensiQuery = SpmbPiketAbsensi::whereHas('tanggal.kelompok', function ($q) use ($selectedYear) {
            $q->where('tahun_ajaran', $selectedYear);
        })
        ->with(['petugas.kelompok', 'tanggal.kelompok', 'tanggal.penggantis', 'user', 'createdBy'])
        ->orderBy('created_at', 'desc');

        if (! empty($selectedTanggalId)) {
            $absensiQuery->where('spmb_piket_tanggal_id', $selectedTanggalId);
        }

        if (! empty($selectedStatus)) {
            $absensiQuery->where('status', $selectedStatus);
        }

        if ($search !== '') {
            $absensiQuery->where(function ($q) use ($search) {
                $q->where('keterangan', 'like', "%{$search}%")
                    ->orWhereHas('petugas', function ($sub) use ($search) {
                        $sub->where('nama_petugas', 'like', "%{$search}%");
                    })
                    ->orWhereHas('tanggal', function ($sub) use ($search) {
                        $sub->where('tanggal', 'like', "%{$search}%")
                            ->orWhere('hari', 'like', "%{$search}%");
                    });
            });
        }

        $absensis = $absensiQuery->paginate($perPage)->appends($request->query());

        // 3. Overall Statistics for Selected Year
        $baseStatsQuery = SpmbPiketAbsensi::whereHas('tanggal.kelompok', function ($q) use ($selectedYear) {
            $q->where('tahun_ajaran', $selectedYear);
        });

        if (! empty($selectedTanggalId)) {
            $baseStatsQuery->where('spmb_piket_tanggal_id', $selectedTanggalId);
        }

        $totalHadir = (clone $baseStatsQuery)->where('status', 'Hadir')->count();
        $totalIzin = (clone $baseStatsQuery)->where('status', 'Izin')->count();
        $totalSakit = (clone $baseStatsQuery)->where('status', 'Sakit')->count();
        $totalAlpa = (clone $baseStatsQuery)->where('status', 'Alpa')->count();
        $totalAbsen = $totalHadir + $totalIzin + $totalSakit + $totalAlpa;

        // Total expected duties
        $totalJadwalPegawai = 0;
        if (! empty($selectedTanggalId)) {
            $tgl = $allTanggals->firstWhere('id', $selectedTanggalId);
            $totalJadwalPegawai = $tgl ? $tgl->kelompok->petugas()->count() : 0;
        } else {
            foreach ($allTanggals as $tgl) {
                $totalJadwalPegawai += $tgl->kelompok->petugas()->count();
            }
        }

        $persentaseKehadiran = $totalJadwalPegawai > 0
            ? round(($totalHadir / $totalJadwalPegawai) * 100, 1)
            : ($totalAbsen > 0 ? round(($totalHadir / $totalAbsen) * 100, 1) : 0);

        $stats = [
            'total_jadwal' => $totalJadwalPegawai,
            'total_absen' => $totalAbsen,
            'hadir' => $totalHadir,
            'izin' => $totalIzin,
            'sakit' => $totalSakit,
            'alpa' => $totalAlpa,
            'persentase' => $persentaseKehadiran,
        ];

        // 4. Rekapitulasi per Pegawai
        $allPetugas = SpmbPiketPetugas::whereHas('kelompok', function ($q) use ($selectedYear) {
            $q->where('tahun_ajaran', $selectedYear);
        })
        ->with(['kelompok.tanggals', 'absensis'])
        ->get();

        $rekapPerPegawai = $allPetugas->map(function ($p) {
            $totalPiket = $p->kelompok ? $p->kelompok->tanggals->count() : 0;
            $hadir = $p->absensis->where('status', 'Hadir')->count();
            $izin = $p->absensis->where('status', 'Izin')->count();
            $sakit = $p->absensis->where('status', 'Sakit')->count();
            $alpa = $p->absensis->where('status', 'Alpa')->count();
            $persen = $totalPiket > 0 ? round(($hadir / $totalPiket) * 100, 1) : 0;

            return [
                'id' => $p->id,
                'nama_petugas' => $p->nama_petugas,
                'nama_kelompok' => $p->kelompok->nama_kelompok ?? '-',
                'total_jadwal' => $totalPiket,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'persentase' => $persen,
            ];
        })->sortByDesc('hadir')->values();

        // Filter rekap by search if search is given
        if ($search !== '') {
            $rekapPerPegawai = $rekapPerPegawai->filter(function ($item) use ($search) {
                return stripos($item['nama_petugas'], $search) !== false
                    || stripos($item['nama_kelompok'], $search) !== false;
            })->values();
        }

        // 5. Data for manual input modal
        $kelompoks = SpmbPiketKelompok::where('tahun_ajaran', $selectedYear)
            ->with(['petugas', 'tanggals' => function ($q) {
                $q->orderBy('tanggal', 'asc');
            }])
            ->get();

        return view('pages.kaur-spmb.absensi.index', compact(
            'availableYears',
            'selectedYear',
            'activeTab',
            'selectedTanggalId',
            'selectedStatus',
            'search',
            'perPage',
            'allTanggals',
            'absensis',
            'stats',
            'rekapPerPegawai',
            'kelompoks'
        ));
    }

    /**
     * Store manual attendance input by Kaur SPMB.
     */
    public function storeManual(Request $request)
    {
        $validated = $request->validate([
            'spmb_piket_tanggal_id' => 'required|exists:spmb_piket_tanggals,id',
            'spmb_piket_petugas_id' => 'required|exists:spmb_piket_petugas,id',
            'status' => 'required|in:Hadir,Izin,Sakit,Alpa',
            'jam_absen' => 'nullable|string',
            'foto_eviden' => 'nullable|image|max:5120',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $petugas = SpmbPiketPetugas::findOrFail($validated['spmb_piket_petugas_id']);
        $tanggal = SpmbPiketTanggal::findOrFail($validated['spmb_piket_tanggal_id']);

        $fotoPath = null;
        if ($request->hasFile('foto_eviden')) {
            $fotoPath = $request->file('foto_eviden')->store('spmb-piket-eviden', 'public');
        }

        $jamAbsen = ! empty($validated['jam_absen'])
            ? $validated['jam_absen']
            : ($validated['status'] === 'Hadir' ? now()->format('H:i:s') : null);

        $pengganti = \App\Models\SpmbPiketPengganti::where('spmb_piket_tanggal_id', $tanggal->id)
            ->where('spmb_piket_petugas_id', $petugas->id)
            ->first();

        $userId = $pengganti?->user_id ?: $petugas->user_id;
        $namaDisplay = $pengganti ? "{$pengganti->nama_pengganti} (Pengganti {$petugas->nama_petugas})" : $petugas->nama_petugas;

        SpmbPiketAbsensi::updateOrCreate(
            [
                'spmb_piket_petugas_id' => $petugas->id,
                'spmb_piket_tanggal_id' => $tanggal->id,
            ],
            [
                'user_id' => $userId,
                'status' => $validated['status'],
                'jam_absen' => $jamAbsen,
                'foto_eviden' => $fotoPath ?: DB::raw('foto_eviden'),
                'keterangan' => $validated['keterangan'] ?? 'Diinput manual oleh Kaur SPMB',
                'metode_input' => 'manual_kaur',
                'created_by_user_id' => auth()->id(),
            ]
        );

        return redirect()->back()->with('success', "Absensi untuk {$namaDisplay} berhasil disimpan secara manual.");
    }

    /**
     * Delete an attendance record.
     */
    public function destroy(SpmbPiketAbsensi $absensi)
    {
        if ($absensi->foto_eviden && Storage::disk('public')->exists($absensi->foto_eviden)) {
            Storage::disk('public')->delete($absensi->foto_eviden);
        }

        $nama = $absensi->petugas->nama_petugas ?? 'Petugas';
        $absensi->delete();

        return redirect()->back()->with('success', "Data absensi {$nama} berhasil dihapus.");
    }

    /**
     * Export Attendances and Rekapitulasi to Styled Excel.
     */
    public function exportExcel(Request $request)
    {
        $tahunAjaran = $request->get('tahun_ajaran', '2027/2028');
        $selectedTanggalId = $request->get('tanggal_id');
        $selectedStatus = $request->get('status');

        $query = SpmbPiketAbsensi::whereHas('tanggal.kelompok', function ($q) use ($tahunAjaran) {
            $q->where('tahun_ajaran', $tahunAjaran);
        })
        ->with(['petugas.kelompok', 'tanggal.kelompok'])
        ->orderBy('created_at', 'desc');

        if (! empty($selectedTanggalId)) {
            $query->where('spmb_piket_tanggal_id', $selectedTanggalId);
        }

        if (! empty($selectedStatus)) {
            $query->where('status', $selectedStatus);
        }

        $records = $query->get();

        // Calculate statistics
        $totalHadir = $records->where('status', 'Hadir')->count();
        $totalIzin = $records->where('status', 'Izin')->count();
        $totalSakit = $records->where('status', 'Sakit')->count();
        $totalAlpa = $records->where('status', 'Alpa')->count();
        $total = $records->count();
        $persen = $total > 0 ? round(($totalHadir / $total) * 100, 1) : 0;

        $stats = [
            'total_jadwal' => $total,
            'hadir' => $totalHadir,
            'izin' => $totalIzin,
            'sakit' => $totalSakit,
            'alpa' => $totalAlpa,
            'persentase' => $persen,
        ];

        // Rekap per pegawai
        $allPetugas = SpmbPiketPetugas::whereHas('kelompok', function ($q) use ($tahunAjaran) {
            $q->where('tahun_ajaran', $tahunAjaran);
        })
        ->with(['kelompok.tanggals', 'absensis'])
        ->get();

        $rekapData = $allPetugas->map(function ($p) {
            $totalPiket = $p->kelompok ? $p->kelompok->tanggals->count() : 0;
            $hadir = $p->absensis->where('status', 'Hadir')->count();
            $izin = $p->absensis->where('status', 'Izin')->count();
            $sakit = $p->absensis->where('status', 'Sakit')->count();
            $alpa = $p->absensis->where('status', 'Alpa')->count();
            $persen = $totalPiket > 0 ? round(($hadir / $totalPiket) * 100, 1) : 0;

            return [
                'nama_petugas' => $p->nama_petugas,
                'nama_kelompok' => $p->kelompok->nama_kelompok ?? '-',
                'total_jadwal' => $totalPiket,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'persentase' => $persen,
            ];
        })->sortByDesc('hadir')->values();

        $safeTahun = str_replace('/', '-', $tahunAjaran);
        $fileName = "Rekap_Presensi_Piket_SPMB_{$safeTahun}.xlsx";

        return Excel::download(new SpmbPiketAbsensiExport($records, $rekapData, $tahunAjaran, $stats), $fileName);
    }
}
