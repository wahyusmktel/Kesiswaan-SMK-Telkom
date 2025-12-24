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
    public function index()
    {
        $today = now()->format('Y-m-d');
        $namaHari = $this->getNamaHari(now()->dayOfWeek);
        
        // Get active academic year
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        
        if (!$tahunAktif) {
            toast('Tahun ajaran aktif belum diatur.', 'error');
            return redirect()->back();
        }

        // Get today's schedule with attendance status
        $jadwalHariIni = JadwalPelajaran::with(['rombel.kelas', 'mataPelajaran', 'guru.user'])
            ->whereHas('rombel', function($q) use ($tahunAktif) {
                $q->where('tahun_pelajaran_id', $tahunAktif->id);
            })
            ->where('hari', $namaHari)
            ->orderBy('jam_mulai')
            ->get()
            ->map(function($jadwal) use ($today) {
                // Check if attendance already recorded for today
                $absensi = AbsensiGuru::where('jadwal_pelajaran_id', $jadwal->id)
                    ->where('tanggal', $today)
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

        return view('pages.piket.absensi-guru.index', compact(
            'jadwalGrouped',
            'totalJadwal',
            'totalHadir',
            'totalTidakHadir',
            'totalTerlambat',
            'totalBelumDicatat',
            'namaHari'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jadwal_pelajaran_id' => 'required|exists:jadwal_pelajarans,id',
            'status' => 'required|in:hadir,tidak_hadir,terlambat,izin',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $today = now()->format('Y-m-d');

        // Check if already recorded
        $existing = AbsensiGuru::where('jadwal_pelajaran_id', $request->jadwal_pelajaran_id)
            ->where('tanggal', $today)
            ->first();

        if ($existing) {
            toast('Absensi untuk jadwal ini sudah dicatat hari ini.', 'warning');
            return redirect()->back();
        }

        AbsensiGuru::create([
            'jadwal_pelajaran_id' => $request->jadwal_pelajaran_id,
            'tanggal' => $today,
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
