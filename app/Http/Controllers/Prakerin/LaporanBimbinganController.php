<?php

namespace App\Http\Controllers\Prakerin;

use App\Http\Controllers\Controller;
use App\Models\PrakerinLaporanBimbingan;
use App\Models\PrakerinPenempatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanBimbinganController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->masterSiswa) {
            $penempatan = PrakerinPenempatan::with(['siswa', 'industri', 'rombelPkl', 'guruPembimbing'])
                ->where('master_siswa_id', $user->masterSiswa->id)
                ->where('status', 'aktif')
                ->first();
            $laporans = $penempatan
                ? PrakerinLaporanBimbingan::with(['penempatan.siswa', 'reviewer'])->where('prakerin_penempatan_id', $penempatan->id)->latest()->paginate(12)
                : collect();
        } else {
            $penempatan = null;
            $laporans = PrakerinLaporanBimbingan::with(['penempatan.siswa', 'penempatan.industri', 'penempatan.rombelPkl'])
                ->whereHas('penempatan', fn ($q) => $q->where('master_guru_id', $user->masterGuru?->id)->where('status', 'aktif'))
                ->latest()
                ->paginate(15);
        }

        return view('pages.prakerin.laporan-bimbingan.index', compact('penempatan', 'laporans'));
    }

    public function store(Request $request)
    {
        $penempatan = PrakerinPenempatan::where('master_siswa_id', Auth::user()->masterSiswa?->id)
            ->where('status', 'aktif')
            ->firstOrFail();

        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'file_laporan' => 'nullable|file|mimes:pdf,doc,docx|max:20480',
        ]);

        $path = $request->hasFile('file_laporan')
            ? $request->file('file_laporan')->store('public/laporan_prakerin')
            : null;

        PrakerinLaporanBimbingan::create([
            'prakerin_penempatan_id' => $penempatan->id,
            'uploaded_by' => Auth::id(),
            'judul' => $data['judul'],
            'file_path' => $path,
            'status' => 'diajukan',
        ]);

        toast('Laporan berhasil diajukan untuk bimbingan.', 'success');

        return back();
    }

    public function update(Request $request, PrakerinLaporanBimbingan $laporan)
    {
        abort_unless($laporan->penempatan?->master_guru_id === Auth::user()->masterGuru?->id, 403);

        $data = $request->validate([
            'status' => 'required|in:ditinjau,revisi',
            'catatan_pembimbing' => 'nullable|string|max:5000',
        ]);

        $laporan->update($data + [
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        toast('Status bimbingan laporan berhasil diperbarui.', 'success');

        return back();
    }
}
