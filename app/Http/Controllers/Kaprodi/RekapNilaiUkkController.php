<?php

namespace App\Http\Controllers\Kaprodi;

use App\Exports\RekapNilaiUkkExport;
use App\Http\Controllers\Controller;
use App\Models\UkkNilaiKeterampilan;
use App\Models\UkkNilaiPengetahuan;
use App\Models\UkkUjian;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RekapNilaiUkkController extends Controller
{
    public function index(Request $request)
    {
        $ujians = UkkUjian::with('tahunPelajaran')->latest()->get();

        $selectedUjianId  = $request->input('ujian_id', $ujians->first()?->id);
        $selectedRombelId = $request->input('rombel_id');

        $ujian  = $ujians->firstWhere('id', $selectedUjianId);
        $rombels = collect();
        $rekap   = collect();

        if ($ujian) {
            $ujian->load([
                'rombels.kelas',
                'rombels.siswa',
                'instrumens.soalPengetahuan',
                'instrumens.kategoriKeterampilan.indikator',
            ]);

            $rombels = $ujian->rombels->sortBy(fn ($r) => $r->kelas->nama_kelas ?? '');

            // Build siswa list with rombel label
            $siswaMap = collect();
            foreach ($rombels as $rombel) {
                if ($selectedRombelId && $rombel->id != $selectedRombelId) continue;
                $label = $rombel->kelas->nama_kelas ?? ('Rombel ' . $rombel->id);
                foreach ($rombel->siswa as $s) {
                    if (!$siswaMap->has($s->id)) {
                        $s->rombel_label = $label;
                        $siswaMap->put($s->id, $s);
                    }
                }
            }
            $siswas  = $siswaMap->values();
            $siswaIds = $siswas->pluck('id');

            // All soal & indikator IDs for this ujian
            $allSoalIds = $ujian->instrumens
                ->flatMap(fn ($i) => $i->soalPengetahuan->pluck('id'));
            $allIndIds  = $ujian->instrumens
                ->flatMap(fn ($i) => $i->kategoriKeterampilan->flatMap(fn ($k) => $k->indikator->pluck('id')));

            // Bulk load nilai
            $allNilaiP = UkkNilaiPengetahuan::whereIn('master_siswa_id', $siswaIds)
                ->whereIn('soal_id', $allSoalIds)
                ->get()
                ->groupBy('master_siswa_id');

            $allNilaiK = UkkNilaiKeterampilan::whereIn('master_siswa_id', $siswaIds)
                ->whereIn('indikator_id', $allIndIds)
                ->get()
                ->groupBy('master_siswa_id');

            $rekap = $siswas->map(function ($siswa) use ($ujian, $allNilaiP, $allNilaiK) {
                return $this->computeSiswaRekap($siswa, $ujian, $allNilaiP, $allNilaiK);
            })->sortBy('siswa.nama_lengkap')->values();
        }

        return view('pages.kaprodi.rekap-ukk.index', compact(
            'ujians', 'ujian', 'rombels',
            'selectedUjianId', 'selectedRombelId',
            'rekap'
        ));
    }

    public function export(Request $request)
    {
        $ujianId  = $request->input('ujian_id');
        $rombelId = $request->input('rombel_id');

        $ujian = UkkUjian::with([
            'tahunPelajaran',
            'rombels.kelas',
            'rombels.siswa',
            'instrumens.soalPengetahuan',
            'instrumens.kategoriKeterampilan.indikator',
        ])->findOrFail($ujianId);

        $rombels = $ujian->rombels->sortBy(fn ($r) => $r->kelas->nama_kelas ?? '');

        $siswaMap = collect();
        foreach ($rombels as $rombel) {
            if ($rombelId && $rombel->id != $rombelId) continue;
            $label = $rombel->kelas->nama_kelas ?? ('Rombel ' . $rombel->id);
            foreach ($rombel->siswa as $s) {
                if (!$siswaMap->has($s->id)) {
                    $s->rombel_label = $label;
                    $siswaMap->put($s->id, $s);
                }
            }
        }
        $siswas   = $siswaMap->values();
        $siswaIds = $siswas->pluck('id');

        $allSoalIds = $ujian->instrumens->flatMap(fn ($i) => $i->soalPengetahuan->pluck('id'));
        $allIndIds  = $ujian->instrumens->flatMap(fn ($i) => $i->kategoriKeterampilan->flatMap(fn ($k) => $k->indikator->pluck('id')));

        $allNilaiP = UkkNilaiPengetahuan::whereIn('master_siswa_id', $siswaIds)
            ->whereIn('soal_id', $allSoalIds)
            ->get()->groupBy('master_siswa_id');

        $allNilaiK = UkkNilaiKeterampilan::whereIn('master_siswa_id', $siswaIds)
            ->whereIn('indikator_id', $allIndIds)
            ->get()->groupBy('master_siswa_id');

        $rekap = $siswas->map(fn ($s) => $this->computeSiswaRekap($s, $ujian, $allNilaiP, $allNilaiK))
            ->sortBy('siswa.nama_lengkap')
            ->values();

        $suffix   = $rombelId ? ('_' . ($rombels->firstWhere('id', $rombelId)?->kelas->nama_kelas ?? $rombelId)) : '_Semua';
        $filename = 'Rekap_UKK_' . str_replace(' ', '_', $ujian->nama_ujian) . $suffix . '.xlsx';

        return Excel::download(new RekapNilaiUkkExport($ujian, $rekap), $filename);
    }

    private function computeSiswaRekap($siswa, $ujian, $allNilaiP, $allNilaiK): array
    {
        $nilaiP = $allNilaiP->get($siswa->id, collect())->pluck('nilai', 'soal_id');
        $nilaiK = $allNilaiK->get($siswa->id, collect())->pluck('nilai', 'indikator_id');

        $instrumenScores = [];
        $totalSoalAll    = 0;
        $totalGradedP    = 0;
        $totalIndAll     = 0;
        $totalGradedK    = 0;

        foreach ($ujian->instrumens as $ins) {
            $soalIds   = $ins->soalPengetahuan->pluck('id');
            $totalSoal = $soalIds->count();
            $benar     = $soalIds->filter(fn ($id) => ($nilaiP[$id] ?? null) == 1)->count();
            $gradedP   = $soalIds->filter(fn ($id) => $nilaiP->has($id))->count();
            $skorP     = $totalSoal > 0 ? round($benar / $totalSoal * 100, 1) : null;

            $indIds   = $ins->kategoriKeterampilan->flatMap(fn ($k) => $k->indikator->pluck('id'));
            $totalInd = $indIds->count();
            $gradedK  = $indIds->filter(fn ($id) => $nilaiK->has($id))->count();

            $skorK = null;
            if ($totalInd > 0) {
                $kScore = 0;
                foreach ($ins->kategoriKeterampilan as $kat) {
                    $katIds = $kat->indikator->pluck('id');
                    if ($katIds->isEmpty()) continue;
                    $avg     = $katIds->sum(fn ($id) => ($nilaiK[$id] ?? 0)) / $katIds->count();
                    $kScore += ($avg / 3) * 100 * ($kat->bobot / 100);
                }
                $skorK = round($kScore, 1);
            }

            $bp      = $ins->bobot_pengetahuan / 100;
            $nilaiIns = null;
            if ($totalSoal > 0 && $totalInd > 0) {
                $nilaiIns = round(($skorP ?? 0) * $bp + ($skorK ?? 0) * (1 - $bp), 1);
            } elseif ($totalSoal > 0) {
                $nilaiIns = $skorP;
            } elseif ($totalInd > 0) {
                $nilaiIns = $skorK;
            }

            $totalSoalAll += $totalSoal;
            $totalGradedP += $gradedP;
            $totalIndAll  += $totalInd;
            $totalGradedK += $gradedK;

            $instrumenScores[] = [
                'nama'   => $ins->nama_instrumen,
                'skor_p' => $skorP,
                'skor_k' => $skorK,
                'nilai'  => $nilaiIns,
            ];
        }

        $isComplete = ($totalSoalAll === 0 || $totalGradedP >= $totalSoalAll)
                   && ($totalIndAll === 0  || $totalGradedK >= $totalIndAll);

        $validNilais = collect($instrumenScores)->pluck('nilai')->filter(fn ($v) => $v !== null);
        $nilaiAkhir  = $validNilais->isNotEmpty() ? round($validNilais->avg(), 1) : null;

        return [
            'siswa'            => $siswa,
            'rombel_label'     => $siswa->rombel_label ?? '-',
            'instrumen_scores' => $instrumenScores,
            'nilai_akhir'      => $nilaiAkhir,
            'is_complete'      => $isComplete,
        ];
    }
}
