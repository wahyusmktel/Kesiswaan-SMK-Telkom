<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\PengumumanKelulusan;
use App\Models\SiswaKelulusan;
use App\Models\TahunPelajaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class PengumumanKelulusanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $siswa = $user->masterSiswa;

        if (!$siswa) {
            return redirect()->route('siswa.dashboard.index')->with('error', 'Data siswa tidak ditemukan.');
        }

        // Cek apakah siswa ini adalah kelas XII
        $rombelXII = $siswa->rombels()
            ->whereHas('kelas', fn($q) => $q->where('nama_kelas', 'like', 'XII%'))
            ->with(['kelas', 'tahunPelajaran'])
            ->first();

        if (!$rombelXII) {
            return view('pages.siswa.pengumuman-kelulusan.index', [
                'bukan_kelas_xii' => true,
                'pengumuman'      => null,
                'kelulusan'       => null,
                'rombel'          => null,
            ]);
        }

        $tahunPelajaran = $rombelXII->tahunPelajaran;

        $pengumuman = PengumumanKelulusan::where('tahun_pelajaran_id', $tahunPelajaran->id)->first();

        $kelulusan = null;
        if ($pengumuman && $pengumuman->sudahDipublikasikan()) {
            $kelulusan = SiswaKelulusan::where('pengumuman_kelulusan_id', $pengumuman->id)
                ->where('master_siswa_id', $siswa->id)
                ->first();
        }

        return view('pages.siswa.pengumuman-kelulusan.index', compact(
            'pengumuman',
            'kelulusan',
            'siswa',
            'rombelXII',
            'tahunPelajaran',
        ));
    }

    public function downloadSKL()
    {
        $user = Auth::user();
        $siswa = $user->masterSiswa;

        if (!$siswa) {
            abort(403);
        }

        $rombelXII = $siswa->rombels()
            ->whereHas('kelas', fn($q) => $q->where('nama_kelas', 'like', 'XII%'))
            ->with(['kelas', 'tahunPelajaran'])
            ->first();

        if (!$rombelXII) {
            abort(403, 'Anda bukan siswa kelas XII.');
        }

        $pengumuman = PengumumanKelulusan::where('tahun_pelajaran_id', $rombelXII->tahunPelajaran->id)->first();

        if (!$pengumuman || !$pengumuman->sudahDipublikasikan()) {
            abort(403, 'Pengumuman kelulusan belum dipublikasikan.');
        }

        $kelulusan = SiswaKelulusan::where('pengumuman_kelulusan_id', $pengumuman->id)
            ->where('master_siswa_id', $siswa->id)
            ->first();

        if (!$kelulusan || $kelulusan->status !== 'lulus') {
            abort(403, 'Surat keterangan lulus hanya tersedia untuk siswa yang dinyatakan LULUS.');
        }

        $siswa->load(['rombels.kelas', 'rombels.tahunPelajaran']);
        $rombel = $rombelXII;
        $tahunPelajaran = $rombelXII->tahunPelajaran;

        $pdf = Pdf::loadView('pdf.surat-keterangan-lulus', compact('pengumuman', 'siswa', 'kelulusan', 'rombel', 'tahunPelajaran'))
            ->setPaper('A4', 'portrait');

        $filename = 'SKL_' . str_replace(' ', '_', $siswa->nama_lengkap) . '.pdf';

        return $pdf->download($filename);
    }
}
