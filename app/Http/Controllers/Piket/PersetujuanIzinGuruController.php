<?php

namespace App\Http\Controllers\Piket;

use App\Http\Controllers\Controller;
use App\Models\GuruIzin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersetujuanIzinGuruController extends Controller
{
    public function index(Request $request)
    {
        $query = GuruIzin::with(['guru'])->latest();
        
        if ($request->filled('status')) {
            $query->where('status_piket', $request->status);
        } else {
            $query->where('status_piket', 'menunggu');
        }

        $izins = $query->paginate(10);
        return view('pages.piket.izin-guru.index', compact('izins'));
    }

    public function approve(GuruIzin $izin)
    {
        $izin->update([
            'status_piket' => 'disetujui',
            'piket_id' => Auth::id(),
            'piket_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Permohonan izin diteruskan ke Waka Kurikulum.');
    }

    public function reject(Request $request, GuruIzin $izin)
    {
        $request->validate(['catatan_piket' => 'required|string']);
        
        $izin->update([
            'status_piket' => 'ditolak',
            'piket_id' => Auth::id(),
            'piket_at' => now(),
            'catatan_piket' => $request->catatan_piket,
        ]);

        return redirect()->back()->with('info', 'Permohonan izin telah ditolak.');
    }
}
