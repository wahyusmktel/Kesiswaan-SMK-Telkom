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

        // Notify SDM
        $approvers = \App\Models\User::role('kaur sdm')->get();
        $msg = "Ada pengajuan Izin Guru (Luar Sekolah) yang perlu validasi akhir.";
        $url = route('sdm.persetujuan-izin-guru.index');
        foreach ($approvers as $approver) {
            $approver->notify(new \App\Notifications\PengajuanIzinGuruNotification($izin, 'pending_approval', $msg, $url));
        }

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

        // Notify Teacher
        $teacherUser = $izin->guru->user;
        if ($teacherUser) {
            $msg = "Permohonan izin Anda ditolak oleh Waka Kurikulum.";
            $url = route('guru.izin.index');
            $teacherUser->notify(new \App\Notifications\PengajuanIzinGuruNotification($izin, 'status_updated', $msg, $url));
        }

        return redirect()->back()->with('info', 'Permohonan izin telah ditolak oleh Waka Kurikulum.');
    }
}
