<?php

namespace App\Http\Controllers\GuruKelas;

use App\Http\Controllers\Controller;
use App\Models\IzinMeninggalkanKelas;
use App\Models\JadwalPelajaran;
use App\Models\MasterGuru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PersetujuanIzinKeluarController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $masterGuru = MasterGuru::where('user_id', $user->id)->first();
        $pengajuanIzin = collect();
        $jadwalSaatIni = null;

        if ($masterGuru) {
            $namaHariIni = $this->getNamaHari(now()->dayOfWeek);
            $waktuSaatIni = now()->format('H:i:s');

            // Cari jadwal guru yang sedang berlangsung saat ini
            $jadwalSaatIni = JadwalPelajaran::with('rombel.kelas')
                ->where('master_guru_id', $masterGuru->id)
                ->where('hari', $namaHariIni)
                ->where('jam_mulai', '<=', $waktuSaatIni)
                ->where('jam_selesai', '>=', $waktuSaatIni)
                ->first();

            // Jika guru sedang mengajar di sebuah kelas
            if ($jadwalSaatIni) {
                $pengajuanIzin = IzinMeninggalkanKelas::with('siswa')
                    ->where('rombel_id', $jadwalSaatIni->rombel_id)
                    ->where('status', 'diajukan')
                    ->get();
            }
        }

        return view('pages.guru-kelas.persetujuan-izin-keluar.index', compact('pengajuanIzin', 'jadwalSaatIni'));
    }

    public function approve(IzinMeninggalkanKelas $izin)
    {
        try {
            $user = Auth::user();
            $izin->update([
                'status'                 => 'disetujui_guru_kelas',
                'guru_kelas_approval_id' => $user->id,
                'guru_kelas_approved_at' => now(),
            ]);
            $izin->load('siswa');

            $sig = \App\Models\UserDigitalSignature::where('user_id', $user->id)->first();
            if ($sig && $sig->isReady() && $sig->auto_sign_izin_keluar) {
                \App\Models\DigitalDocument::autoSign(
                    $user,
                    'IZIN_KELUAR_GK',
                    'Izin Keluar (Guru Kelas) - ' . $izin->siswa->name,
                    $izin->id,
                    ['IZIN_KELUAR_GK', (string) $izin->id, (string) $izin->user_id, $izin->siswa->name]
                );
            }

            toast('Izin berhasil disetujui.', 'success');
        } catch (\Exception $e) {
            Log::error('Error approving leave permit by class teacher: ' . $e->getMessage());
            toast('Gagal menyetujui izin.', 'error');
        }
        return back();
    }

    public function reject(Request $request, IzinMeninggalkanKelas $izin)
    {
        $request->validate(['alasan_penolakan' => 'required|string|min:5']);
        try {
            $izin->update([
                'status' => 'ditolak',
                'ditolak_oleh' => Auth::id(),
                'alasan_penolakan' => $request->alasan_penolakan,
            ]);
            toast('Izin telah ditolak.', 'info');
        } catch (\Exception $e) {
            Log::error('Error rejecting leave permit by class teacher: ' . $e->getMessage());
            toast('Gagal menolak izin.', 'error');
        }
        return back();
    }

    /**
     * Menampilkan halaman riwayat persetujuan izin keluar oleh guru kelas.
     */
    public function riwayat(Request $request)
    {
        $guruKelasId = Auth::id();

        $query = IzinMeninggalkanKelas::with(['siswa.masterSiswa.rombels.kelas', 'penolak'])
            // Tampilkan yang disetujui atau ditolak oleh guru ini
            ->where(function ($q) use ($guruKelasId) {
                $q->where('guru_kelas_approval_id', $guruKelasId)
                    ->orWhere('ditolak_oleh', $guruKelasId);
            });

        // Filter berdasarkan pencarian nama siswa
        if ($request->filled('search')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // Filter berdasarkan rentang tanggal persetujuan/penolakan
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('guru_kelas_approved_at', '>=', $request->start_date)
                    ->whereDate('guru_kelas_approved_at', '<=', $request->end_date);
            });
        }

        $riwayatIzin = $query->latest('updated_at')->paginate(20);

        return view('pages.guru-kelas.persetujuan-izin-keluar.riwayat', compact('riwayatIzin'));
    }

    private function getNamaHari($dayOfWeek)
    {
        $hari = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
        return $hari[$dayOfWeek] ?? 'Tidak Diketahui';
    }
}
