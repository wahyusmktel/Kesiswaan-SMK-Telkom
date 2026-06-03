<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Exports\LegerSiswaDetailExport;
use App\Http\Controllers\Controller;
use App\Models\MasterSiswa;
use App\Models\NilaiUjianSemester;
use App\Models\UjianSemester;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LegerNilaiController extends Controller
{
    public function index(Request $request)
    {
        $ujians = UjianSemester::withCount('nilai')->latest()->get();
        $selectedUjian = $request->filled('ujian_id')
            ? UjianSemester::with(['tahunPelajaran', 'ujianMapels'])->find($request->ujian_id)
            : $ujians->firstWhere('nilai_count', '>', 0) ?? $ujians->first();

        $kelasOptions = collect();
        $mapels = collect();
        $rows = collect();
        $analysis = $this->emptyAnalysis();

        if ($selectedUjian) {
            $kelasOptions = NilaiUjianSemester::where('ujian_semester_id', $selectedUjian->id)
                ->whereNotNull('master_siswa_id')
                ->whereNotNull('kelas')
                ->select('kelas')
                ->distinct()
                ->orderBy('kelas')
                ->pluck('kelas');

            $kelas = $request->kelas ?: $kelasOptions->first();
            $mapels = $selectedUjian->ujianMapels->sortBy('nama_mapel')->values();
            $rows = $this->buildLegerRows($selectedUjian, $kelas, $mapels);
            $analysis = $this->buildAnalysis($rows, $mapels);
        } else {
            $kelas = null;
        }

        return view('pages.kesiswaan.leger-nilai.index', compact(
            'ujians',
            'selectedUjian',
            'kelasOptions',
            'kelas',
            'mapels',
            'rows',
            'analysis'
        ));
    }

    public function show(Request $request, MasterSiswa $siswa)
    {
        $ujian = UjianSemester::with(['tahunPelajaran', 'ujianMapels'])->findOrFail($request->ujian_id);
        $detail = $this->buildStudentDetail($ujian, $siswa);

        return view('pages.kesiswaan.leger-nilai.show', compact('ujian', 'siswa', 'detail'));
    }

    public function exportStudent(Request $request, MasterSiswa $siswa)
    {
        $ujian = UjianSemester::with(['tahunPelajaran', 'ujianMapels'])->findOrFail($request->ujian_id);
        $detail = $this->buildStudentDetail($ujian, $siswa);
        $filename = 'detail-nilai-' . str($siswa->nama_lengkap)->slug() . '-' . str($ujian->nama_ujian)->slug() . '.xlsx';

        return Excel::download(new LegerSiswaDetailExport($ujian, $siswa, $detail), $filename);
    }

    public function studentPdf(Request $request, MasterSiswa $siswa)
    {
        $ujian = UjianSemester::with(['tahunPelajaran', 'ujianMapels'])->findOrFail($request->ujian_id);
        $detail = $this->buildStudentDetail($ujian, $siswa);

        $pdf = Pdf::loadView('pdf.leger-siswa-detail', compact('ujian', 'siswa', 'detail'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('detail-nilai-' . str($siswa->nama_lengkap)->slug() . '.pdf');
    }

    private function buildLegerRows(UjianSemester $ujian, ?string $kelas, $mapels)
    {
        $nilai = NilaiUjianSemester::with('siswa')
            ->where('ujian_semester_id', $ujian->id)
            ->whereNotNull('master_siswa_id')
            ->when($kelas, fn ($query) => $query->where('kelas', $kelas))
            ->get();

        return $nilai->groupBy('master_siswa_id')
            ->map(function ($items) use ($mapels, $ujian) {
                $first = $items->first();
                $scores = [];

                foreach ($mapels as $mapel) {
                    $score = $items->firstWhere('ujian_semester_mapel_id', $mapel->id);
                    $scores[$mapel->id] = $score?->nilai_akhir !== null ? (float) $score->nilai_akhir : null;
                }

                $validScores = collect($scores)->filter(fn ($score) => $score !== null);
                $average = $validScores->count() ? round($validScores->avg(), 2) : null;

                return [
                    'siswa' => $first->siswa,
                    'kelas' => $first->kelas,
                    'scores' => $scores,
                    'average' => $average,
                    'min' => $validScores->count() ? $validScores->min() : null,
                    'max' => $validScores->count() ? $validScores->max() : null,
                    'complete' => $mapels->count() > 0 && $validScores->count() === $mapels->count(),
                    'complete_count' => $validScores->count(),
                    'detail_url' => route('kesiswaan.leger-nilai.show', [
                        'siswa' => $first->master_siswa_id,
                        'ujian_id' => $ujian->id,
                    ]),
                ];
            })
            ->sortByDesc(fn ($row) => $row['average'] ?? -1)
            ->values();
    }

    private function buildAnalysis($rows, $mapels): array
    {
        $averages = $rows->pluck('average')->filter(fn ($value) => $value !== null);
        $mapelAverages = $mapels->map(function ($mapel) use ($rows) {
            $scores = $rows->pluck("scores.{$mapel->id}")->filter(fn ($value) => $value !== null);

            return [
                'id' => $mapel->id,
                'nama' => $mapel->nama_mapel,
                'average' => $scores->count() ? round($scores->avg(), 2) : null,
                'complete' => $scores->count(),
            ];
        })->values();

        $distribution = [
            '90-100' => $averages->filter(fn ($v) => $v >= 90)->count(),
            '80-89' => $averages->filter(fn ($v) => $v >= 80 && $v < 90)->count(),
            '70-79' => $averages->filter(fn ($v) => $v >= 70 && $v < 80)->count(),
            '<70' => $averages->filter(fn ($v) => $v < 70)->count(),
        ];

        return [
            'student_count' => $rows->count(),
            'class_average' => $averages->count() ? round($averages->avg(), 2) : null,
            'highest' => $averages->count() ? round($averages->max(), 2) : null,
            'lowest' => $averages->count() ? round($averages->min(), 2) : null,
            'complete_rate' => $rows->count() ? round(($rows->where('complete', true)->count() / $rows->count()) * 100, 1) : 0,
            'risk_count' => $averages->filter(fn ($v) => $v < 70)->count(),
            'top_student' => $rows->first(),
            'best_mapel' => $mapelAverages->whereNotNull('average')->sortByDesc('average')->first(),
            'weakest_mapel' => $mapelAverages->whereNotNull('average')->sortBy('average')->first(),
            'mapel_averages' => $mapelAverages,
            'distribution' => $distribution,
        ];
    }

    private function buildStudentDetail(UjianSemester $ujian, MasterSiswa $siswa): array
    {
        $nilai = NilaiUjianSemester::where('ujian_semester_id', $ujian->id)
            ->where('master_siswa_id', $siswa->id)
            ->with('ujianMapel')
            ->get()
            ->keyBy('ujian_semester_mapel_id');

        $subjects = $ujian->ujianMapels->sortBy('nama_mapel')->map(function ($mapel) use ($nilai) {
            $score = $nilai->get($mapel->id);

            return [
                'mapel' => $mapel->nama_mapel,
                'jumlah_soal' => $mapel->jumlah_soal,
                'jumlah_benar' => $score?->jumlah_benar,
                'nilai' => $score?->nilai_akhir !== null ? (float) $score->nilai_akhir : null,
                'kelas' => $score?->kelas,
            ];
        })->values();

        $validScores = $subjects->pluck('nilai')->filter(fn ($score) => $score !== null);
        $average = $validScores->count() ? round($validScores->avg(), 2) : null;

        return [
            'subjects' => $subjects,
            'average' => $average,
            'highest' => $validScores->count() ? round($validScores->max(), 2) : null,
            'lowest' => $validScores->count() ? round($validScores->min(), 2) : null,
            'complete_count' => $validScores->count(),
            'subject_count' => $subjects->count(),
            'kelas' => $subjects->firstWhere('kelas') ? $subjects->firstWhere('kelas')['kelas'] : '-',
            'recommendation' => $this->studentRecommendation($average, $validScores->count(), $subjects->count()),
        ];
    }

    private function studentRecommendation(?float $average, int $completeCount, int $subjectCount): string
    {
        if ($subjectCount === 0 || $completeCount === 0) {
            return 'Belum ada data nilai yang cukup untuk dianalisa.';
        }

        if ($completeCount < $subjectCount) {
            return 'Data nilai belum lengkap. Lengkapi import semua mata pelajaran sebelum membuat kesimpulan final.';
        }

        if ($average >= 85) {
            return 'Performa sangat kuat dan konsisten. Pertahankan ritme belajar dan dorong pengayaan pada mapel terbaik.';
        }

        if ($average >= 75) {
            return 'Performa baik. Fokuskan pendampingan pada mata pelajaran dengan nilai paling rendah agar rata-rata naik.';
        }

        return 'Perlu pendampingan akademik intensif, terutama pada mata pelajaran dengan capaian terendah.';
    }

    private function emptyAnalysis(): array
    {
        return [
            'student_count' => 0,
            'class_average' => null,
            'highest' => null,
            'lowest' => null,
            'complete_rate' => 0,
            'risk_count' => 0,
            'top_student' => null,
            'best_mapel' => null,
            'weakest_mapel' => null,
            'mapel_averages' => collect(),
            'distribution' => ['90-100' => 0, '80-89' => 0, '70-79' => 0, '<70' => 0],
        ];
    }
}
