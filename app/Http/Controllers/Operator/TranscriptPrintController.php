<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\MasterSiswa;
use App\Models\Rombel;
use App\Models\TranscriptConfig;
use App\Models\TranscriptSubject;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class TranscriptPrintController extends Controller
{
    public function index(Request $request)
    {
        $rombels = $this->finalRombels()->get();
        $selectedRombel = $request->filled('rombel_id')
            ? Rombel::with(['kelas', 'siswa.dapodik', 'siswa.transcriptDiplomaNumber'])->find($request->rombel_id)
            : null;

        $students = $selectedRombel
            ? $selectedRombel->siswa->sortBy('nama_lengkap')->values()
            : collect();

        return view('pages.operator.transcript.print', compact('rombels', 'selectedRombel', 'students'));
    }

    public function student(Request $request, MasterSiswa $student)
    {
        $student->load(['dapodik', 'rombels.kelas', 'transcriptDiplomaNumber', 'transcriptGrades.subject']);
        $config = TranscriptConfig::firstOrCreate([]);
        $subjects = $this->subjects()->get();

        $pdf = Pdf::loadView('pdf.transcript', [
            'config' => $config,
            'students' => collect([$student]),
            'subjects' => $subjects,
            'letterheadDataUri' => $this->dataUri($config->letterhead_path),
            'watermarkDataUri' => $this->dataUri($config->watermark_path),
            'single' => true,
        ])->setPaper($this->paper($config), 'portrait');

        return $pdf->stream('Transkrip_' . str($student->nama_lengkap)->slug('_') . '.pdf');
    }

    public function classroom(Request $request)
    {
        $data = $request->validate(['rombel_id' => 'required|exists:rombels,id']);
        $rombel = Rombel::with(['kelas', 'siswa.dapodik', 'siswa.rombels.kelas', 'siswa.transcriptDiplomaNumber', 'siswa.transcriptGrades.subject'])
            ->findOrFail($data['rombel_id']);
        $config = TranscriptConfig::firstOrCreate([]);
        $subjects = $this->subjects()->get();
        $students = $rombel->siswa->sortBy('nama_lengkap')->values();

        $pdf = Pdf::loadView('pdf.transcript', [
            'config' => $config,
            'students' => $students,
            'subjects' => $subjects,
            'letterheadDataUri' => $this->dataUri($config->letterhead_path),
            'watermarkDataUri' => $this->dataUri($config->watermark_path),
            'single' => false,
        ])->setPaper($this->paper($config), 'portrait');

        return $pdf->stream('Transkrip_' . str($rombel->kelas?->nama_kelas ?? 'kelas')->slug('_') . '.pdf');
    }

    private function finalRombels()
    {
        return Rombel::with('kelas')
            ->whereHas('kelas', function ($query) {
                $query->where('nama_kelas', 'like', '%XII%')
                    ->orWhere('nama_kelas', 'like', '%12%');
            })
            ->orderByDesc('tahun_ajaran')
            ->orderBy('kelas_id');
    }

    private function subjects()
    {
        return TranscriptSubject::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    private function paper(TranscriptConfig $config): string|array
    {
        return match ($config->paper_size ?? 'A4') {
            'F4' => [0, 0, 595.28, 935.43],
            'Letter' => 'letter',
            'Legal' => 'legal',
            default => 'a4',
        };
    }

    private function dataUri(?string $path): ?string
    {
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        $absolutePath = Storage::disk('public')->path($path);
        $mime = mime_content_type($absolutePath) ?: 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($absolutePath));
    }
}
