<?php

namespace App\Http\Controllers\Operator;

use App\Exports\TranscriptGradeTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\TranscriptGradeImport;
use App\Models\Rombel;
use App\Models\TranscriptSubject;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TranscriptGradeController extends Controller
{
    public function index(Request $request)
    {
        $rombels = $this->finalRombels()->get();
        $subjects = $this->subjects()->get();
        $selectedRombel = $request->filled('rombel_id')
            ? Rombel::with(['kelas', 'siswa.dapodik', 'siswa.transcriptGrades'])->find($request->rombel_id)
            : null;

        $students = collect();

        if ($selectedRombel) {
            $students = $selectedRombel->siswa
                ->load(['dapodik', 'transcriptGrades'])
                ->sortBy('nama_lengkap')
                ->values();
        }

        return view('pages.operator.transcript.grades', compact('rombels', 'subjects', 'selectedRombel', 'students'));
    }

    public function template(Request $request)
    {
        $data = $request->validate(['rombel_id' => 'required|exists:rombels,id']);
        $rombel = Rombel::with(['kelas', 'siswa.dapodik', 'siswa.rombels.kelas', 'siswa.transcriptGrades'])->findOrFail($data['rombel_id']);
        $subjects = $this->subjects()->get();
        $students = $rombel->siswa->sortBy('nama_lengkap')->values();
        $kelas = str($rombel->kelas?->nama_kelas ?? 'kelas')->slug('_');

        return Excel::download(new TranscriptGradeTemplateExport($students, $subjects), "template_nilai_transkrip_{$kelas}.xlsx");
    }

    public function import(Request $request)
    {
        $data = $request->validate([
            'rombel_id' => 'required|exists:rombels,id',
            'file_import' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $rombel = Rombel::with(['siswa.dapodik'])->findOrFail($data['rombel_id']);
        $subjects = $this->subjects()->get();
        $import = new TranscriptGradeImport($rombel->siswa, $subjects);

        Excel::import($import, $request->file('file_import'));
        toast('Import nilai transkrip selesai.', 'success');

        return redirect()
            ->route('operator.transcript.grades.index', ['rombel_id' => $rombel->id])
            ->with('import_report', $import->summary());
    }

    private function subjects()
    {
        return TranscriptSubject::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->orderBy('name');
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
}
