<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\GuruIzin;
use App\Models\JadwalPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IzinGuruController extends Controller
{
    public function index()
    {
        $guru = Auth::user()->masterGuru;
        if (!$guru) {
            return redirect()->route('dashboard')->with('error', 'Data Master Guru tidak ditemukan. Silakan hubungi admin.');
        }
        $izins = GuruIzin::where('master_guru_id', $guru->id)->latest()->paginate(10);
        return view('pages.guru.izin.index', compact('izins'));
    }

    public function create()
    {
        $guru = Auth::user()->masterGuru;
        if (!$guru) {
            return redirect()->route('dashboard')->with('error', 'Data Master Guru tidak ditemukan. Silakan hubungi admin.');
        }
        // Optional: Get schedule for today or next few days
        return view('pages.guru.izin.create');
    }

    public function getSchedules(Request $request)
    {
        $guru = Auth::user()->masterGuru;
        if (!$guru) {
            return response()->json([], 404);
        }
        $tanggal = $request->tanggal;
        $hariMap = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];
        $hari = $hariMap[date('l', strtotime($tanggal))];

        $schedules = JadwalPelajaran::with(['rombel', 'mataPelajaran'])
            ->where('master_guru_id', $guru->id)
            ->where('hari', $hari)
            ->orderBy('jam_ke')
            ->get();

        return response()->json($schedules);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jenis_izin' => 'required|string',
            'kategori_penyetujuan' => 'required|in:sekolah,luar,terlambat',
            'deskripsi' => 'required|string',
            'jadwal_ids' => 'nullable|array',
            'jadwal_ids.*' => 'exists:jadwal_pelajarans,id',
        ]);

        $guru = Auth::user()->masterGuru;
        if (!$guru) {
            return redirect()->back()->with('error', 'Data Master Guru tidak ditemukan.');
        }

        // Logic check: If there are schedules within the permit timeframe, at least one must be selected
        $startDate = \Carbon\Carbon::parse($request->tanggal_mulai);
        $endDate = \Carbon\Carbon::parse($request->tanggal_selesai);
        
        // Get all schedules for the days covered by the permit
        // (For simplicity assuming single day or handle multiple days if needed)
        // Usually teacher permits are per-day in this context
        $hariMap = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
        ];
        
        $hari = $hariMap[$startDate->format('l')];
        $startTime = $startDate->format('H:i:s');
        $endTime = $endDate->format('H:i:s');

        $availableSchedules = JadwalPelajaran::where('master_guru_id', $guru->id)
            ->where('hari', $hari)
            ->where(function($q) use ($startTime, $endTime) {
                $q->where('jam_mulai', '<', $endTime)
                  ->where('jam_selesai', '>', $startTime);
            })
            ->get();

        if ($availableSchedules->isNotEmpty() && count($request->input('jadwal_ids', [])) === 0) {
            return redirect()->back()->withInput()->with('error', 'Sistem mendeteksi Anda memiliki jam mengajar pada waktu tersebut. Silakan pilih jam pelajaran yang Anda tinggalkan.');
        }

        // Check for overlapping permits (existing permits for the same time)
        $overlap = GuruIzin::where('master_guru_id', $guru->id)
            ->where(function ($query) use ($request) {
                $query->where('tanggal_mulai', '<=', $request->tanggal_selesai)
                      ->where('tanggal_selesai', '>=', $request->tanggal_mulai);
            })
            ->where('status_piket', '!=', 'ditolak')
            ->where('status_kurikulum', '!=', 'ditolak')
            ->where('status_sdm', '!=', 'ditolak')
            ->exists();

        if ($overlap) {
            return redirect()->back()->withInput()->with('error', 'Anda sudah memiliki pengajuan izin pada rentang waktu tersebut yang sedang diproses atau sudah disetujui.');
        }

        $statusPiket = 'menunggu';
        $statusKurikulum = 'menunggu';
        
        // Bisnis logic khusus: Izin Terlambat langsung ke KAUR SDM
        if ($request->kategori_penyetujuan === 'terlambat') {
            $statusPiket = 'disetujui';
            $statusKurikulum = 'disetujui';
        }

        $izin = GuruIzin::create([
            'master_guru_id' => $guru->id,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'jenis_izin' => $request->jenis_izin,
            'kategori_penyetujuan' => $request->kategori_penyetujuan,
            'deskripsi' => $request->deskripsi,
            'status_piket' => $statusPiket,
            'status_kurikulum' => $statusKurikulum,
            'status_sdm' => 'menunggu',
        ]);

        if ($request->filled('jadwal_ids')) {
            $izin->jadwals()->attach($request->jadwal_ids);
        }

        return redirect()->route('guru.izin.index')->with('success', 'Permohonan izin berhasil diajukan dan sedang menunggu persetujuan.');
    }
}
