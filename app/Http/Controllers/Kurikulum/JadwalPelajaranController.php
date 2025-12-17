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
        $jamSlots = JamPelajaran::orderBy('jam_ke')->get(); // <-- Ambil dari database
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

        return view('pages.kurikulum.jadwal-pelajaran.show', compact('rombel', 'days', 'jamSlots', 'mataPelajaran', 'guru', 'jadwalFormatted'));
    }

    // Method store() diubah untuk mengambil data dari database
    public function store(Request $request, Rombel $rombel)
    {
        $jadwalData = $request->input('jadwal', []);
        $jamSlots = JamPelajaran::orderBy('jam_ke')->get()->keyBy('jam_ke'); // <-- Ambil dari database

        DB::beginTransaction();
        try {
            JadwalPelajaran::where('rombel_id', $rombel->id)->delete();
            foreach ($jadwalData as $hari => $jamKeList) {
                foreach ($jamKeList as $jamKe => $data) {
                    if (!empty($data['mata_pelajaran_id']) && !empty($data['master_guru_id'])) {
                        $slot = $jamSlots->get($jamKe);
                        if ($slot) { // Pastikan slot jam ditemukan
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
}
