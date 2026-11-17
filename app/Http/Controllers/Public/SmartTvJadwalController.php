<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\InfoTicker;
use App\Models\JadwalPelajaran;
use App\Models\JamPelajaran;
use App\Models\Rombel;
use App\Models\SmartTvMedia;
use App\Models\TahunPelajaran;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SmartTvJadwalController extends Controller
{
    private const DAYS = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

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

        // Kegiatan khusus untuk JS (sebagai PHP array, bukan JSON string)
        $activitySlots = collect($todaySlots)
            ->where('is_activity', true)
            ->values()
            ->toArray();

        // Running text aktif
        $tickers = InfoTicker::active()->latest()->pluck('konten')->toArray();

        // Ambil Media Tayang Terjadwal Aktif
        $now = Carbon::now();
        $allActiveMedia = SmartTvMedia::active()
            ->orderBy('urutan', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // 1. Media Eksklusif (Takeover Fullscreen) yang aktif saat jam dan hari ini
        $exclusiveMedia = $allActiveMedia
            ->filter(fn($m) => $m->mode_tayang === 'eksklusif' && $m->isScheduleActiveNow($now))
            ->first();

        // 2. Media Kombinasi (Disisipkan ke Slideshow Jadwal) yang aktif saat jam dan hari ini
        $kombinasiMedia = $allActiveMedia
            ->filter(fn($m) => $m->mode_tayang === 'kombinasi' && $m->isScheduleActiveNow($now))
            ->values();

        return view('public.smart-tv-jadwal', [
            'kelasData'      => $kelasData,
            'todaySlots'     => array_values($todaySlots),
            'todayName'      => $todayName,
            'tahunAktif'     => $tahunAktif,
            'activitySlots'  => $activitySlots,
            'tickers'        => $tickers,
            'exclusiveMedia' => $exclusiveMedia,
            'kombinasiMedia' => $kombinasiMedia,
            'allActiveMedia' => $allActiveMedia,
        ]);
    }

    /**
     * Endpoint JSON polling status realtime untuk Smart TV.
     * TV memanggil ini tiap 15-30 detik untuk mendeteksi perubahan jadwal/media/running text tanpa reload browser.
     */
    public function status(Request $request)
    {
        $now = Carbon::now();
        $dayMap = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
        $todayName = $dayMap[$now->dayOfWeek] ?? 'Senin';

        $allActiveMedia = SmartTvMedia::active()
            ->orderBy('urutan', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $exclusive = $allActiveMedia
            ->filter(fn($m) => $m->mode_tayang === 'eksklusif' && $m->isScheduleActiveNow($now))
            ->first();

        $kombinasi = $allActiveMedia
            ->filter(fn($m) => $m->mode_tayang === 'kombinasi' && $m->isScheduleActiveNow($now))
            ->values()
            ->map(function ($item) {
                return [
                    'id'           => $item->id,
                    'judul'        => $item->judul,
                    'tipe'         => $item->tipe,
                    'tipe_label'   => $item->tipe_label,
                    'file_url'     => $item->file_url,
                    'file_type'    => $item->file_type,
                    'durasi_detik' => $item->durasi_detik ?: 20,
                    'keterangan'   => $item->keterangan,
                ];
            });

        $tickers = InfoTicker::active()->latest()->pluck('konten')->toArray();

        $payload = [
            'server_time'      => $now->format('H:i:s'),
            'server_date'      => $now->translatedFormat('l, d F Y'),
            'today_name'       => $todayName,
            'is_exclusive'     => (bool) $exclusive,
            'exclusive_media'  => $exclusive ? [
                'id'           => $exclusive->id,
                'judul'        => $exclusive->judul,
                'tipe'         => $exclusive->tipe,
                'tipe_label'   => $exclusive->tipe_label,
                'file_url'     => $exclusive->file_url,
                'file_type'    => $exclusive->file_type,
                'jam_mulai'    => $exclusive->jam_mulai_formatted,
                'jam_selesai'  => $exclusive->jam_selesai_formatted,
                'keterangan'   => $exclusive->keterangan,
            ] : null,
            'kombinasi_media'  => $kombinasi,
            'tickers'          => $tickers,
        ];

        // Hash untuk perbandingan perubahan secara instan
        $payload['hash'] = md5(json_encode([
            $payload['is_exclusive'],
            $payload['exclusive_media']['id'] ?? null,
            $payload['exclusive_media']['file_url'] ?? null,
            $kombinasi->pluck('id')->toArray(),
            $tickers,
        ]));

        return response()->json($payload);
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
