<?php

namespace App\Http\Controllers\Piket;

use App\Http\Controllers\Controller;
use App\Models\Keterlambatan;
use App\Models\MasterSiswa;
use App\Models\User;
use App\Notifications\SiswaTerlambatNotification; // Kita akan buat notifikasi ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class PenangananTerlambatController extends Controller
{
    public function index(Request $request)
    {
        $hasilPencarian = null;
        // if ($request->filled('search')) {
        //     $hasilPencarian = MasterSiswa::with('rombels.kelas')
        //         ->where('nama_lengkap', 'like', '%' . $request->search . '%')
        //         ->orWhere('nis', 'like', '%' . $request->search . '%')
        //         ->get();
        // }
        return view('pages.piket.penanganan-terlambat.index', compact('hasilPencarian'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'master_siswa_id' => 'required|exists:master_siswa,id',
            'alasan' => 'required|string|min:5',
            'tindak_lanjut' => 'nullable|string',
        ]);

        try {
            $keterlambatan = Keterlambatan::create([
                'master_siswa_id' => $request->master_siswa_id,
                'jam_terlambat' => now(),
                'alasan' => $request->alasan,
                'tindak_lanjut' => $request->tindak_lanjut,
                'dicatat_oleh_id' => Auth::id(),
            ]);

            // Kirim Notifikasi ke Wali Kelas & Guru BK
            // $this->kirimNotifikasi($keterlambatan);

            toast('Data keterlambatan berhasil dicatat.', 'success');
            // return redirect()->route('piket.penanganan-terlambat.print', $keterlambatan->id);
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
        $keterlambatan->load(['siswa.rombels.kelas', 'pencatat']);
        $pdf = Pdf::loadView('pdf.surat-izin-masuk-kelas', compact('keterlambatan'));
        return $pdf->stream('surat-izin-masuk-' . $keterlambatan->siswa->nama_lengkap . '.pdf');
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
