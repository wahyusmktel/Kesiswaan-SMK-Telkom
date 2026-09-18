<?php

namespace App\Http\Controllers\Survey;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use App\Models\User;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
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
                ->orWhere(function ($q) use ($user) {
                    $q->where('is_active', true)
                        ->whereHas('targets', function ($sq) use ($user) {
                            $sq->where('user_id', $user->id);
                        });
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

        $activeYear = TahunPelajaran::where('is_active', true)->first();

        $roles = Role::where('name', '!=', 'Siswa')->get();
        $rombels = Rombel::with(['kelas', 'siswa.user'])
            ->when($activeYear, function ($q) use ($activeYear) {
                $q->where(function ($sub) use ($activeYear) {
                    $sub->where('tahun_pelajaran_id', $activeYear->id)
                        ->orWhere(function ($fallback) use ($activeYear) {
                            $fallback->whereNull('tahun_pelajaran_id')
                                ->where('tahun_ajaran', $activeYear->tahun);
                        });
                });
            })
            ->get()
            ->sortBy('kelas.nama_kelas')
            ->values();

        $guruKelas = User::role('Guru Kelas')->get();
        $nonStudentUsers = User::whereDoesntHave('roles', function ($q) {
            $q->where('name', 'Siswa');
        })->get();

        return view('pages.surveys.create', compact('isStudent', 'roles', 'rombels', 'guruKelas', 'nonStudentUsers', 'activeYear'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'is_active' => 'required|boolean',
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
                'is_active' => $request->is_active,
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

    public function duplicate(Survey $survey)
    {
        $user = auth()->user();
        if ($survey->created_by !== $user->id && !$user->hasRole(['Super Admin', 'Operator'])) {
            abort(403, 'Anda tidak memiliki izin untuk menduplikasi survei ini.');
        }

        DB::beginTransaction();
        try {
            $newSurvey = Survey::create([
                'title' => $survey->title . ' (Salinan)',
                'description' => $survey->description,
                'created_by' => $user->id,
                'is_active' => false,
                'start_at' => null,
                'end_at' => null,
            ]);

            foreach ($survey->questions()->orderBy('order')->get() as $question) {
                $newSurvey->questions()->create([
                    'question_text' => $question->question_text,
                    'type' => $question->type,
                    'options' => $question->options,
                    'order' => $question->order,
                ]);
            }

            $targetUserIds = $survey->targets()->pluck('users.id')->toArray();
            if (!empty($targetUserIds)) {
                $newSurvey->targets()->sync($targetUserIds);
            }

            DB::commit();

            return redirect()->route('surveys.index')->with('success', 'Survei "' . $survey->title . '" berhasil diduplikasi sebagai draft.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menduplikasi survei: ' . $e->getMessage());
        }
    }

    public function edit(Survey $survey)
    {
        $user = auth()->user();
        if ($survey->created_by !== $user->id && !$user->hasRole(['Super Admin', 'Operator'])) {
            abort(403);
        }

        $responsesCount = $survey->responses()->count();
        $isStudent = $user->hasRole('Siswa');

        $activeYear = TahunPelajaran::where('is_active', true)->first();

        $roles = Role::where('name', '!=', 'Siswa')->get();
        $rombels = Rombel::with(['kelas', 'siswa.user'])
            ->when($activeYear, function ($q) use ($activeYear) {
                $q->where(function ($sub) use ($activeYear) {
                    $sub->where('tahun_pelajaran_id', $activeYear->id)
                        ->orWhere(function ($fallback) use ($activeYear) {
                            $fallback->whereNull('tahun_pelajaran_id')
                                ->where('tahun_ajaran', $activeYear->tahun);
                        });
                });
            })
            ->get()
            ->sortBy('kelas.nama_kelas')
            ->values();

        $guruKelas = User::role('Guru Kelas')->get();
        $nonStudentUsers = User::whereDoesntHave('roles', function ($q) {
            $q->where('name', 'Siswa');
        })->get();

        $survey->load([
            'questions' => function ($q) {
                $q->withCount('answers')->orderBy('order');
            },
            'targets'
        ]);

        return view('pages.surveys.edit', compact('survey', 'isStudent', 'roles', 'rombels', 'guruKelas', 'nonStudentUsers', 'activeYear', 'responsesCount'));
    }

    public function update(Request $request, Survey $survey)
    {
        $user = auth()->user();
        if ($survey->created_by !== $user->id && !$user->hasRole(['Super Admin', 'Operator'])) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'is_active' => 'required|boolean',
            'questions' => 'required|array|min:1',
            'questions.*.id' => 'nullable|integer',
            'questions.*.question_text' => 'required|string',
            'questions.*.type' => 'required|in:multiple_choice,essay',
            'questions.*.options' => 'nullable|array|max:5',
            'target_users' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            $survey->update([
                'title' => $request->title,
                'description' => $request->description,
                'start_at' => $request->start_at,
                'end_at' => $request->end_at,
                'is_active' => $request->is_active,
            ]);

            $responsesCount = $survey->responses()->count();

            if ($responsesCount === 0) {
                // No responses yet: delete and recreate questions cleanly
                $survey->questions()->delete();
                foreach ($request->questions as $index => $q) {
                    $survey->questions()->create([
                        'question_text' => $q['question_text'],
                        'type' => $q['type'],
                        'options' => $q['type'] === 'multiple_choice' ? ($q['options'] ?? []) : null,
                        'order' => $index,
                    ]);
                }
            } else {
                // Survey already has responses: update existing questions by ID to preserve answers
                $keptQuestionIds = [];
                foreach ($request->questions as $index => $q) {
                    if (!empty($q['id']) && ($existingQ = $survey->questions()->find($q['id']))) {
                        $existingQ->update([
                            'question_text' => $q['question_text'],
                            'type' => $q['type'],
                            'options' => $q['type'] === 'multiple_choice' ? ($q['options'] ?? []) : null,
                            'order' => $index,
                        ]);
                        $keptQuestionIds[] = $existingQ->id;
                    } else {
                        $newQ = $survey->questions()->create([
                            'question_text' => $q['question_text'],
                            'type' => $q['type'],
                            'options' => $q['type'] === 'multiple_choice' ? ($q['options'] ?? []) : null,
                            'order' => $index,
                        ]);
                        $keptQuestionIds[] = $newQ->id;
                    }
                }

                // Delete only questions that were removed and have NO answers
                $survey->questions()
                    ->whereNotIn('id', $keptQuestionIds)
                    ->whereDoesntHave('answers')
                    ->delete();
            }

            // Sync targets
            $survey->targets()->sync($request->target_users);

            DB::commit();
            return redirect()->route('surveys.index')->with('success', 'Survei berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memperbarui survei: ' . $e->getMessage())->withInput();
        }
    }
}
