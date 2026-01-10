<?php

namespace App\Http\Controllers\Piket;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\Keterlambatan;
use App\Models\PoinCategory;
use App\Models\PoinPeraturan;
use App\Models\SiswaPelanggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class VerifikasiTerlambatController extends Controller
{
    // Menampilkan daftar siswa yang perlu diverifikasi
    public function index()
    {
        $daftarSiswaTerlambat = Keterlambatan::with('siswa.rombels.kelas')
            ->where('status', 'dicatat_security')
            ->latest('waktu_dicatat_security')
            ->get();

        return view('pages.piket.verifikasi-terlambat.index', compact('daftarSiswaTerlambat'));
    }

    // Menampilkan halaman detail untuk verifikasi
    public function show(Keterlambatan $keterlambatan)
    {
        $keterlambatan->load('siswa.rombels.kelas', 'security');
        return view('pages.piket.verifikasi-terlambat.show', compact('keterlambatan'));
    }

    // Menyimpan data verifikasi dan men-generate PDF
    public function update(Request $request, Keterlambatan $keterlambatan)
    {
        $request->validate(['tindak_lanjut_piket' => 'nullable|string']);

        try {
            // 1. Ambil rombel dan waktu SAAT SISWA DICATAT oleh security
            $rombelSiswa = $keterlambatan->siswa->rombels()->first();
            $waktuTercatat = Carbon::parse($keterlambatan->waktu_dicatat_security);
            $namaHari = $waktuTercatat->isoFormat('dddd');
            $waktu = $waktuTercatat->format('H:i:s');

            $jadwalSaatItu = null;
            if ($rombelSiswa) {
                // Cari jadwal pelajaran berdasarkan HARI dan WAKTU saat siswa tercatat terlambat
                $jadwalSaatItu = JadwalPelajaran::where('rombel_id', $rombelSiswa->id)
                    ->where('hari', $namaHari)
                    ->where('jam_mulai', '<=', $waktu)
                    ->where('jam_selesai', '>=', $waktu)
                    ->first();
            }

            // 2. Update data keterlambatan dengan ID jadwal yang ditemukan
            $keterlambatan->update([
                'tindak_lanjut_piket' => $request->tindak_lanjut_piket,
                'diverifikasi_oleh_piket_id' => Auth::id(),
                'waktu_verifikasi_piket' => now(),
                'jadwal_pelajaran_id' => $jadwalSaatItu?->id,
                'status' => 'diverifikasi_piket',
            ]);

            // 2.5 Tambahkan Poin Pelanggaran Otomatis Berdasarkan Aturan
            $peraturanTerlambat = PoinPeraturan::whereHas('category', function($q) {
                    $q->where('name', 'Kedisiplinan');
                })
                ->where('pasal', 'Ketertiban')
                ->where('deskripsi', 'Terlambat')
                ->first();

            // Jika aturan tidak ditemukan, buat fallback (untuk keamanan logic)
            if (!$peraturanTerlambat) {
                $category = PoinCategory::firstOrCreate(['name' => 'Kedisiplinan']);
                $peraturanTerlambat = PoinPeraturan::firstOrCreate(
                    ['deskripsi' => 'Terlambat', 'poin_category_id' => $category->id],
                    [
                        'pasal' => 'Ketertiban',
                        'bobot_poin' => 1,
                    ]
                );
            }

            SiswaPelanggaran::create([
                'master_siswa_id' => $keterlambatan->master_siswa_id,
                'poin_peraturan_id' => $peraturanTerlambat->id,
                'tanggal' => now()->toDateString(),
                'catatan' => 'Terlambat (' . $peraturanTerlambat->bobot_poin . ' Poin) pada hari ' . $namaHari,
                'pelapor_id' => Auth::id(),
            ]);

            // 3. Siapkan data untuk PDF dengan me-load ulang semua relasi
            $keterlambatan->load(['siswa.rombels.kelas', 'guruPiket', 'jadwalPelajaran.mataPelajaran', 'jadwalPelajaran.guru']);

            // 4. Buat QR Codes
            $publicUrl = route('verifikasi.surat-terlambat', $keterlambatan->uuid);
            $publicQrCode = 'data:image/svg+xml;base64,' . base64_encode(QrCode::format('svg')->size(60)->generate($publicUrl));

            $guruKelasUrl = route('guru-kelas.verifikasi-terlambat.scan', $keterlambatan->uuid);
            $guruKelasQrCode = 'data:image/svg+xml;base64,' . base64_encode(QrCode::format('svg')->size(60)->generate($guruKelasUrl));

            // 5. Generate & stream PDF
            $pdf = Pdf::loadView('pdf.surat-izin-masuk-kelas', compact('keterlambatan', 'publicQrCode', 'guruKelasQrCode'));

            return $pdf->stream('surat-izin-masuk-' . $keterlambatan->siswa->nama_lengkap . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error verifying late record: ' . $e->getMessage());
            toast('Gagal memverifikasi data.', 'error');
            return back();
        }
    }
}
