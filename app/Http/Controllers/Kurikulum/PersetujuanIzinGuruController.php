<?php

namespace App\Http\Controllers\Kurikulum;

use App\Http\Controllers\Controller;
use App\Models\GuruIzin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersetujuanIzinGuruController extends Controller
{
    public function index(Request $request)
    {
        $query = GuruIzin::with(['guru'])->where('status_piket', 'disetujui')->latest();
        
        if ($request->filled('status')) {
            $query->where('status_kurikulum', $request->status);
        } else {
            $query->where('status_kurikulum', 'menunggu');
        }

        $izins = $query->paginate(10);
        return view('pages.kurikulum.izin-guru.index', compact('izins'));
    }

    public function approve(GuruIzin $izin)
    {
        $izin->update([
            'status_kurikulum' => 'disetujui',
            'kurikulum_id' => Auth::id(),
            'kurikulum_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Permohonan izin diteruskan ke KAUR SDM.');
    }

    public function reject(Request $request, GuruIzin $izin)
    {
        $request->validate(['catatan_kurikulum' => 'required|string']);
        
        $izin->update([
            'status_kurikulum' => 'ditolak',
            'kurikulum_id' => Auth::id(),
            'kurikulum_at' => now(),
            'catatan_kurikulum' => $request->catatan_kurikulum,
        ]);

        return redirect()->back()->with('info', 'Permohonan izin telah ditolak oleh Waka Kurikulum.');
    }
}
