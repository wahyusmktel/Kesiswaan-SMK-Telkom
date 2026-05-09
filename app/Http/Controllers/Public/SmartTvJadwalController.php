<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\JamPelajaran;
use App\Models\Rombel;
use App\Models\TahunPelajaran;

class SmartTvJadwalController extends Controller
{
    private const DAYS = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    public function index()
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        $rombels = Rombel::with(['kelas', 'waliKelas', 'tahunPelajaran'])
            ->where('tahun_pelajaran_id', $tahunAktif?->id)
            ->orderBy('kelas_id')
            ->get();

        $allJam = JamPelajaran::orderBy('jam_ke')->get();
        $jamKeList = $allJam->pluck('jam_ke')->unique()->sort()->values();

        // Build jamLookup: "jamKe-Hari" => JamPelajaran
        $jamSlotsGrouped = $allJam->groupBy('jam_ke');
        $jamLookup = [];
        foreach ($jamSlotsGrouped as $ke => $slots) {
            $default = $slots->whereNull('hari')->first();
            foreach (self::DAYS as $day) {
                $specific = $slots->where('hari', $day)->first();
                $jamLookup["{$ke}-{$day}"] = $specific ?? $default;
            }
        }

        // All schedules grouped by rombel_id → hari-jamKe → data
        $semua = JadwalPelajaran::with(['mataPelajaran', 'guru', 'rombel.kelas'])
            ->whereIn('rombel_id', $rombels->pluck('id'))
            ->get();

        $jadwalPerRombel = [];
        foreach ($semua as $j) {
            $jadwalPerRombel[$j->rombel_id]["{$j->hari}-{$j->jam_ke}"] = [
                'mapel' => $j->mataPelajaran->nama_mapel ?? '-',
                'kode'  => $j->mataPelajaran->kode_mapel ?? '-',
                'guru'  => $j->guru->nama_lengkap ?? '-',
                'mulai' => $j->jam_mulai,
                'selesai' => $j->jam_selesai,
            ];
        }

        return view('public.smart-tv-jadwal', [
            'rombels'        => $rombels,
            'days'           => self::DAYS,
            'jamKeList'      => $jamKeList,
            'jamLookup'      => $jamLookup,
            'jadwalPerRombel'=> $jadwalPerRombel,
            'tahunAktif'     => $tahunAktif,
        ]);
    }
}
