<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\BKKonsultasiJadwal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BKController extends Controller
{
    public function index()
    {
        $siswa = Auth::user()->masterSiswa;
        if (!$siswa) return redirect()->back()->with('error', 'Profil siswa tidak ditemukan.');

        $jadwals = $siswa->konsultasiJadwals()->with('guruBK')->latest()->get();
        $gurusBK = User::role('Guru BK')->get();

        return view('pages.siswa.bk.index', compact('jadwals', 'gurusBK'));
    }

    public function storeJadwal(Request $request)
    {
        $request->validate([
            'perihal' => 'required|string',
            'tanggal_rencana' => 'required|date|after_or_equal:today',
            'jam_rencana' => 'required',
            'guru_bk_id' => 'nullable|exists:users,id',
        ]);

        $siswa = Auth::user()->masterSiswa;

        BKKonsultasiJadwal::create([
            'master_siswa_id' => $siswa->id,
            'guru_bk_id' => $request->guru_bk_id,
            'perihal' => $request->perihal,
            'tanggal_rencana' => $request->tanggal_rencana,
            'jam_rencana' => $request->jam_rencana,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Pengajuan jadwal konsultasi berhasil dikirim.');
    }
}
