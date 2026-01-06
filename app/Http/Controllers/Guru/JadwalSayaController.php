<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\MasterGuru;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalSayaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $masterGuru = MasterGuru::where('user_id', $user->id)->first();
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        if (!$masterGuru || !$tahunAktif) {
            return redirect()->back()->with('error', 'Data guru atau tahun pelajaran aktif tidak ditemukan.');
        }

        $jadwalRaw = JadwalPelajaran::with(['rombel.kelas', 'mataPelajaran'])
            ->where('master_guru_id', $masterGuru->id)
            ->whereHas('rombel', function($query) use ($tahunAktif) {
                $query->where('tahun_pelajaran_id', $tahunAktif->id);
            })
            ->get();

        // Grouping by day
        $hariUrutan = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        
        $jadwalGrouped = $jadwalRaw->groupBy('hari')->sortBy(function ($value, $key) use ($hariUrutan) {
            return array_search($key, $hariUrutan);
        });

        return view('pages.guru.jadwal.index', compact('jadwalGrouped', 'tahunAktif', 'masterGuru'));
    }
}
