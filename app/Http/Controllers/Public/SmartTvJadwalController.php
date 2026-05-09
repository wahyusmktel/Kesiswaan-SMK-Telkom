<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\JamPelajaran;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use Carbon\Carbon;

class SmartTvJadwalController extends Controller
{
    private const DAYS = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    private const ACTIVITY_LABELS = [
        'istirahat'    => 'Istirahat',
        'ishoma'       => 'Istirahat Sholat & Makan',
        'sholawat_pagi'=> 'Sholawat Pagi',
        'upacara'      => 'Upacara Bendera',
        'kegiatan_4r'  => 'Kegiatan 4R',
    ];

    public function index()
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        $rombels = Rombel::with(['kelas', 'waliKelas'])
            ->where('tahun_pelajaran_id', $tahunAktif?->id)
            ->orderBy('kelas_id')
            ->get();

        // Hari ini
        $todayIndex = Carbon::now()->dayOfWeek; // 0=Sun, 1=Mon, ...
        $dayMap     = [0 => null, 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
        $todayName  = $dayMap[$todayIndex] ?? 'Senin';

        // Semua slot jam (default + hari ini)
        $allJam = JamPelajaran::orderBy('jam_ke')->get();
        $jamSlotsGrouped = $allJam->groupBy('jam_ke');

        // Buat daftar slot hari ini (urut jam_ke)
        $todaySlots = [];
        foreach ($jamSlotsGrouped as $ke => $slots) {
            $default  = $slots->whereNull('hari')->first();
            $specific = $slots->where('hari', $todayName)->first();
            $slot     = $specific ?? $default;
            if ($slot) {
                $todaySlots[$ke] = [
                    'jam_ke'        => $ke,
                    'jam_mulai'     => Carbon::parse($slot->jam_mulai)->format('H:i'),
                    'jam_selesai'   => Carbon::parse($slot->jam_selesai)->format('H:i'),
                    'tipe_kegiatan' => $slot->tipe_kegiatan,
                    'keterangan'    => $slot->keterangan,
                    'is_activity'   => $this->isSpecialActivity($slot, $todayName),
                    'activity_label'=> $slot->tipe_kegiatan
                        ? (self::ACTIVITY_LABELS[$slot->tipe_kegiatan] ?? ucwords(str_replace('_', ' ', $slot->tipe_kegiatan)))
                        : null,
                ];
            }
        }
        ksort($todaySlots);

        // Semua jadwal pelajaran hari ini
        $jadwalHariIni = JadwalPelajaran::with(['mataPelajaran', 'guru'])
            ->whereIn('rombel_id', $rombels->pluck('id'))
            ->where('hari', $todayName)
            ->get()
            ->groupBy('rombel_id')
            ->map(fn($items) => $items->keyBy('jam_ke'));

        // Susun data per rombel
        $kelasData = $rombels->map(function ($rombel) use ($todaySlots, $jadwalHariIni) {
            $rombelJadwal = $jadwalHariIni->get($rombel->id, collect());

            $slots = collect($todaySlots)->map(function ($slot) use ($rombelJadwal) {
                $pelajaran = $rombelJadwal->get($slot['jam_ke']);
                return array_merge($slot, [
                    'mapel' => $pelajaran?->mataPelajaran?->nama_mapel,
                    'kode'  => $pelajaran?->mataPelajaran?->kode_mapel,
                    'guru'  => $pelajaran?->guru?->nama_lengkap,
                ]);
            })->values();

            $totalMapel = $slots->whereNull('tipe_kegiatan')->whereNotNull('mapel')->count();

            return [
                'id'         => $rombel->id,
                'kelas'      => $rombel->kelas->nama_kelas,
                'wali_kelas' => $rombel->waliKelas?->name ?? 'Belum ditentukan',
                'total_mapel'=> $totalMapel,
                'slots'      => $slots->toArray(),
            ];
        })->values()->toArray();

        // Cari kegiatan khusus yang sedang berlangsung (untuk JS)
        $activitySlotsJson = collect($todaySlots)
            ->where('is_activity', true)
            ->values()
            ->toJson();

        return view('public.smart-tv-jadwal', [
            'kelasData'    => $kelasData,
            'todaySlots'   => array_values($todaySlots),
            'todayName'    => $todayName,
            'tahunAktif'   => $tahunAktif,
            'activitySlots'=> $activitySlotsJson,
        ]);
    }

    private function isSpecialActivity(JamPelajaran $slot, string $day): bool
    {
        if (!$slot->tipe_kegiatan) return false;
        $tk = $slot->tipe_kegiatan;
        if (in_array($tk, ['istirahat', 'sholawat_pagi', 'ishoma'])) return true;
        if ($tk === 'upacara' && $day === 'Senin') return true;
        if ($tk === 'kegiatan_4r' && $day === 'Jumat') return true;
        return false;
    }
}
