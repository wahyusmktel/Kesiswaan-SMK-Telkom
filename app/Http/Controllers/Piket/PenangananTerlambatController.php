<?php

namespace App\Http\Controllers\Piket;

use App\Http\Controllers\Controller;
use App\Models\Keterlambatan;
use App\Models\MasterSiswa;
use App\Models\User;
use App\Models\JadwalPelajaran;
use App\Models\PoinCategory;
use App\Models\PoinPeraturan;
use App\Models\SiswaPelanggaran;
use App\Notifications\SiswaTerlambatNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class PenangananTerlambatController extends Controller
{
    public function index(Request $request)
    {
        $hasilPencarian = null;
        $terlambatHariIni = Keterlambatan::with(['siswa.user', 'siswa.rombels.kelas', 'guruPiket'])
            ->whereDate('waktu_dicatat_security', Carbon::today())
            ->latest()
            ->get();

        return view('pages.piket.penanganan-terlambat.index', compact('hasilPencarian', 'terlambatHariIni'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'master_siswa_id' => 'required|exists:master_siswa,id',
            'alasan' => 'required|string|min:5',
            'tindak_lanjut' => 'nullable|string',
        ]);

        try {
            $siswa = MasterSiswa::findOrFail($request->master_siswa_id);
            $now = Carbon::now();
            $today = $now->toDateString();

            // Cek duplikasi: Apakah siswa ini sudah dicatat terlambat HARI INI?
            $sudahAda = Keterlambatan::where('master_siswa_id', $siswa->id)
                ->whereDate('waktu_dicatat_security', $today)
                ->first();

            if ($sudahAda) {
                toast('Siswa ini sudah dicatat keterlambatannya pada hari ini (' . $sudahAda->waktu_dicatat_security->format('H:i') . ')', 'warning');
                return back()->withInput();
            }

            $namaHari = $now->isoFormat('dddd');
            $waktu = $now->format('H:i:s');

            // Cari jadwal pelajaran saat ini
            $rombelSiswa = $siswa->rombels()->first();
            $jadwalSaatItu = null;
            if ($rombelSiswa) {
                $jadwalSaatItu = JadwalPelajaran::where('rombel_id', $rombelSiswa->id)
                    ->where('hari', $namaHari)
                    ->where('jam_mulai', '<=', $waktu)
                    ->where('jam_selesai', '>=', $waktu)
                    ->first();
            }

            $keterlambatan = Keterlambatan::create([
                'master_siswa_id' => $siswa->id,
                'dicatat_oleh_security_id' => Auth::id(), // Piket acts as recorder here
                'waktu_dicatat_security' => $now,
                'alasan_siswa' => $request->alasan,
                'diverifikasi_oleh_piket_id' => Auth::id(),
                'waktu_verifikasi_piket' => $now,
                'tindak_lanjut_piket' => $request->tindak_lanjut,
                'jadwal_pelajaran_id' => $jadwalSaatItu?->id,
                'status' => 'diverifikasi_piket',
            ]);

            // Tambahkan Poin Pelanggaran Otomatis Berdasarkan Aturan
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
                'tanggal' => $now->toDateString(),
                'catatan' => 'Terlambat (' . $peraturanTerlambat->bobot_poin . ' Poin) dicatat langsung oleh Piket pada hari ' . $namaHari,
                'pelapor_id' => Auth::id(),
            ]);

            toast('Data keterlambatan berhasil dicatat.', 'success');
            return redirect()->route('piket.penanganan-terlambat.index')
                ->with('print_url', route('piket.penanganan-terlambat.print', $keterlambatan->id));
        } catch (\Exception $e) {
            Log::error('Error storing late record: ' . $e->getMessage());
            toast('Gagal menyimpan data keterlambatan.', 'error');
            return back()->withInput();
        }
    }

    public function printPdf(Keterlambatan $keterlambatan)
    {
        $keterlambatan->load(['siswa.user', 'siswa.rombels.kelas', 'guruPiket']);

        // Generate QR Codes required by the template (Admission Slip)
        $publicUrl = route('verifikasi.surat-terlambat', $keterlambatan->uuid);
        $publicQrCode = 'data:image/svg+xml;base64,' . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(60)->generate($publicUrl));

        $guruKelasUrl = route('guru-kelas.verifikasi-terlambat.scan', $keterlambatan->uuid);
        $guruKelasQrCode = 'data:image/svg+xml;base64,' . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(60)->generate($guruKelasUrl));

        $pdf = Pdf::loadView('pdf.surat-izin-masuk-kelas', compact('keterlambatan', 'publicQrCode', 'guruKelasQrCode'));
        return $pdf->stream('surat-izin-masuk-' . $keterlambatan->siswa->user->name . '.pdf');
    }

    private function kirimNotifikasi(Keterlambatan $keterlambatan)
    {
        $keterlambatan->load('siswa.rombels.waliKelas', 'siswa.user');

        // Cari Wali Kelas dari rombel siswa
        $waliKelas = $keterlambatan->siswa->rombels->first()->waliKelas ?? null;
        if ($waliKelas) {
            // Kita akan buat Notifikasi ini di langkah berikutnya
            // $waliKelas->notify(new SiswaTerlambatNotification($keterlambatan));
        }

        // Kirim ke semua Guru BK
        $guruBKs = User::role('Guru BK')->get();
        foreach ($guruBKs as $bk) {
            // $bk->notify(new SiswaTerlambatNotification($keterlambatan));
        }
    }

    /**
     * API Internal untuk Live Search Siswa via AJAX
     */
    public function search(Request $request)
    {
        $query = $request->get('query');

        // Validasi minimal 3 karakter biar query ga berat
        if (strlen($query) < 3) {
            return response()->json([]);
        }

        try {
            $siswa = MasterSiswa::with('rombels.kelas')
                ->where('nama_lengkap', 'like', "%{$query}%")
                ->orWhere('nis', 'like', "%{$query}%")
                ->limit(10) // Batasi 10 hasil biar cepat
                ->get()
                ->map(function ($s) {
                    return [
                        'id' => $s->id,
                        'nis' => $s->nis,
                        'nama_lengkap' => $s->nama_lengkap,
                        'kelas' => $s->rombels->first()?->kelas->nama_kelas ?? 'Tanpa Kelas',
                    ];
                });

            return response()->json($siswa);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
