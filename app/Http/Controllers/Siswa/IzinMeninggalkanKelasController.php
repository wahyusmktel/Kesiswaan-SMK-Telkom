<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\IzinMeninggalkanKelas;
use App\Models\JadwalPelajaran;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class IzinMeninggalkanKelasController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $riwayatIzin = IzinMeninggalkanKelas::where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        // Cari jadwal yang sedang berlangsung untuk ditampilkan di form
        $jadwalSaatIni = null;
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        
        if ($tahunAktif) {
            $rombelAktif = $user->masterSiswa?->rombels()
                ->where('tahun_pelajaran_id', $tahunAktif->id)
                ->first();
        } else {
            $rombelAktif = null;
        }

        if ($rombelAktif) {
            $namaHariIni = $this->getNamaHari(now()->dayOfWeek);
            $waktuSaatIni = now()->format('H:i:s');

            $jadwalSaatIni = JadwalPelajaran::with(['mataPelajaran', 'guru'])
                ->where('rombel_id', $rombelAktif->id)
                ->where('hari', $namaHariIni)
                ->where('jam_mulai', '<=', $waktuSaatIni)
                ->where('jam_selesai', '>=', $waktuSaatIni)
                ->first();
        }

        return view('pages.siswa.izin-keluar-kelas.index', compact('riwayatIzin', 'jadwalSaatIni'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_izin' => 'required|in:keluar_sekolah,dalam_lingkungan',
            'tujuan' => 'required|string|max:255',
            'keterangan' => 'required|string|min:5|max:1000',
            'foto' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'estimasi_kembali' => 'required|date_format:H:i',
        ], [
            'jenis_izin.required' => 'Pilih jenis izin terlebih dahulu.',
            'tujuan.required' => 'Tujuan / keperluan izin wajib diisi.',
            'keterangan.required' => 'Keterangan alasan izin wajib diisi.',
            'keterangan.min' => 'Keterangan alasan izin minimal 5 karakter.',
            'foto.required' => 'Foto selfie atau kondisi saat ini wajib diunggah.',
            'foto.image' => 'File unggahan harus berupa gambar/foto.',
            'foto.mimes' => 'Format foto harus berupa JPG, JPEG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran file foto maksimal 5MB.',
            'estimasi_kembali.required' => 'Perkiraan jam kembali wajib diisi.',
            'estimasi_kembali.date_format' => 'Format perkiraan kembali tidak valid (HH:MM).',
        ]);

        try {
            $user = Auth::user();
            $tahunAktif = TahunPelajaran::where('is_active', true)->first();
            
            if (!$tahunAktif) {
                toast('Tahun ajaran aktif belum diatur.', 'error');
                return back();
            }
            
            $rombelAktif = $user->masterSiswa->rombels()
                ->where('tahun_pelajaran_id', $tahunAktif->id)
                ->first();

            if (!$rombelAktif) {
                toast('Anda tidak terdaftar di rombel manapun pada tahun ajaran ini.', 'error');
                return back();
            }

            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('izin_keluar_kelas', 'public');
            }

            IzinMeninggalkanKelas::create([
                'user_id' => $user->id,
                'rombel_id' => $rombelAktif->id,
                'jadwal_pelajaran_id' => $request->jadwal_pelajaran_id,
                'jenis_izin' => $request->jenis_izin,
                'tujuan' => $request->tujuan,
                'keterangan' => $request->keterangan,
                'foto' => $fotoPath,
                'estimasi_kembali' => now()->setTimeFromTimeString($request->estimasi_kembali),
                'status' => 'diajukan',
            ]);

            toast('Pengajuan berhasil dikirim. Silakan temui guru kelas Anda.', 'success');
            return redirect()->route('siswa.izin-keluar-kelas.index');
        } catch (\Exception $e) {
            Log::error('Error creating leave permit: ' . $e->getMessage());
            toast('Gagal membuat pengajuan izin.', 'error');
            return back()->withInput();
        }
    }

    private function getNamaHari($dayOfWeek)
    {
        $hari = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
        return $hari[$dayOfWeek] ?? 'Tidak Diketahui';
    }
}
