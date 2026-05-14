<?php

namespace App\Http\Controllers\GuruKelas;

use App\Http\Controllers\Controller;
use App\Models\MasterSiswa;
use App\Models\UkkInstrumenIndikator;
use App\Models\UkkInstrumenSoal;
use App\Models\UkkNilaiKeterampilan;
use App\Models\UkkNilaiPengetahuan;
use App\Models\UkkUjian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenilaianUkkController extends Controller
{
    private function authorizeAsPenguji(UkkUjian $ujian): void
    {
        abort_unless(
            $ujian->penguji()->where('user_id', Auth::id())->exists(),
            403,
            'Anda tidak terdaftar sebagai penguji untuk ujian ini.'
        );
    }

    public function index()
    {
        $ujians = UkkUjian::with(['tahunPelajaran', 'rombels', 'instrumens'])
            ->whereHas('penguji', fn ($q) => $q->where('user_id', Auth::id()))
            ->latest()
            ->get();

        foreach ($ujians as $ujian) {
            $siswaIds = $ujian->rombels
                ->flatMap(fn ($r) => $r->siswa->pluck('id'))
                ->unique();

            $ujian->total_siswa = $siswaIds->count();

            $soalIds = $ujian->instrumens
                ->flatMap(fn ($i) => $i->soalPengetahuan->pluck('id'));

            $ujian->sudah_dinilai = $soalIds->isEmpty() ? 0
                : UkkNilaiPengetahuan::whereIn('soal_id', $soalIds)
                    ->whereIn('master_siswa_id', $siswaIds)
                    ->distinct('master_siswa_id')
                    ->count('master_siswa_id');
        }

        return view('pages.guru-kelas.ukk.index', compact('ujians'));
    }

    public function show(UkkUjian $ujian)
    {
        $this->authorizeAsPenguji($ujian);

        $ujian->load([
            'tahunPelajaran',
            'rombels.siswa',
            'instrumens.soalPengetahuan',
            'instrumens.kategoriKeterampilan.indikator',
        ]);

        $siswas = $ujian->rombels
            ->flatMap(fn ($r) => $r->siswa)
            ->unique('id')
            ->sortBy('nama_lengkap')
            ->values();

        $allSoalIds      = $ujian->instrumens->flatMap(fn ($i) => $i->soalPengetahuan->pluck('id'));
        $allIndikatorIds = $ujian->instrumens->flatMap(fn ($i) =>
            $i->kategoriKeterampilan->flatMap(fn ($k) => $k->indikator->pluck('id'))
        );

        $totalSoal      = $allSoalIds->count();
        $totalIndikator = $allIndikatorIds->count();

        $siswaIdList = $siswas->pluck('id');

        $gradedP = UkkNilaiPengetahuan::whereIn('soal_id', $allSoalIds)
            ->whereIn('master_siswa_id', $siswaIdList)
            ->selectRaw('master_siswa_id, count(*) as cnt')
            ->groupBy('master_siswa_id')
            ->pluck('cnt', 'master_siswa_id');

        $gradedK = UkkNilaiKeterampilan::whereIn('indikator_id', $allIndikatorIds)
            ->whereIn('master_siswa_id', $siswaIdList)
            ->selectRaw('master_siswa_id, count(*) as cnt')
            ->groupBy('master_siswa_id')
            ->pluck('cnt', 'master_siswa_id');

        return view('pages.guru-kelas.ukk.show', compact(
            'ujian', 'siswas', 'totalSoal', 'totalIndikator', 'gradedP', 'gradedK'
        ));
    }

    public function penilaian(UkkUjian $ujian, MasterSiswa $siswa)
    {
        $this->authorizeAsPenguji($ujian);

        $ujian->load([
            'instrumens.soalPengetahuan',
            'instrumens.kategoriKeterampilan.indikator',
        ]);

        $allSoalIds      = $ujian->instrumens->flatMap(fn ($i) => $i->soalPengetahuan->pluck('id'));
        $allIndikatorIds = $ujian->instrumens->flatMap(fn ($i) =>
            $i->kategoriKeterampilan->flatMap(fn ($k) => $k->indikator->pluck('id'))
        );

        $nilaiP = UkkNilaiPengetahuan::where('master_siswa_id', $siswa->id)
            ->whereIn('soal_id', $allSoalIds)
            ->pluck('nilai', 'soal_id');

        $nilaiK = UkkNilaiKeterampilan::where('master_siswa_id', $siswa->id)
            ->whereIn('indikator_id', $allIndikatorIds)
            ->pluck('nilai', 'indikator_id');

        return view('pages.guru-kelas.ukk.penilaian', compact(
            'ujian', 'siswa', 'nilaiP', 'nilaiK'
        ));
    }

    public function simpan(Request $request, UkkUjian $ujian, MasterSiswa $siswa)
    {
        $this->authorizeAsPenguji($ujian);

        $request->validate([
            'pengetahuan'               => 'nullable|array',
            'pengetahuan.*'             => 'integer|in:0,1',
            'keterampilan'              => 'nullable|array',
            'keterampilan.*'            => 'integer|in:0,1,2,3',
        ]);

        // Verify soal & indikator belong to this ujian
        $instrumenIds    = $ujian->instrumens()->pluck('id');
        $validSoalIds    = UkkInstrumenSoal::whereIn('instrumen_id', $instrumenIds)->pluck('id');
        $validIndIds     = UkkInstrumenIndikator::whereIn('kategori_id',
            DB::table('ukk_instrumen_kategoris')->whereIn('instrumen_id', $instrumenIds)->pluck('id')
        )->pluck('id');

        DB::transaction(function () use ($request, $siswa, $validSoalIds, $validIndIds) {
            foreach ($request->input('pengetahuan', []) as $soalId => $nilai) {
                if (!$validSoalIds->contains((int) $soalId)) continue;
                UkkNilaiPengetahuan::updateOrCreate(
                    ['master_siswa_id' => $siswa->id, 'soal_id' => (int) $soalId],
                    ['nilai' => (int) $nilai, 'penguji_id' => Auth::id()]
                );
            }
            foreach ($request->input('keterampilan', []) as $indId => $nilai) {
                if (!$validIndIds->contains((int) $indId)) continue;
                UkkNilaiKeterampilan::updateOrCreate(
                    ['master_siswa_id' => $siswa->id, 'indikator_id' => (int) $indId],
                    ['nilai' => (int) $nilai, 'penguji_id' => Auth::id()]
                );
            }
        });

        return response()->json(['message' => 'Penilaian berhasil disimpan.']);
    }
}
