<?php

namespace App\Http\Controllers\Prakerin;

use App\Http\Controllers\Controller;
use App\Models\MasterGuru;
use App\Models\PrakerinJurnal;
use App\Models\PrakerinPenempatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringPembimbingController extends Controller
{
    // Menampilkan daftar siswa bimbingan
    public function index()
    {
        $guru = Auth::user()->masterGuru;
        $siswaBimbingan = PrakerinPenempatan::with(['siswa', 'industri', 'rombelPkl'])
            ->withCount([
                'jurnals',
                'jurnals as jurnal_menunggu_count' => fn ($q) => $q->where('status_verifikasi', 'menunggu'),
                'jurnals as jurnal_ditinjau_count' => fn ($q) => $q->where('status_verifikasi', 'disetujui'),
            ])
            ->where('master_guru_id', $guru?->id)
            ->where('status', 'aktif')
            ->get();

        return view('pages.prakerin.monitoring-pembimbing.index', compact('siswaBimbingan'));
    }

    // Menampilkan detail jurnal dari satu siswa
    public function show(PrakerinPenempatan $penempatan)
    {
        // Pastikan guru hanya bisa mengakses siswa bimbingannya
        if ($penempatan->master_guru_id !== Auth::user()->masterGuru?->id) {
            abort(403);
        }

        $jurnals = PrakerinJurnal::where('prakerin_penempatan_id', $penempatan->id)
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        return view('pages.prakerin.monitoring-pembimbing.show', compact('penempatan', 'jurnals'));
    }

    // Memvalidasi jurnal
    public function updateJurnal(Request $request, PrakerinJurnal $jurnal)
    {
        $request->validate([
            'status_verifikasi' => 'required|in:disetujui,revisi',
            'catatan_pembimbing' => 'nullable|string',
        ]);

        // Pastikan guru hanya bisa memvalidasi jurnal siswa bimbingannya
        if ($jurnal->penempatan->master_guru_id !== Auth::user()->masterGuru?->id) {
            abort(403);
        }

        $jurnal->update([
            'status_verifikasi' => $request->status_verifikasi,
            'catatan_pembimbing' => $request->catatan_pembimbing,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        toast('Jurnal berhasil ditinjau.', 'success');
        return back();
    }
}
