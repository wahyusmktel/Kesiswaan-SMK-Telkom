<?php

namespace App\Http\Controllers\Piket;

use App\Http\Controllers\Controller;
use App\Models\IzinMeninggalkanKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf; // Import PDF facade
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PersetujuanIzinKeluarController extends Controller
{
    public function index()
    {
        // Ambil semua izin yang sudah disetujui guru kelas atau sudah diproses oleh piket
        $daftarIzin = IzinMeninggalkanKelas::with(['siswa', 'guruKelasApprover'])
            ->whereIn('status', ['disetujui_guru_kelas', 'disetujui_guru_piket', 'diverifikasi_security', 'selesai'])
            ->latest()
            ->paginate(15);

        return view('pages.piket.persetujuan-izin-keluar.index', compact('daftarIzin'));
    }

    public function create()
    {
        return view('pages.piket.persetujuan-izin-keluar.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'master_siswa_id' => 'required|exists:master_siswa,id',
            'jenis_izin' => 'required|in:keluar_sekolah,dalam_lingkungan',
            'tujuan' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'estimasi_kembali' => 'required|date_format:H:i',
        ]);

        try {
            $siswa = \App\Models\MasterSiswa::findOrFail($request->master_siswa_id);
            $user = $siswa->user;

            if (!$user) {
                toast('Data user siswa tidak ditemukan.', 'error');
                return back();
            }

            $tahunAktif = \App\Models\TahunPelajaran::where('is_active', true)->first();
            if (!$tahunAktif) {
                toast('Tahun ajaran aktif belum diatur.', 'error');
                return back();
            }

            $rombelAktif = $siswa->rombels()
                ->where('tahun_pelajaran_id', $tahunAktif->id)
                ->first();

            if (!$rombelAktif) {
                toast('Siswa tidak terdaftar di rombel manapun pada tahun ajaran ini.', 'error');
                return back();
            }

            // Cari jadwal saat ini
            $namaHariIni = $this->getNamaHari(now()->dayOfWeek);
            $waktuSaatIni = now()->format('H:i:s');
            $jadwalSaatIni = \App\Models\JadwalPelajaran::with('guru')->where('rombel_id', $rombelAktif->id)
                ->where('hari', $namaHariIni)
                ->where('jam_mulai', '<=', $waktuSaatIni)
                ->where('jam_selesai', '>=', $waktuSaatIni)
                ->first();

            IzinMeninggalkanKelas::create([
                'user_id' => $user->id,
                'rombel_id' => $rombelAktif->id,
                'jadwal_pelajaran_id' => $jadwalSaatIni?->id,
                'jenis_izin' => $request->jenis_izin,
                'tujuan' => $request->tujuan,
                'keterangan' => $request->keterangan,
                'estimasi_kembali' => now()->setTimeFromTimeString($request->estimasi_kembali),
                'status' => 'disetujui_guru_piket',
                'guru_kelas_approval_id' => $jadwalSaatIni?->guru?->user_id,
                'guru_kelas_approved_at' => $jadwalSaatIni ? now() : null,
                'guru_piket_approval_id' => Auth::id(),
                'guru_piket_approved_at' => now(),
            ]);

            toast('Izin siswa berhasil dicatat dan disetujui.', 'success');
            return redirect()->route('piket.persetujuan-izin-keluar.index');
        } catch (\Exception $e) {
            Log::error('Error duty teacher creating leave permit: ' . $e->getMessage());
            toast('Gagal mencatat izin.', 'error');
            return back()->withInput();
        }
    }

    public function getStudentSchedule(\App\Models\MasterSiswa $siswa)
    {
        try {
            $tahunAktif = \App\Models\TahunPelajaran::where('is_active', true)->first();
            if (!$tahunAktif) {
                return response()->json(['error' => 'Tahun ajaran aktif belum diatur.'], 404);
            }

            $rombelAktif = $siswa->rombels()
                ->where('tahun_pelajaran_id', $tahunAktif->id)
                ->first();

            if (!$rombelAktif) {
                return response()->json(['error' => 'Siswa tidak memiliki rombel aktif.'], 404);
            }

            $namaHariIni = $this->getNamaHari(now()->dayOfWeek);
            $waktuSaatIni = now()->format('H:i:s');
            
            $jadwalSaatIni = \App\Models\JadwalPelajaran::with(['mataPelajaran', 'guru'])
                ->where('rombel_id', $rombelAktif->id)
                ->where('hari', $namaHariIni)
                ->where('jam_mulai', '<=', $waktuSaatIni)
                ->where('jam_selesai', '>=', $waktuSaatIni)
                ->first();

            if (!$jadwalSaatIni) {
                return response()->json(['error' => 'Tidak ada jadwal pelajaran saat ini.'], 404);
            }

            return response()->json([
                'id' => $jadwalSaatIni->id,
                'mapel' => $jadwalSaatIni->mataPelajaran->nama_mapel,
                'guru' => $jadwalSaatIni->guru->nama_lengkap,
                'jam_ke' => $jadwalSaatIni->jam_ke,
                'waktu' => substr($jadwalSaatIni->jam_mulai, 0, 5) . ' - ' . substr($jadwalSaatIni->jam_selesai, 0, 5)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getNamaHari($dayOfWeek)
    {
        $hari = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
        return $hari[$dayOfWeek] ?? 'Tidak Diketahui';
    }

    public function approve(IzinMeninggalkanKelas $izin)
    {
        try {
            $updateData = [
                'status' => 'disetujui_guru_piket',
                'guru_piket_approval_id' => Auth::id(),
                'guru_piket_approved_at' => now(),
            ];

            // Auto-fill Guru Kelas if empty and we have a schedule
            if (!$izin->guru_kelas_approval_id && $izin->jadwal_pelajaran_id) {
                // Ensure relationship is loaded
                $izin->load('jadwalPelajaran.guru');
                if ($izin->jadwalPelajaran?->guru?->user_id) {
                    $updateData['guru_kelas_approval_id'] = $izin->jadwalPelajaran->guru->user_id;
                    $updateData['guru_kelas_approved_at'] = now();
                }
            }

            $izin->update($updateData);
            toast('Izin berhasil disetujui. Silakan cetak surat izin.', 'success');
        } catch (\Exception $e) {
            Log::error('Error approving leave permit by picket teacher: ' . $e->getMessage());
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
            Log::error('Error rejecting leave permit by picket teacher: ' . $e->getMessage());
            toast('Gagal menolak izin.', 'error');
        }
        return back();
    }

    public function printPdf(IzinMeninggalkanKelas $izin)
    {
        if (!in_array($izin->status, ['disetujui_guru_piket', 'diverifikasi_security', 'selesai'])) {
            toast('Izin belum bisa dicetak.', 'error');
            return back();
        }

        $izin->load(['siswa.masterSiswa.rombels.kelas', 'guruKelasApprover', 'guruPiketApprover', 'securityVerifier', 'jadwalPelajaran.mataPelajaran', 'jadwalPelajaran.guru']);

        // 1. URL untuk verifikasi publik (tetap sama)
        $publicUrl = route('verifikasi.surat', $izin->uuid);
        $publicQrCode = QrCode::format('svg')->size(70)->generate($publicUrl);
        $publicQrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($publicQrCode);

        // 2. URL untuk aksi internal security (diubah ke route cerdas yang baru)
        $securityUrl = route('security.verifikasi.process-scan', $izin->uuid); // <-- BARIS INI DIPERBARUI
        $securityQrCode = QrCode::format('svg')->size(70)->generate($securityUrl);
        $securityQrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($securityQrCode);

        $pdf = Pdf::loadView('pdf.surat-izin-keluar', [
            'izin' => $izin,
            'publicQrCodeBase64' => $publicQrCodeBase64,
            'securityQrCodeBase64' => $securityQrCodeBase64,
        ]);

        return $pdf->stream('surat-izin-' . $izin->siswa->name . '.pdf');
    }

    /**
     * Menampilkan halaman riwayat persetujuan izin keluar oleh piket.
     */
    public function riwayat(Request $request)
    {
        $query = IzinMeninggalkanKelas::with(['siswa.masterSiswa.rombels.kelas', 'guruPiketApprover'])
            ->whereNotNull('guru_piket_approval_id'); // Hanya tampilkan yang pernah diproses piket

        // Filter berdasarkan pencarian nama siswa
        if ($request->filled('search')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // Filter berdasarkan rentang tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereDate('guru_piket_approved_at', '>=', $request->start_date)
                ->whereDate('guru_piket_approved_at', '<=', $request->end_date);
        }

        $riwayatIzin = $query->latest('guru_piket_approved_at')->paginate(20);

        return view('pages.piket.persetujuan-izin-keluar.riwayat', compact('riwayatIzin'));
    }
}
