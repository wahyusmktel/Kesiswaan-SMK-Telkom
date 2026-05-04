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

        $kelulusan = \App\Models\SiswaKelulusan::where('pengumuman_kelulusan_id', $pengumuman->id)
            ->where('master_siswa_id', $siswa->id)
            ->first();

        if (!$kelulusan || $kelulusan->status !== 'lulus') {
            abort(403, 'Surat keterangan lulus hanya tersedia untuk siswa yang dinyatakan LULUS.');
        }

        $siswa->load(['rombels.kelas', 'rombels.tahunPelajaran']);
        $rombel         = $rombelXII;
        $tahunPelajaran = $rombelXII->tahunPelajaran;

        $nomorSurat = $this->generateNomorSurat($pengumuman, $siswa->id);
        $kopBase64  = $this->imageToBase64($pengumuman->kop_surat_path);
        $ttdBase64  = $this->imageToBase64($pengumuman->ttd_stempel_path);

        $digitalDoc = \App\Models\DigitalDocument::where('document_type', 'SKL')
            ->where('reference_id', $kelulusan->id)
            ->where('is_valid', true)
            ->first();

        $qrBase64 = $digitalDoc ? $this->generateQrBase64(route('verifikasi.dokumen', $digitalDoc->token)) : null;

        $pdf = Pdf::loadView('pdf.surat-keterangan-lulus', [
            'pengumuman'    => $pengumuman,
            'siswa'         => $siswa,
            'kelulusan'     => $kelulusan,
            'rombel'        => $rombel,
            'tahunPelajaran'=> $tahunPelajaran,
            'nomorSurat'    => $nomorSurat,
            'kopBase64'     => $kopBase64,
            'ttdBase64'     => $ttdBase64,
            'qrBase64'      => $qrBase64,
            'digitalDoc'    => $digitalDoc,
        ])->setPaper('A4', 'portrait');

        return $pdf->download('SKL_' . str_replace(' ', '_', $siswa->nama_lengkap) . '.pdf');
    }

    private function generateQrBase64(string $url): string
    {
        $options = new \chillerlan\QRCode\QROptions([
            'outputType'    => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
            'imageBase64'   => true,
            'scale'         => 5,
            'quietzoneSize' => 1,
            'eccLevel'      => \chillerlan\QRCode\QRCode::ECC_M,
        ]);
        return (new \chillerlan\QRCode\QRCode($options))->render($url);
    }

    private function generateNomorSurat(\App\Models\PengumumanKelulusan $pengumuman, int $siswaId): string
    {
        $siswaIds = \App\Models\SiswaKelulusan::where('pengumuman_kelulusan_id', $pengumuman->id)
            ->join('master_siswa', 'siswa_kelulusans.master_siswa_id', '=', 'master_siswa.id')
            ->orderBy('master_siswa.nama_lengkap')
            ->pluck('siswa_kelulusans.master_siswa_id')
            ->values();

        $rank  = $siswaIds->search($siswaId);
        $nomor = $pengumuman->nomor_surat_start + ($rank !== false ? $rank : 0);
        $bulan = $pengumuman->tanggal_surat
            ? \Carbon\Carbon::parse($pengumuman->tanggal_surat)->format('m')
            : now()->format('m');
        $tahun = $pengumuman->tahunPelajaran->tahun ?? now()->year;
        $tahunAngka = (int) explode('/', $tahun)[0];

        $prefix = $pengumuman->nomor_surat_prefix ?? 'SKL';

        return str_pad($nomor, 4, '0', STR_PAD_LEFT) . '/' . $prefix . '/' . \Carbon\Carbon::createFromFormat('m', $bulan)->translatedFormat('n') . '/' . $tahunAngka;
    }

    private function imageToBase64(?string $storagePath): ?string
    {
        if (!$storagePath) return null;

        $fullPath = storage_path('app/public/' . $storagePath);
        if (!file_exists($fullPath)) return null;

        $mime = mime_content_type($fullPath);
        $data = base64_encode(file_get_contents($fullPath));

        return "data:{$mime};base64,{$data}";
    }
}
