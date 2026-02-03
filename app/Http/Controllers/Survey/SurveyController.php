<?php

namespace App\Http\Controllers\Survey;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use App\Models\User;
use App\Models\Rombel;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SurveyExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class SurveyController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $surveys = Survey::where(function ($query) use ($user) {
            $query->where('created_by', $user->id)
                ->orWhereHas('targets', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
        })
            ->withCount('responses')
            ->with([
                'responses' => function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                }
            ])
            ->latest()
            ->paginate(10);

        return view('pages.surveys.index', compact('surveys'));
    }

    public function create()
    {
        $user = auth()->user();
        $isStudent = $user->hasRole('Siswa');

        $roles = Role::where('name', '!=', 'Siswa')->get();
        $rombels = Rombel::with(['kelas', 'siswa.user'])->get();
        $guruKelas = User::role('Guru Kelas')->get();
        $nonStudentUsers = User::whereDoesntHave('roles', function ($q) {
            $q->where('name', 'Siswa');
        })->get();

        return view('pages.surveys.create', compact('isStudent', 'roles', 'rombels', 'guruKelas', 'nonStudentUsers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.type' => 'required|in:multiple_choice,essay',
            'questions.*.options' => 'nullable|array|max:5',
            'target_users' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            $survey = Survey::create([
                'title' => $request->title,
                'description' => $request->description,
                'start_at' => $request->start_at,
                'end_at' => $request->end_at,
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

            // Sync targets
            $survey->targets()->sync($request->target_users);

            DB::commit();
            return redirect()->route('surveys.index')->with('success', 'Survei berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan survei.')->withInput();
        }
    }

    public function show(Survey $survey)
    {
        $user = auth()->user();

        // Security checks
        if (!$survey->isOpen()) {
            $status = $survey->schedule_status;
            if ($status === 'upcoming') {
                return back()->with('info', 'Survei ini belum dimulai. Silakan kembali lagi nanti.');
            }
            if ($status === 'expired') {
                return back()->with('error', 'Survei ini sudah berakhir.');
            }
            return back()->with('error', 'Survei ini sudah tidak aktif.');
        }

        if ($survey->created_by !== $user->id && !$survey->targets()->where('user_id', $user->id)->exists()) {
            abort(403, 'Anda tidak terdaftar sebagai responden survei ini.');
        }

        if ($survey->responses()->where('user_id', $user->id)->exists()) {
            return redirect()->route('surveys.index')->with('info', 'Anda sudah mengisi survei ini.');
        }

        $survey->load('questions');
        return view('pages.surveys.show', compact('survey'));
    }

    public function submitResponse(Request $request, Survey $survey)
    {
        $user = auth()->user();

        // Security checks
        if (!$survey->isOpen()) {
            abort(403, 'Survei tidak sedang berlangsung.');
        }

        if ($survey->responses()->where('user_id', $user->id)->exists()) {
            return redirect()->route('surveys.index')->with('error', 'Anda sudah mengisi survei ini.');
        }

        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $response = $survey->responses()->create([
                'user_id' => $user->id,
            ]);

            foreach ($request->answers as $questionId => $value) {
                $response->answers()->create([
                    'question_id' => $questionId,
                    'answer_value' => is_array($value) ? json_encode($value) : $value,
                ]);
            }

            DB::commit();
            return redirect()->route('surveys.index')->with('success', 'Terima kasih telah mengisi survei!');
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
