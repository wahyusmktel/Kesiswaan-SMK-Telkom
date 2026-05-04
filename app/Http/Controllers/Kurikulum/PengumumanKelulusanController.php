<?php

namespace App\Http\Controllers\Kurikulum;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MasterSiswa;
use App\Models\PengumumanKelulusan;
use App\Models\Rombel;
use App\Models\SiswaKelulusan;
use App\Models\TahunPelajaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengumumanKelulusanController extends Controller
{
    public function index()
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first()
            ?? TahunPelajaran::latest()->first();

        $pengumuman = PengumumanKelulusan::where('tahun_pelajaran_id', $tahunAktif?->id)->first();

        // Ambil semua rombel kelas XII pada tahun pelajaran aktif
        $rombelXII = Rombel::with(['kelas', 'siswa'])
            ->whereHas('kelas', fn($q) => $q->where('nama_kelas', 'like', 'XII%'))
            ->when($tahunAktif, fn($q) => $q->where('tahun_pelajaran_id', $tahunAktif->id))
            ->get();

        // Ambil semua siswa kelas XII beserta data kelasnya
        $siswaDaftarList = collect();
        foreach ($rombelXII as $rombel) {
            foreach ($rombel->siswa as $siswa) {
                $siswaDaftarList->push([
                    'siswa' => $siswa,
                    'kelas' => $rombel->kelas->nama_kelas,
                    'rombel' => $rombel,
                ]);
            }
        }

        // Map status kelulusan per siswa jika pengumuman sudah ada
        $statusMap = [];
        if ($pengumuman) {
            $statusMap = SiswaKelulusan::where('pengumuman_kelulusan_id', $pengumuman->id)
                ->pluck('status', 'master_siswa_id')
                ->toArray();
        }

        $totalSiswa = $siswaDaftarList->count();
        $totalLulus = $pengumuman
            ? SiswaKelulusan::where('pengumuman_kelulusan_id', $pengumuman->id)->where('status', 'lulus')->count()
            : 0;
        $totalTidakLulus = $pengumuman
            ? SiswaKelulusan::where('pengumuman_kelulusan_id', $pengumuman->id)->where('status', 'tidak_lulus')->count()
            : 0;

        $totalBelumTtd = 0;
        if ($pengumuman && $totalLulus > 0) {
            $lulusIds   = SiswaKelulusan::where('pengumuman_kelulusan_id', $pengumuman->id)
                ->where('status', 'lulus')->pluck('id');
            $sudahTtd   = \App\Models\DigitalDocument::where('document_type', 'SKL')
                ->whereIn('reference_id', $lulusIds)->where('is_valid', true)->count();
            $totalBelumTtd = $lulusIds->count() - $sudahTtd;
        }

        $tahunPelajaranList = TahunPelajaran::orderByDesc('tahun')->get();

        return view('pages.kurikulum.pengumuman-kelulusan.index', compact(
            'pengumuman',
            'siswaDaftarList',
            'statusMap',
            'tahunAktif',
            'tahunPelajaranList',
            'totalSiswa',
            'totalLulus',
            'totalTidakLulus',
            'totalBelumTtd',
        ));
    }

    public function storePengumuman(Request $request)
    {
        $request->validate([
            'judul'               => 'required|string|max:255',
            'keterangan'          => 'nullable|string',
            'waktu_publikasi'     => 'required|date',
            'tahun_pelajaran_id'  => 'required|exists:tahun_pelajaran,id',
            'skl_aktif'           => 'nullable|boolean',
            'kop_surat'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'nomor_surat_prefix'  => 'nullable|string|max:100',
            'nomor_surat_start'   => 'nullable|numeric|min:1',
            'kota_surat'          => 'nullable|string|max:100',
            'tanggal_surat'       => 'nullable|date',
            'nama_kepala_sekolah' => 'nullable|string|max:255',
            'nip_kepala_sekolah'  => 'nullable|string|max:50',
            'ttd_stempel'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $existing = PengumumanKelulusan::where('tahun_pelajaran_id', $request->tahun_pelajaran_id)->first();

        $data = [
            'judul'               => $request->judul,
            'keterangan'          => $request->keterangan,
            'waktu_publikasi'     => $request->waktu_publikasi,
            'skl_aktif'           => $request->boolean('skl_aktif'),
            'created_by'          => Auth::id(),
            'nomor_surat_prefix'  => $request->nomor_surat_prefix,
            'nomor_surat_start'   => (int) ltrim($request->nomor_surat_start ?: '1', '0') ?: 1,
            'kota_surat'          => $request->kota_surat,
            'tanggal_surat'       => $request->tanggal_surat,
            'nama_kepala_sekolah' => $request->nama_kepala_sekolah,
            'nip_kepala_sekolah'  => $request->nip_kepala_sekolah,
        ];

        if ($request->hasFile('kop_surat')) {
            if ($existing?->kop_surat_path) {
                Storage::disk('public')->delete($existing->kop_surat_path);
            }
            $data['kop_surat_path'] = $request->file('kop_surat')->store('pengumuman-kelulusan', 'public');
        }

        if ($request->hasFile('ttd_stempel')) {
            if ($existing?->ttd_stempel_path) {
                Storage::disk('public')->delete($existing->ttd_stempel_path);
            }
            $data['ttd_stempel_path'] = $request->file('ttd_stempel')->store('pengumuman-kelulusan', 'public');
        }

        PengumumanKelulusan::updateOrCreate(
            ['tahun_pelajaran_id' => $request->tahun_pelajaran_id],
            $data
        );

        return back()->with('success', 'Pengumuman kelulusan berhasil disimpan.');
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'pengumuman_kelulusan_id' => 'required|exists:pengumuman_kelulusans,id',
            'master_siswa_id'         => 'required|exists:master_siswa,id',
            'status'                  => 'required|in:lulus,tidak_lulus',
            'catatan'                 => 'nullable|string|max:500',
        ]);

        SiswaKelulusan::updateOrCreate(
            [
                'pengumuman_kelulusan_id' => $request->pengumuman_kelulusan_id,
                'master_siswa_id'         => $request->master_siswa_id,
            ],
            [
                'status'  => $request->status,
                'catatan' => $request->catatan,
            ]
        );

        return response()->json(['success' => true]);
    }

    public function updateStatusBulk(Request $request)
    {
        $request->validate([
            'pengumuman_kelulusan_id' => 'required|exists:pengumuman_kelulusans,id',
            'status'                  => 'required|in:lulus,tidak_lulus',
        ]);

        $pengumuman = PengumumanKelulusan::findOrFail($request->pengumuman_kelulusan_id);

        $rombelXII = Rombel::with('siswa')
            ->whereHas('kelas', fn($q) => $q->where('nama_kelas', 'like', 'XII%'))
            ->where('tahun_pelajaran_id', $pengumuman->tahun_pelajaran_id)
            ->get();

        DB::transaction(function () use ($rombelXII, $request) {
            foreach ($rombelXII as $rombel) {
                foreach ($rombel->siswa as $siswa) {
                    SiswaKelulusan::updateOrCreate(
                        [
                            'pengumuman_kelulusan_id' => $request->pengumuman_kelulusan_id,
                            'master_siswa_id'         => $siswa->id,
                        ],
                        ['status' => $request->status]
                    );
                }
            }
        });

        return back()->with('success', 'Status semua siswa berhasil diperbarui.');
    }

    public function downloadSKL(PengumumanKelulusan $pengumuman, MasterSiswa $siswa)
    {
        $kelulusan = SiswaKelulusan::where('pengumuman_kelulusan_id', $pengumuman->id)
            ->where('master_siswa_id', $siswa->id)
            ->firstOrFail();

        $siswa->load(['rombels.kelas', 'rombels.tahunPelajaran']);

        $rombelXII = $siswa->rombels
            ->filter(fn($r) => str_starts_with($r->kelas->nama_kelas ?? '', 'XII'))
            ->first() ?? $siswa->rombels->first();

        $tahunPelajaran = $pengumuman->tahunPelajaran;

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
            'rombel'        => $rombelXII,
            'tahunPelajaran'=> $tahunPelajaran,
            'nomorSurat'    => $nomorSurat,
            'kopBase64'     => $kopBase64,
            'ttdBase64'     => $ttdBase64,
            'qrBase64'      => $qrBase64,
            'digitalDoc'    => $digitalDoc,
        ])->setPaper('A4', 'portrait');

        $filename = 'SKL_' . str_replace(' ', '_', $siswa->nama_lengkap) . '.pdf';

        return $pdf->download($filename);
    }

    private function generateQrBase64(string $url): string
    {
        $options = new \chillerlan\QRCode\QROptions([
            'outputType'   => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
            'imageBase64'  => true,
            'scale'        => 5,
            'quietzoneSize'=> 1,
            'eccLevel'     => \chillerlan\QRCode\QRCode::ECC_M,
        ]);
        return (new \chillerlan\QRCode\QRCode($options))->render($url);
    }

    private function generateNomorSurat(PengumumanKelulusan $pengumuman, int $siswaId): string
    {
        $siswaIds = SiswaKelulusan::where('pengumuman_kelulusan_id', $pengumuman->id)
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
