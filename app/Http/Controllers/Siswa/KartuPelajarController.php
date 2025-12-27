<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KartuPelajarController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $siswa = $user->masterSiswa;

        // Ensure the user is a student and has master record
        if (!$siswa) {
            return redirect()->route('dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }

        // Get rombel (class) info - Join with tahun_pelajaran to find the active one
        $rombel = $siswa->rombels()->with('kelas')->whereHas('tahunPelajaran', function($query) {
            $query->where('is_active', true);
        })->first() ?? $siswa->rombels()->with('kelas')->first();

        // Get school settings
        $settings = \App\Models\AppSetting::first();

        return view('pages.siswa.kartu-pelajar.index', [
            'user' => $user,
            'siswa' => $siswa,
            'rombel' => $rombel,
            'settings' => $settings
        ]);
    }
}
