<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\MasterSiswa;
use App\Models\Perizinan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Notifications\IzinDisetujuiNotification;
use App\Notifications\IzinDitolakNotification;

class PerizinanController extends Controller
{
    /**
     * Menampilkan daftar perizinan dari siswa yang menjadi perwalian.
     */
    public function index(Request $request)
    {
        try {
            $waliKelas = Auth::user();

            // LOGIKA BARU: Ambil ID semua siswa yang terhubung dengan Wali Kelas melalui Rombel
            // 1. Dapatkan semua ID MasterSiswa dari semua rombel yang diampu oleh wali kelas ini.
            $masterSiswaIds = MasterSiswa::whereHas('rombels', function ($query) use ($waliKelas) {
                $query->where('wali_kelas_id', $waliKelas->id);
            })->pluck('id');

            // 2. Dari ID MasterSiswa tersebut, dapatkan ID user (akun login) mereka.
            $userIds = MasterSiswa::whereIn('id', $masterSiswaIds)->whereNotNull('user_id')->pluck('user_id');

            // 3. Gunakan ID user tersebut untuk mengambil data perizinan.
            $query = Perizinan::with('user') // Eager load relasi user (siswa)
                ->whereIn('user_id', $userIds);

            // Filter berdasarkan status (tetap sama)
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter berdasarkan pencarian nama siswa (tetap sama)
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->whereHas('user', function ($q) use ($searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%');
                });
            }

            $perizinan = $query->latest()->paginate(10)->withQueryString();

            return view('pages.wali-kelas.perizinan.index', compact('perizinan'));

        } catch (\Exception $e) {
            Log::error('Error fetching student permissions for Wali Kelas: ' . $e->getMessage());
            toast('Gagal memuat data perizinan siswa.', 'error');
            return redirect()->back();
        }
    }

    /**
     * Menyetujui pengajuan izin.
     */
    public function approve(Perizinan $perizinan)
    {
        // LOGIKA OTORISASI BARU
        $isMyStudent = $perizinan->user->masterSiswa
                                ->rombels()
                                ->where('wali_kelas_id', Auth::id())
                                ->exists();

        if (!$isMyStudent) {
            toast('Anda tidak berhak menyetujui izin ini.', 'error');
            return redirect()->back();
        }

        try {
            $wali = Auth::user();
            $perizinan->update([
                'status'        => 'disetujui',
                'disetujui_oleh'=> $wali->id,
            ]);

            // Kirim notifikasi ke siswa
            $perizinan->user->notify(new IzinDisetujuiNotification($perizinan));

            // Auto-sign TTD Wali Kelas jika diaktifkan
            $sig = \App\Models\UserDigitalSignature::where('user_id', $wali->id)->first();
            if ($sig && $sig->isReady() && $sig->auto_sign_perizinan) {
                \App\Models\DigitalDocument::autoSign(
                    $wali,
                    'PERIZINAN',
                    'Perizinan - ' . $perizinan->user->name,
                    $perizinan->id,
                    ['PERIZINAN', (string) $perizinan->id, (string) $perizinan->user_id, $perizinan->user->name]
                );
            }

            toast('Pengajuan izin telah disetujui.', 'success');
            return redirect()->back();

        } catch (\Exception $e) {
            Log::error('Error approving permission: ' . $e->getMessage());
            toast('Terjadi kesalahan saat menyetujui izin.', 'error');
            return redirect()->back();
        }
    }

    /**
     * Menolak pengajuan izin.
     */
    public function reject(Request $request, Perizinan $perizinan)
    {
        // LOGIKA OTORISASI BARU
        $isMyStudent = $perizinan->user->masterSiswa
                                ->rombels()
                                ->where('wali_kelas_id', Auth::id())
                                ->exists();

        if (!$isMyStudent) {
            toast('Anda tidak berhak menolak izin ini.', 'error');
            return redirect()->back();
        }

        $request->validate(['alasan_penolakan' => 'required|string|min:10']);

        try {
            $perizinan->update([
                'status' => 'ditolak',
                'alasan_penolakan' => $request->alasan_penolakan,
                'disetujui_oleh' => Auth::id(),
            ]);

            // Kirim notifikasi ke siswa
            $perizinan->user->notify(new IzinDitolakNotification($perizinan));

            toast('Pengajuan izin telah ditolak.', 'info');
            return redirect()->back();

        } catch (\Exception $e) {
            Log::error('Error rejecting permission: ' . $e->getMessage());
            toast('Terjadi kesalahan saat menolak izin.', 'error');
            return redirect()->back();
        }
    }
}
