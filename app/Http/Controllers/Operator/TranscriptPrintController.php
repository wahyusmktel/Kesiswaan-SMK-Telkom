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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            'transcriptNumbers' => $this->transcriptNumbers(collect([$student]), $config),
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
            'transcriptNumbers' => $this->transcriptNumbers($students, $config),
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

    private function transcriptNumbers(Collection $students, TranscriptConfig $config): array
    {
        $start = $config->number_start ?: '400.3.11/800.01';
        $suffix = $config->number_suffix ?: '/SMKTEL-LPG/KURL.03/V/2026';
        $parsed = $this->parseRunningNumber($start);

        $orderedIds = $this->finalStudentsOrder()->pluck('id')->values();
        $targetIds = $students->pluck('id')->all();
        $numbers = [];

        foreach ($targetIds as $studentId) {
            $position = $orderedIds->search($studentId);
            $offset = $position === false ? 0 : $position;
            $numbers[$studentId] = $parsed['prefix'] . str_pad((string) ($parsed['number'] + $offset), $parsed['width'], '0', STR_PAD_LEFT) . $suffix;
        }

        return $numbers;
    }

    private function parseRunningNumber(string $start): array
    {
        if (preg_match('/^(.*?)(\d+)$/', $start, $matches)) {
            return [
                'prefix' => $matches[1],
                'number' => (int) $matches[2],
                'width' => strlen($matches[2]),
            ];
        }

        return [
            'prefix' => Str::finish($start, '.'),
            'number' => 1,
            'width' => 2,
        ];
    }

    private function finalStudentsOrder(): Collection
    {
        return MasterSiswa::select('master_siswa.id', DB::raw('MIN(rombels.kelas_id) as sort_kelas'), DB::raw('MIN(master_siswa.nama_lengkap) as sort_name'))
            ->join('rombel_siswa', 'master_siswa.id', '=', 'rombel_siswa.master_siswa_id')
            ->join('rombels', 'rombels.id', '=', 'rombel_siswa.rombel_id')
            ->join('kelas', 'kelas.id', '=', 'rombels.kelas_id')
            ->where(function ($query) {
                $query->where('kelas.nama_kelas', 'like', '%XII%')
                    ->orWhere('kelas.nama_kelas', 'like', '%12%');
            })
            ->groupBy('master_siswa.id')
            ->orderBy('sort_kelas')
            ->orderBy('sort_name')
            ->get();
    }
}
