<?php

namespace App\Http\Controllers\Kurikulum;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\JamPelajaran; // <-- Import model baru
use App\Models\TahunPelajaran;
use App\Models\MasterGuru;
use App\Models\MataPelajaran;
use App\Models\Rombel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalPelajaranController extends Controller
{
    // Method index() tidak perlu diubah
    public function index()
    {
        // 1. Cari Tahun Pelajaran yang Aktif
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $tahunAktifId = $tahunAktif ? $tahunAktif->id : null;

        // 2. Ambil Rombel hanya di tahun aktif tersebut
        // Jangan lupa eager load 'tahunPelajaran'
        $rombels = Rombel::with(['kelas', 'waliKelas', 'tahunPelajaran'])
            ->where('tahun_pelajaran_id', $tahunAktifId)
            ->get();

        return view('pages.kurikulum.jadwal-pelajaran.index', compact('rombels'));
    }

    // Method show() diubah untuk mengambil data dari database
    public function show(Rombel $rombel)
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $allJam = JamPelajaran::orderBy('jam_ke')->get();
        
        // Ambil daftar unik jam_ke untuk baris tabel
        $jamKeList = $allJam->pluck('jam_ke')->unique()->sort();
        
        // Kelompokkan slot jam untuk mempermudah pencarian (override vs default)
        $jamSlotsGrouped = $allJam->groupBy('jam_ke');
        
        // Siapkan array final jamSlots untuk dikirim ke view
        // Kita buat dummy collection yang berisi jam_ke unik agar loop di blade tetap jalan
        $jamSlots = $jamKeList->map(function($ke) {
            return (object)['jam_ke' => $ke];
        });

        // Hash table untuk pencarian cepat di blade: jamKe-Hari => JamPelajaran object
        $jamLookup = [];
        foreach ($jamSlotsGrouped as $ke => $slots) {
            $default = $slots->whereNull('hari')->first();
            foreach ($days as $day) {
                $specific = $slots->where('hari', $day)->first();
                $jamLookup["{$ke}-{$day}"] = $specific ?? $default;
            }
        }

        $mataPelajaran = MataPelajaran::where('kelas_id', $rombel->kelas_id)
            ->orderBy('nama_mapel')
            ->get();
        $guru = MasterGuru::orderBy('nama_lengkap')->get();

        $jadwal = JadwalPelajaran::where('rombel_id', $rombel->id)
            ->with(['mataPelajaran', 'guru'])
            ->get();

        // Hitung jam yang sudah dialokasikan untuk setiap mapel
        $jamTerpakai = $jadwal->groupBy('mata_pelajaran_id')->map->count();

        // Siapkan data mata pelajaran dengan sisa jam
        $mataPelajaran = $mataPelajaran->map(function ($mapel) use ($jamTerpakai) {
            $terpakai = $jamTerpakai->get($mapel->id, 0);
            $mapel->sisa_jam = $mapel->jumlah_jam - $terpakai;
            return $mapel;
        });

        $jadwalFormatted = $jadwal->keyBy(function ($item) {
            return $item->hari . '-' . $item->jam_ke;
        });

        return view('pages.kurikulum.jadwal-pelajaran.show', compact('rombel', 'days', 'jamSlots', 'mataPelajaran', 'guru', 'jadwalFormatted', 'jamLookup'));
    }

    // Method store() diubah untuk mengambil data dari database
    public function store(Request $request, Rombel $rombel)
    {
        $jadwalData = $request->input('jadwal', []);
        
        // Ambil semua jam pelajaran dan buat lookup: hari-jamKe => object
        // Hari null di-mapped ke 'default-jamKe'
        $allSlots = JamPelajaran::all();
        $slotLookup = [];
        foreach ($allSlots as $s) {
            $key = ($s->hari ?? 'default') . '-' . $s->jam_ke;
            $slotLookup[$key] = $s;
        }

        DB::beginTransaction();
        try {
            JadwalPelajaran::where('rombel_id', $rombel->id)->delete();
            foreach ($jadwalData as $hari => $jamKeList) {
                foreach ($jamKeList as $jamKe => $data) {
                    if (!empty($data['mata_pelajaran_id']) && !empty($data['master_guru_id'])) {
                        
                        // Cari slot yang spesifik hari ini, jika tidak ada pakai default
                        $slot = $slotLookup["{$hari}-{$jamKe}"] ?? $slotLookup["default-{$jamKe}"] ?? null;
                        
                        if ($slot) {
                            JadwalPelajaran::create([
                                'rombel_id' => $rombel->id,
                                'hari' => $hari,
                                'jam_ke' => $jamKe,
                                'jam_mulai' => $slot->jam_mulai,
                                'jam_selesai' => $slot->jam_selesai,
                                'mata_pelajaran_id' => $data['mata_pelajaran_id'],
                                'master_guru_id' => $data['master_guru_id'],
                            ]);
                        }
                    }
                }
            }
            DB::commit();
            toast('Jadwal pelajaran berhasil disimpan.', 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            toast('Gagal menyimpan jadwal: ' . $e->getMessage(), 'error');
        }
        return redirect()->route('kurikulum.jadwal-pelajaran.show', $rombel->id);
    }

    public function exportPdf()
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $rombels = Rombel::with(['kelas'])->get();
        $allJam = JamPelajaran::orderBy('jam_ke')->get();
        
        // Ambil daftar unik jam_ke untuk baris tabel
        $jamKeList = $allJam->pluck('jam_ke')->unique()->sort();
        
        // Kelompokkan slot jam untuk mempermudah pencarian (override vs default)
        $jamSlotsGrouped = $allJam->groupBy('jam_ke');
        
        // Hash table untuk pencarian jam: jamKe-Hari => JamPelajaran object
        $jamLookup = [];
        foreach ($jamSlotsGrouped as $ke => $slots) {
            $default = $slots->whereNull('hari')->first();
            foreach ($days as $day) {
                $specific = $slots->where('hari', $day)->first();
                $jamLookup["{$ke}-{$day}"] = $specific ?? $default;
            }
        }

        // Ambil SEMUA data jadwal
        $jadwal = JadwalPelajaran::with(['mataPelajaran', 'guru', 'rombel.kelas'])->get();
        
        // Buat matrix data: hari-jamKe-rombelId => data
        $jadwalMatrix = [];
        foreach ($jadwal as $j) {
            $jadwalMatrix["{$j->hari}-{$j->jam_ke}-{$j->rombel_id}"] = [
                'kode' => $j->mataPelajaran->kode_mapel ?? '?',
                'guru' => $j->guru->nama_lengkap ?? '?'
            ];
        }

        // Ambil daftar mapel unik untuk lampiran
        $allMapels = MataPelajaran::orderBy('kode_mapel')->get()->unique('kode_mapel');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.kurikulum.jadwal_rekap', compact(
            'days', 'rombels', 'jamKeList', 'jamLookup', 'jadwalMatrix', 'allMapels'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('Master_Jadwal_Pelajaran_' . date('Y-m-d') . '.pdf');
    }
}
