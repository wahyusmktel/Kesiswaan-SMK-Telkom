<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\PengumumanKelulusan;
use App\Models\SiswaKelulusan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class PengumumanKelulusanController extends Controller
{
    private function getSiswaLulusData()
    {
        $user  = Auth::user();
        $siswa = $user->masterSiswa;

        if (!$siswa) return null;

        $rombelXII = $siswa->rombels()
            ->whereHas('kelas', fn($q) => $q->where('nama_kelas', 'like', 'XII%'))
            ->with(['kelas', 'tahunPelajaran'])
            ->first();

        if (!$rombelXII) return null;

        $pengumuman = PengumumanKelulusan::where('tahun_pelajaran_id', $rombelXII->tahunPelajaran->id)->first();

        if (!$pengumuman || !$pengumuman->sudahDipublikasikan()) return null;

        $kelulusan = SiswaKelulusan::where('pengumuman_kelulusan_id', $pengumuman->id)
            ->where('master_siswa_id', $siswa->id)
            ->first();

        if (!$kelulusan || $kelulusan->status !== 'lulus') return null;

        return compact('siswa', 'rombelXII', 'pengumuman', 'kelulusan');
    }

    public function index()
    {
        $user  = Auth::user();
        $siswa = $user->masterSiswa;

        if (!$siswa) {
            return redirect()->route('siswa.dashboard.index')->with('error', 'Data siswa tidak ditemukan.');
        }

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
        $pengumuman     = PengumumanKelulusan::where('tahun_pelajaran_id', $tahunPelajaran->id)->first();

        $kelulusan = null;
        if ($pengumuman && $pengumuman->sudahDipublikasikan()) {
            $kelulusan = SiswaKelulusan::where('pengumuman_kelulusan_id', $pengumuman->id)
                ->where('master_siswa_id', $siswa->id)
                ->first();
        }

        return view('pages.siswa.pengumuman-kelulusan.index', compact(
            'pengumuman', 'kelulusan', 'siswa', 'rombelXII', 'tahunPelajaran',
        ));
    }

    public function kartuKelulusan()
    {
        $data = $this->getSiswaLulusData();

        if (!$data) {
            return redirect()->route('siswa.pengumuman-kelulusan.index')
                ->with('error', 'Kartu kelulusan hanya tersedia untuk siswa kelas XII yang dinyatakan LULUS setelah pengumuman dipublikasikan.');
        }

        return view('pages.siswa.pengumuman-kelulusan.kartu-kelulusan', [
            'siswa'         => $data['siswa'],
            'rombel'        => $data['rombelXII'],
            'tahunPelajaran'=> $data['rombelXII']->tahunPelajaran,
            'pengumuman'    => $data['pengumuman'],
            'kelulusan'     => $data['kelulusan'],
        ]);
    }

    public function downloadSKL()
    {
        $user  = Auth::user();
        $siswa = $user->masterSiswa;

        if (!$siswa) abort(403);

        $rombelXII = $siswa->rombels()
            ->whereHas('kelas', fn($q) => $q->where('nama_kelas', 'like', 'XII%'))
            ->with(['kelas', 'tahunPelajaran'])
            ->first();

        if (!$rombelXII) abort(403, 'Anda bukan siswa kelas XII.');

        $pengumuman = PengumumanKelulusan::where('tahun_pelajaran_id', $rombelXII->tahunPelajaran->id)->first();

        if (!$pengumuman || !$pengumuman->sudahDipublikasikan()) {
            abort(403, 'Pengumuman kelulusan belum dipublikasikan.');
        }

        if (!$pengumuman->skl_aktif) {
            abort(403, 'Download SKL belum diaktifkan oleh Waka Kurikulum.');
        }

        $kelulusan = SiswaKelulusan::where('pengumuman_kelulusan_id', $pengumuman->id)
            ->where('master_siswa_id', $siswa->id)
            ->first();

        if (!$kelulusan || $kelulusan->status !== 'lulus') {
            abort(403, 'Surat keterangan lulus hanya tersedia untuk siswa yang dinyatakan LULUS.');
        }

        $siswa->load(['rombels.kelas', 'rombels.tahunPelajaran']);
        $rombel         = $rombelXII;
        $tahunPelajaran = $rombelXII->tahunPelajaran;

        $pdf = Pdf::loadView('pdf.surat-keterangan-lulus', compact('pengumuman', 'siswa', 'kelulusan', 'rombel', 'tahunPelajaran'))
            ->setPaper('A4', 'portrait');

        return $pdf->download('SKL_' . str_replace(' ', '_', $siswa->nama_lengkap) . '.pdf');
    }
}
