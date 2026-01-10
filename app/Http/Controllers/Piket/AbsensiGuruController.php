<?php

namespace App\Http\Controllers\Piket;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\JadwalPelajaran;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AbsensiGuruController extends Controller
{
    public function index(Request $request)
    {
        $selectedDate = $request->get('date', now()->format('Y-m-d'));
        $date = \Carbon\Carbon::parse($selectedDate);
        $namaHari = $this->getNamaHari($date->dayOfWeek);
        $isPast = $date->isPast() && !$date->isToday();
        
        // Get active academic year
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        
        if (!$tahunAktif) {
            toast('Tahun ajaran aktif belum diatur.', 'error');
            return redirect()->back();
        }

        // Get schedule for selected date with attendance status
        $jadwalHariIni = JadwalPelajaran::with(['rombel.kelas', 'mataPelajaran', 'guru.user'])
            ->whereHas('rombel', function($q) use ($tahunAktif) {
                $q->where('tahun_pelajaran_id', $tahunAktif->id);
            })
            ->where('hari', $namaHari)
            ->orderBy('jam_mulai')
            ->get()
            ->map(function($jadwal) use ($selectedDate) {
                // Check if attendance already recorded for selected date
                $absensi = AbsensiGuru::where('jadwal_pelajaran_id', $jadwal->id)
                    ->where('tanggal', $selectedDate)
                    ->first();
                
                $jadwal->absensi = $absensi;
                $jadwal->status_absensi = $absensi ? $absensi->status : 'belum_dicatat';
                
                return $jadwal;
            });

        // Group by time slots for better UI
        $jadwalGrouped = $jadwalHariIni->groupBy(function($item) {
            return substr($item->jam_mulai, 0, 5); // Group by HH:MM
        });

        // Statistics
        $totalJadwal = $jadwalHariIni->count();
        $totalHadir = $jadwalHariIni->where('status_absensi', 'hadir')->count();
        $totalTidakHadir = $jadwalHariIni->where('status_absensi', 'tidak_hadir')->count();
        $totalTerlambat = $jadwalHariIni->where('status_absensi', 'terlambat')->count();
        $totalBelumDicatat = $jadwalHariIni->where('status_absensi', 'belum_dicatat')->count();

        // Extract unique jam_ke with time for filter
        $listJamKe = $jadwalHariIni->sortBy('jam_ke')
            ->groupBy('jam_ke')
            ->map(function($group) {
                $first = $group->first();
                return (object)[
                    'jam_ke' => $first->jam_ke,
                    'waktu' => substr($first->jam_mulai, 0, 5) . ' - ' . substr($first->jam_selesai, 0, 5)
                ];
            })->values();

        return view('pages.piket.absensi-guru.index', compact(
            'jadwalGrouped',
            'totalJadwal',
            'totalHadir',
            'totalTidakHadir',
            'totalTerlambat',
            'totalBelumDicatat',
            'namaHari',
            'listJamKe',
            'selectedDate',
            'isPast'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jadwal_pelajaran_id' => 'required|exists:jadwal_pelajarans,id',
            'status' => 'required|in:hadir,tidak_hadir,terlambat,izin',
            'keterangan' => 'nullable|string|max:500',
            'tanggal' => 'required|date',
            'agreement' => 'nullable|boolean',
        ]);

        $tanggal = $request->tanggal;
        $date = \Carbon\Carbon::parse($tanggal);
        
        // Check if past date and agreement is missing
        if ($date->isPast() && !$date->isToday() && !$request->has('agreement')) {
            toast('Anda harus menyetujui pernyataan untuk mencatat absensi di hari lampau.', 'error');
            return redirect()->back();
        }

        // Check if future date (optional restriction)
        if ($date->isFuture()) {
            toast('Tidak dapat mencatat absensi untuk hari esok.', 'error');
            return redirect()->back();
        }

        // Check if already recorded for this specific date
        $existing = AbsensiGuru::where('jadwal_pelajaran_id', $request->jadwal_pelajaran_id)
            ->where('tanggal', $tanggal)
            ->first();

        if ($existing) {
            toast('Absensi untuk jadwal ini sudah dicatat pada tanggal ' . $tanggal, 'warning');
            return redirect()->back();
        }

        AbsensiGuru::create([
            'jadwal_pelajaran_id' => $request->jadwal_pelajaran_id,
            'tanggal' => $tanggal,
            'status' => $request->status,
            'waktu_absen' => now(),
            'dicatat_oleh' => Auth::id(),
            'keterangan' => $request->keterangan,
        ]);

        toast('Absensi guru berhasil dicatat.', 'success');
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:hadir,tidak_hadir,terlambat,izin',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $absensi = AbsensiGuru::findOrFail($id);
        
        $absensi->update([
            'status' => $request->status,
            'waktu_absen' => now(),
            'dicatat_oleh' => Auth::id(),
            'keterangan' => $request->keterangan,
        ]);

        toast('Absensi guru berhasil diperbarui.', 'success');
        return redirect()->back();
    }

    private function getNamaHari($dayOfWeek)
    {
        $hari = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu'
        ];
        return $hari[$dayOfWeek] ?? 'Tidak Diketahui';
    }
}
