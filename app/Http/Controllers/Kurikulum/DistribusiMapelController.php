<?php

namespace App\Http\Controllers\Kurikulum;

use App\Http\Controllers\Controller;
use App\Models\MasterGuru;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;

class DistribusiMapelController extends Controller
{
    public function index()
    {
        // 1. Cari Tahun Pelajaran yang Aktif
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $tahunAktifId = $tahunAktif ? $tahunAktif->id : null;

        // 2. Ambil data guru beserta jadwal pelajaran yang sesuai dengan tahun aktif
        // Gunakan whereHas di tingkat atas dan with untuk eager loading agar data pasti muncul
        $gurus = MasterGuru::whereHas('jadwalPelajaran', function($q) use ($tahunAktifId) {
            $q->whereHas('rombel', function($rq) use ($tahunAktifId) {
                $rq->where('tahun_pelajaran_id', $tahunAktifId);
            });
        })
        ->with(['jadwalPelajaran' => function ($query) use ($tahunAktifId) {
            $query->whereHas('rombel', function ($q) use ($tahunAktifId) {
                $q->where('tahun_pelajaran_id', $tahunAktifId);
            })->with(['mataPelajaran', 'rombel.kelas']);
        }])
        ->orderBy('nama_lengkap')
        ->get();

        // 3. Olah data untuk rekapitulasi
        $rekapitulasi = $gurus->map(function ($guru) {
            $distribusi = [];
            $totalJP = 0;

            foreach ($guru->jadwalPelajaran as $jadwal) {
                // Pastikan ada mata pelajaran dan rombel
                if (!$jadwal->mataPelajaran || !$jadwal->rombel) continue;

                $mapelId = $jadwal->mata_pelajaran_id;
                $rombelId = $jadwal->rombel_id;
                $key = $mapelId . '-' . $rombelId;

                if (!isset($distribusi[$key])) {
                    $distribusi[$key] = [
                        'nama_mapel' => $jadwal->mataPelajaran->nama_mapel,
                        'kelas' => $jadwal->rombel->kelas->nama_kelas ?? $jadwal->rombel->nama_rombel ?? '-',
                        'jumlah_jam' => 0,
                    ];
                }

                $distribusi[$key]['jumlah_jam']++;
                $totalJP++;
            }

            return (object) [
                'nama_guru' => $guru->nama_lengkap,
                'distribusi' => collect(array_values($distribusi)), // Ensure it's a list for @foreach
                'total_jp' => $totalJP,
            ];
        });

        // Hitung statistik untuk header
        $stats = [
            'total_guru' => $rekapitulasi->count(),
            'total_jp' => $rekapitulasi->sum('total_jp'),
            'avg_jp' => $rekapitulasi->count() > 0 ? round($rekapitulasi->average('total_jp'), 1) : 0,
        ];

        return view('pages.kurikulum.distribusi-mapel.index', compact('rekapitulasi', 'tahunAktif', 'stats'));
    }
}
