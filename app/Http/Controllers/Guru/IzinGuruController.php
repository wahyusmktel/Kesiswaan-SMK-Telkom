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
            'deskripsi' => 'required|string',
            'jadwal_ids' => 'required|array',
            'jadwal_ids.*' => 'exists:jadwal_pelajarans,id',
        ]);

        $guru = Auth::user()->masterGuru;
        if (!$guru) {
            return redirect()->back()->with('error', 'Data Master Guru tidak ditemukan.');
        }

        $izin = GuruIzin::create([
            'master_guru_id' => $guru->id,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'jenis_izin' => $request->jenis_izin,
            'deskripsi' => $request->deskripsi,
            'status_piket' => 'menunggu',
            'status_kurikulum' => 'menunggu',
            'status_sdm' => 'menunggu',
        ]);

        $izin->jadwals()->attach($request->jadwal_ids);

        return redirect()->route('guru.izin.index')->with('success', 'Permohonan izin berhasil diajukan dan sedang menunggu persetujuan.');
    }
}
