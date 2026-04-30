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
        ));
    }

    public function storePengumuman(Request $request)
    {
        $request->validate([
            'judul'              => 'required|string|max:255',
            'keterangan'         => 'nullable|string',
            'waktu_publikasi'    => 'required|date',
            'tahun_pelajaran_id' => 'required|exists:tahun_pelajaran,id',
            'skl_aktif'          => 'nullable|boolean',
        ]);

        PengumumanKelulusan::updateOrCreate(
            ['tahun_pelajaran_id' => $request->tahun_pelajaran_id],
            [
                'judul'           => $request->judul,
                'keterangan'      => $request->keterangan,
                'waktu_publikasi' => $request->waktu_publikasi,
                'skl_aktif'       => $request->boolean('skl_aktif'),
                'created_by'      => Auth::id(),
            ]
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
        $rombel = $siswa->rombels->first();

        $tahunPelajaran = $pengumuman->tahunPelajaran;

        $pdf = Pdf::loadView('pdf.surat-keterangan-lulus', compact('pengumuman', 'siswa', 'kelulusan', 'rombel', 'tahunPelajaran'))
            ->setPaper('A4', 'portrait');

        $filename = 'SKL_' . str_replace(' ', '_', $siswa->nama_lengkap) . '.pdf';

        return $pdf->download($filename);
    }
}
