<?php

namespace App\Http\Controllers\Survey;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SurveyExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class SurveyController extends Controller
{
    public function index()
    {
        $surveys = Survey::where('created_by', auth()->id())
            ->withCount('responses')
            ->latest()
            ->paginate(10);

        return view('pages.surveys.index', compact('surveys'));
    }

    public function create()
    {
        return view('pages.surveys.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.type' => 'required|in:multiple_choice,essay',
            'questions.*.options' => 'nullable|array|max:5',
        ]);

        DB::beginTransaction();
        try {
            $survey = Survey::create([
                'title' => $request->title,
                'description' => $request->description,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->questions as $index => $q) {
                $survey->questions()->create([
                    'question_text' => $q['question_text'],
                    'type' => $q['type'],
                    'options' => $q['type'] === 'multiple_choice' ? ($q['options'] ?? []) : null,
                    'order' => $index,
                ]);
            }

            DB::commit();
            return redirect()->route('surveys.index')->with('success', 'Survei berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan survei.')->withInput();
        }
    }

    public function show(Survey $survey)
    {
        if (!$survey->is_active) {
            return back()->with('error', 'Survei ini sudah tidak aktif.');
        }

        $survey->load('questions');
        return view('pages.surveys.show', compact('survey'));
    }

    public function submitResponse(Request $request, Survey $survey)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $response = $survey->responses()->create([
                'user_id' => auth()->id(),
            ]);

            foreach ($request->answers as $questionId => $value) {
                // For multiple choice, value might be an array if we allow multi-select, 
                // but for now it's single choice as per prompt "maksimal 5 pilihan jawaban".
                $response->answers()->create([
                    'question_id' => $questionId,
                    'answer_value' => is_array($value) ? json_encode($value) : $value,
                ]);
            }

            DB::commit();
            return redirect()->route('dashboard')->with('success', 'Terima kasih telah mengisi survei!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat mengirim jawaban.');
        }
    }

    public function results(Survey $survey)
    {
        if ($survey->created_by !== auth()->id()) {
            abort(403);
        }

        $survey->load(['questions.answers', 'responses.respondent']);

        $analysis = [];
        foreach ($survey->questions as $question) {
            if ($question->type === 'multiple_choice') {
                $answers = $question->answers->pluck('answer_value');
                $counts = $answers->countBy();

                $data = [];
                foreach ($question->options as $option) {
                    $data[$option] = $counts->get($option, 0);
                }
                $analysis[$question->id] = [
                    'labels' => array_keys($data),
                    'values' => array_values($data)
                ];
            }
        }

        return view('pages.surveys.results', compact('survey', 'analysis'));
    }

    public function destroy(Survey $survey)
    {
        if ($survey->created_by !== auth()->id()) {
            abort(403);
        }

        $survey->delete();
        return redirect()->route('surveys.index')->with('success', 'Survei berhasil dihapus.');
    }

    public function exportExcel(Survey $survey)
    {
        if ($survey->created_by !== auth()->id()) {
            abort(403);
        }

        return Excel::download(new SurveyExport($survey->id), 'hasil-survei-' . Str::slug($survey->title) . '.xlsx');
    }

    public function exportPdf(Survey $survey)
    {
        if ($survey->created_by !== auth()->id()) {
            abort(403);
        }

        $survey->load(['questions.answers', 'responses.respondent']);

        $analysis = [];
        foreach ($survey->questions as $question) {
            if ($question->type === 'multiple_choice') {
                $answers = $question->answers->pluck('answer_value');
                $counts = $answers->countBy();

                $data = [];
                foreach ($question->options as $option) {
                    $data[$option] = $counts->get($option, 0);
                }
                $analysis[$question->id] = [
                    'labels' => array_keys($data),
                    'values' => array_values($data)
                ];
            }
        }

        $pdf = Pdf::loadView('pages.surveys.pdf', compact('survey', 'analysis'));
        return $pdf->download('hasil-survei-' . Str::slug($survey->title) . '.pdf');
    }
}
