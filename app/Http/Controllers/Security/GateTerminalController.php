<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\IzinMeninggalkanKelas;
use App\Models\Keterlambatan;
use App\Models\MasterSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GateTerminalController extends Controller
{
    /**
     * Display the main terminal menu.
     */
    public function index()
    {
        return view('pages.security.gate-terminal.index');
    }

    /**
     * Display the lateness scanning interface.
     */
    public function lateness()
    {
        return view('pages.security.gate-terminal.lateness');
    }

    /**
     * Display the permit scanning interface.
     */
    public function permit()
    {
        return view('pages.security.gate-terminal.permit');
    }

    /**
     * Process scanned NIPD for lateness.
     */
    public function processLateness(Request $request)
    {
        $request->validate([
            'nipd' => 'required|string',
        ]);

        $siswa = MasterSiswa::with('rombels.kelas')->where('nis', $request->nipd)->first();

        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan (NIPD: ' . $request->nipd . ')',
            ], 404);
        }

        // Check if already recorded today
        $exists = Keterlambatan::where('master_siswa_id', $siswa->id)
            ->whereDate('waktu_dicatat_security', today())
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa ' . $siswa->nama_lengkap . ' sudah didata terlambat hari ini.',
                'siswa' => $siswa
            ], 422);
        }

        try {
            Keterlambatan::create([
                'master_siswa_id' => $siswa->id,
                'alasan_siswa' => 'Terdeteksi via Gate Terminal',
                'dicatat_oleh_security_id' => Auth::id(),
                'waktu_dicatat_security' => now(),
                'status' => 'dicatat_security',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Keterlambatan berhasil dicatat untuk ' . $siswa->nama_lengkap,
                'siswa' => $siswa
            ]);
        } catch (\Exception $e) {
            Log::error('GateTerminal Error (Lateness): ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat menyimpan data.',
            ], 500);
        }
    }

    /**
     * Process scanned NIPD for exit/return permit.
     */
    public function processPermit(Request $request)
    {
        $request->validate([
            'nipd' => 'required|string',
        ]);

        $siswa = MasterSiswa::where('nis', $request->nipd)->first();

        if (!$siswa || !$siswa->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa atau akun tidak ditemukan.',
            ], 404);
        }

        // Find active permit (Either approved by piket OR already verified by security but not finished)
        $izin = IzinMeninggalkanKelas::where('user_id', $siswa->user_id)
            ->whereIn('status', ['disetujui_guru_piket', 'diverifikasi_security'])
            ->latest()
            ->first();

        if (!$izin) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data izin aktif untuk siswa ' . $siswa->nama_lengkap,
            ], 404);
        }

        try {
            $action = '';
            if ($izin->status === 'disetujui_guru_piket') {
                // VERIFY EXIT
                $izin->update([
                    'status' => 'diverifikasi_security',
                    'security_verification_id' => Auth::id(),
                    'security_verified_at' => now(),
                    'waktu_keluar_sebenarnya' => now(),
                ]);
                $action = 'KELUAR';
            } else {
                // VERIFY RETURN
                $waktuKembali = now();
                $estimasiKembali = \Carbon\Carbon::parse($izin->estimasi_kembali);
                $statusAkhir = $waktuKembali->gt($estimasiKembali) ? 'terlambat' : 'selesai';

                $izin->update([
                    'status' => $statusAkhir,
                    'waktu_kembali_sebenarnya' => $waktuKembali,
                ]);
                $action = 'KEMBALI';
            }

            return response()->json([
                'success' => true,
                'message' => 'Berhasil verifikasi ' . $action . ' untuk ' . $siswa->nama_lengkap,
                'action' => $action,
                'siswa' => $siswa,
                'izin' => $izin
            ]);
        } catch (\Exception $e) {
            Log::error('GateTerminal Error (Permit): ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat memproses izin.',
            ], 500);
        }
    }
}
