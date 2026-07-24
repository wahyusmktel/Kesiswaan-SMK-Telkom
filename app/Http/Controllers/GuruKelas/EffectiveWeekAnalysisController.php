<?php

namespace App\Http\Controllers\GuruKelas;

use App\Http\Controllers\Controller;
use App\Services\EffectiveWeekAnalysisService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EffectiveWeekAnalysisController extends Controller
{
    public function index(Request $request, EffectiveWeekAnalysisService $service)
    {
        $academicYear = $service->activeAcademicYear();
        $schedules = $academicYear
            ? $service->scheduleOptions(Auth::user(), $academicYear)
            : collect();
        $selected = $this->selectedSchedule($request, $schedules);
        $analysis = null;

        if ($academicYear && $selected) {
            $analysis = $service->analyze(
                Auth::user(),
                $academicYear,
                $selected['rombel_id'],
                $selected['mata_pelajaran_id'],
                (int) $request->integer('p5_weeks'),
                (int) $request->integer('reserve_weeks'),
            );
        }

        return view('pages.guru-kelas.effective-week.index', compact(
            'academicYear',
            'schedules',
            'selected',
            'analysis',
        ));
    }

    public function pdf(Request $request, EffectiveWeekAnalysisService $service)
    {
        $validated = $request->validate([
            'rombel_id' => ['required', 'integer'],
            'mata_pelajaran_id' => ['required', 'integer'],
            'p5_weeks' => ['nullable', 'integer', 'min:0', 'max:20'],
            'reserve_weeks' => ['nullable', 'integer', 'min:0', 'max:20'],
            'download' => ['nullable', 'boolean'],
        ]);
        $academicYear = $service->activeAcademicYear();
        abort_unless($academicYear, 404, 'Tahun pelajaran aktif tidak ditemukan.');

        $analysis = $service->analyze(
            Auth::user(),
            $academicYear,
            (int) $validated['rombel_id'],
            (int) $validated['mata_pelajaran_id'],
            (int) ($validated['p5_weeks'] ?? 0),
            (int) ($validated['reserve_weeks'] ?? 0),
        );
        $filename = 'Analisis_Pekan_Efektif_'
            .str($analysis['subject'])->slug('_')
            .'_'.str($analysis['class'])->slug('_')
            .'.pdf';
        $pdf = Pdf::loadView('pdf.effective-week-analysis', compact('analysis'))
            ->setPaper('a4', 'portrait');

        return $request->boolean('download')
            ? $pdf->download($filename)
            : $pdf->stream($filename);
    }

    private function selectedSchedule(Request $request, $schedules): ?array
    {
        if ($schedules->isEmpty()) {
            return null;
        }

        if ($request->filled(['rombel_id', 'mata_pelajaran_id'])) {
            return $schedules->first(
                fn ($item) => $item['rombel_id'] === (int) $request->rombel_id
                    && $item['mata_pelajaran_id'] === (int) $request->mata_pelajaran_id
            );
        }

        return $schedules->first();
    }
}
