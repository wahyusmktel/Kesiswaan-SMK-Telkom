<?php

namespace App\Http\Controllers;

use App\Imports\AssessmentQuestionImport;
use App\Models\AssessmentInstrument;
use App\Models\AssessmentPeriod;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentResponse;
use App\Models\DigitalDocument;
use App\Models\JadwalPelajaran;
use App\Models\MasterGuru;
use App\Models\MasterSiswa;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Models\User;
use App\Models\UserDigitalSignature;
use App\Notifications\AssessmentAvailableNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class AssessmentController extends Controller
{
    private array $adminRoles = ['Operator', 'Super Admin', 'KAUR SDM'];

    public function index()
    {
        $user = auth()->user();
        $period = $this->activePeriod();
        $tasks = $period ? $this->tasksFor($user, $period) : collect();

        return view('pages.penilaian.index', compact('period', 'tasks'));
    }

    public function take(AssessmentInstrument $instrument, string $targetType, int $targetId)
    {
        abort_unless($instrument->period->isOpen(), 403);

        $target = $this->resolveTarget($targetType, $targetId);
        abort_unless($target && $this->isAllowedTarget(auth()->user(), $instrument, $target), 403);

        $existing = AssessmentResponse::where('assessment_instrument_id', $instrument->id)
            ->where('assessor_user_id', auth()->id())
            ->where('assessable_type', $target::class)
            ->where('assessable_id', $target->getKey())
            ->exists();

        if ($existing) {
            return redirect()->route('penilaian.index')->with('info', 'Penilaian untuk target ini sudah dikirim.');
        }

        $instrument->load('questions');
        return view('pages.penilaian.take', compact('instrument', 'target', 'targetType'));
    }

    public function submit(Request $request, AssessmentInstrument $instrument, string $targetType, int $targetId)
    {
        abort_unless($instrument->period->isOpen(), 403);

        $target = $this->resolveTarget($targetType, $targetId);
        abort_unless($target && $this->isAllowedTarget(auth()->user(), $instrument, $target), 403);

        $instrument->load('questions');
        $rules = ['answers' => ['required', 'array']];
        foreach ($instrument->questions as $question) {
            $rules["answers.{$question->id}"] = $question->answer_type === 'multiple_choice' ? ['required', 'array'] : ['required'];
        }
        $request->validate($rules);

        DB::transaction(function () use ($request, $instrument, $target) {
            $response = AssessmentResponse::create([
                'assessment_period_id' => $instrument->assessment_period_id,
                'assessment_instrument_id' => $instrument->id,
                'assessor_user_id' => auth()->id(),
                'assessable_type' => $target::class,
                'assessable_id' => $target->getKey(),
                'submitted_at' => now(),
            ]);

            $earned = 0;
            $maximum = 0;

            foreach ($instrument->questions as $question) {
                $value = $request->input("answers.{$question->id}");
                $score = $this->scoreAnswer($question, $value);

                if ($question->isScored()) {
                    $earned += $score;
                    $maximum += $question->max_score;
                }

                $response->answers()->create([
                    'assessment_question_id' => $question->id,
                    'answer_value' => is_array($value) ? array_values($value) : [$value],
                    'score' => $question->isScored() ? $score : null,
                ]);
            }

            $response->update([
                'score' => $maximum > 0 ? round(($earned / $maximum) * 100, 2) : 0,
            ]);
        });

        return redirect()->route('penilaian.index')->with('success', 'Penilaian berhasil dikirim.');
    }

    public function settings()
    {
        $this->authorizeAssessmentAdmin();

        $tahunPelajaran = TahunPelajaran::orderByDesc('is_active')->orderByDesc('id')->get();
        $periods = AssessmentPeriod::with('tahunPelajaran')->latest()->paginate(10);

        return view('pages.penilaian.settings', compact('tahunPelajaran', 'periods'));
    }

    public function storePeriod(Request $request)
    {
        $this->authorizeAssessmentAdmin();

        $data = $request->validate([
            'tahun_pelajaran_id' => ['required', 'exists:tahun_pelajaran,id'],
            'title' => ['required', 'string', 'max:255'],
            'semester' => ['required', 'string', 'max:20'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after_or_equal:start_at'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['created_by'] = auth()->id();
        AssessmentPeriod::create($data);

        return back()->with('success', 'Periode penilaian berhasil disimpan.');
    }

    public function updatePeriod(Request $request, AssessmentPeriod $period)
    {
        $this->authorizeAssessmentAdmin();

        $data = $request->validate([
            'tahun_pelajaran_id' => ['required', 'exists:tahun_pelajaran,id'],
            'title' => ['required', 'string', 'max:255'],
            'semester' => ['required', 'string', 'max:20'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after_or_equal:start_at'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $period->update($data);

        return back()->with('success', 'Periode penilaian berhasil diperbarui.');
    }

    public function instruments(Request $request)
    {
        $this->authorizeAssessmentAdmin();

        $period = $request->integer('period_id')
            ? AssessmentPeriod::findOrFail($request->integer('period_id'))
            : ($this->activePeriod() ?? AssessmentPeriod::latest()->first());
        $periods = AssessmentPeriod::with('tahunPelajaran')->latest()->get();
        $instruments = $period
            ? AssessmentInstrument::withCount('questions')->where('assessment_period_id', $period->id)->get()
            : collect();
        $types = AssessmentInstrument::TYPES;
        $answerTypes = AssessmentQuestion::ANSWER_TYPES;

        return view('pages.penilaian.instruments', compact('period', 'periods', 'instruments', 'types', 'answerTypes'));
    }

    public function storeInstrument(Request $request)
    {
        $this->authorizeAssessmentAdmin();

        $data = $request->validate([
            'assessment_period_id' => ['required', 'exists:assessment_periods,id'],
            'type' => ['required', 'string', 'in:' . implode(',', array_keys(AssessmentInstrument::TYPES))],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        AssessmentInstrument::updateOrCreate(
            ['assessment_period_id' => $data['assessment_period_id'], 'type' => $data['type']],
            $data
        );

        return back()->with('success', 'Instrumen berhasil disimpan.');
    }

    public function storeQuestion(Request $request, AssessmentInstrument $instrument)
    {
        $this->authorizeAssessmentAdmin();

        $data = $request->validate([
            'question_text' => ['required', 'string'],
            'answer_type' => ['required', 'in:' . implode(',', array_keys(AssessmentQuestion::ANSWER_TYPES))],
            'options_text' => ['nullable', 'string'],
            'max_score' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $options = match ($data['answer_type']) {
            'yes_no' => ['Ya', 'Tidak'],
            'single_choice', 'multiple_choice' => collect(preg_split('/[|;,]/', $data['options_text'] ?? ''))->map(fn ($item) => trim($item))->filter()->values()->all(),
            default => null,
        };

        $instrument->questions()->create([
            'question_text' => $data['question_text'],
            'answer_type' => $data['answer_type'],
            'options' => $options,
            'max_score' => $data['max_score'],
            'order' => (int) $instrument->questions()->max('order') + 1,
        ]);

        return back()->with('success', 'Soal instrumen berhasil ditambahkan.');
    }

    public function deleteQuestion(AssessmentQuestion $question)
    {
        $this->authorizeAssessmentAdmin();
        $question->delete();

        return back()->with('success', 'Soal instrumen berhasil dihapus.');
    }

    public function importQuestions(Request $request, AssessmentInstrument $instrument)
    {
        $this->authorizeAssessmentAdmin();
        $request->validate(['file' => ['required', 'file', 'mimes:xlsx,xls,csv']]);
        Excel::import(new AssessmentQuestionImport($instrument), $request->file('file'));

        return back()->with('success', 'Import soal instrumen berhasil diproses.');
    }

    public function notifyPeriod(AssessmentPeriod $period)
    {
        $this->authorizeAssessmentAdmin();

        $count = 0;
        User::whereHas('roles', fn ($q) => $q->whereIn('name', ['Kepala Sekolah', 'Guru Kelas', 'Siswa']))->chunk(100, function ($users) use ($period, &$count) {
            foreach ($users as $user) {
                if ($this->tasksFor($user, $period)->isNotEmpty()) {
                    $user->notify(new AssessmentAvailableNotification($period));
                    $count++;
                }
            }
        });

        return back()->with('success', "Notifikasi penilaian dikirim ke {$count} pengguna.");
    }

    public function report(Request $request)
    {
        $this->authorizeAssessmentAdmin();

        $period = $request->integer('period_id')
            ? AssessmentPeriod::findOrFail($request->integer('period_id'))
            : ($this->activePeriod() ?? AssessmentPeriod::latest()->first());
        $periods = AssessmentPeriod::with('tahunPelajaran')->latest()->get();
        $type = $request->get('type', 'all');
        $targetKind = $request->get('target_kind', 'teacher');
        $ranking = $period ? $this->ranking($period, $type, $targetKind) : collect();
        $summary = $period ? $this->summary($period) : [];

        return view('pages.penilaian.report', compact('period', 'periods', 'type', 'targetKind', 'ranking', 'summary'));
    }

    public function reportPdf(Request $request)
    {
        $this->authorizeAssessmentAdmin();

        $period = AssessmentPeriod::findOrFail($request->integer('period_id'));
        $type = $request->get('type', 'all');
        $targetKind = $request->get('target_kind', 'teacher');
        $ranking = $this->ranking($period, $type, $targetKind);
        $summary = $this->summary($period);
        $pdf = Pdf::loadView('pages.penilaian.report-pdf', compact('period', 'type', 'targetKind', 'ranking', 'summary'))->setPaper('a4', 'portrait');

        return $pdf->download('laporan-penilaian-' . Str::slug($period->title) . '.pdf');
    }

    public function certificate(Request $request, AssessmentPeriod $period, string $targetKind, int $targetId)
    {
        $this->authorizeAssessmentAdmin();

        $target = $targetKind === 'student' ? MasterSiswa::findOrFail($targetId) : MasterGuru::findOrFail($targetId);
        $ranking = $this->ranking($period, 'all', $targetKind);
        $rank = $ranking->search(fn ($row) => $row['id'] === $target->id);
        abort_if($rank === false || $rank > 4, 404);

        $signature = null;
        if (auth()->user()->hasRole('KAUR SDM')) {
            $sig = UserDigitalSignature::where('user_id', auth()->id())->first();
            if ($sig?->isReady() && $sig->auto_sign_assessment_certificate) {
                $documentType = $targetKind === 'student' ? 'ASSESSMENT_CERTIFICATE_STUDENT' : 'ASSESSMENT_CERTIFICATE_TEACHER';
                $signature = DigitalDocument::autoSign(auth()->user(), $documentType, 'Sertifikat Penilaian ' . $this->targetName($target), $period->id * 100000 + $target->id, [
                    $documentType,
                    $period->id,
                    $targetKind,
                    $target->id,
                    $ranking[$rank]['score'],
                ]);
            }
        }

        $pdf = Pdf::loadView('pages.penilaian.certificate-pdf', [
            'period' => $period,
            'target' => $target,
            'targetKind' => $targetKind,
            'rank' => $rank + 1,
            'score' => $ranking[$rank]['score'],
            'signature' => $signature,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('sertifikat-' . Str::slug($this->targetName($target)) . '.pdf');
    }

    private function activePeriod(): ?AssessmentPeriod
    {
        $activeYear = TahunPelajaran::where('is_active', true)->first();

        return AssessmentPeriod::query()
            ->when($activeYear, fn ($q) => $q->where('tahun_pelajaran_id', $activeYear->id))
            ->where('is_active', true)
            ->latest('start_at')
            ->first();
    }

    private function tasksFor(User $user, AssessmentPeriod $period)
    {
        return AssessmentInstrument::with('period')
            ->where('assessment_period_id', $period->id)
            ->where('is_active', true)
            ->get()
            ->flatMap(function (AssessmentInstrument $instrument) use ($user) {
                return $this->targetsFor($user, $instrument)->map(function (Model $target) use ($instrument, $user) {
                    $done = AssessmentResponse::where('assessment_instrument_id', $instrument->id)
                        ->where('assessor_user_id', $user->id)
                        ->where('assessable_type', $target::class)
                        ->where('assessable_id', $target->getKey())
                        ->exists();

                    return compact('instrument', 'target', 'done') + [
                        'target_type' => $this->targetType($target),
                        'target_name' => $this->targetName($target),
                    ];
                });
            });
    }

    private function targetsFor(User $user, AssessmentInstrument $instrument)
    {
        return match ($instrument->type) {
            'principal_to_teacher' => $user->hasRole('Kepala Sekolah') ? MasterGuru::with('user')->whereHas('user.roles', fn ($q) => $q->where('name', 'Guru Kelas'))->get() : collect(),
            'teacher_to_principal' => $user->hasRole('Guru Kelas') ? User::role('Kepala Sekolah')->get() : collect(),
            'teacher_to_teacher' => $user->hasRole('Guru Kelas') ? MasterGuru::with('user')->where('user_id', '!=', $user->id)->whereHas('user.roles', fn ($q) => $q->where('name', 'Guru Kelas'))->get() : collect(),
            'student_to_teacher' => $user->hasRole('Siswa') ? $this->studentTeachers($user) : collect(),
            'student_to_student' => $user->hasRole('Siswa') ? $this->studentClassmates($user) : collect(),
            default => collect(),
        };
    }

    private function studentTeachers(User $user)
    {
        $rombel = $this->activeStudentRombel($user);
        if (!$rombel) {
            return collect();
        }

        return JadwalPelajaran::with('guru.user')
            ->where('rombel_id', $rombel->id)
            ->whereHas('guru.user.roles', fn ($q) => $q->where('name', 'Guru Kelas'))
            ->get()
            ->pluck('guru')
            ->filter()
            ->unique('id')
            ->values();
    }

    private function studentClassmates(User $user)
    {
        $rombel = $this->activeStudentRombel($user);
        if (!$rombel || !$user->masterSiswa) {
            return collect();
        }

        return $rombel->siswa()->with('user')->where('master_siswa.id', '!=', $user->masterSiswa->id)->get();
    }

    private function activeStudentRombel(User $user): ?Rombel
    {
        $activeYear = TahunPelajaran::where('is_active', true)->first();

        return $user->masterSiswa?->rombels()
            ->when($activeYear, fn ($q) => $q->where('tahun_pelajaran_id', $activeYear->id))
            ->with('siswa.user')
            ->latest('rombels.id')
            ->first();
    }

    private function isAllowedTarget(User $user, AssessmentInstrument $instrument, Model $target): bool
    {
        return $this->targetsFor($user, $instrument)->contains(fn ($item) => $item::class === $target::class && $item->getKey() === $target->getKey());
    }

    private function resolveTarget(string $targetType, int $targetId): ?Model
    {
        return match ($targetType) {
            'teacher' => MasterGuru::find($targetId),
            'student' => MasterSiswa::find($targetId),
            'user' => User::find($targetId),
            default => null,
        };
    }

    private function targetType(Model $target): string
    {
        return $target instanceof MasterGuru ? 'teacher' : ($target instanceof MasterSiswa ? 'student' : 'user');
    }

    private function targetName(Model $target): string
    {
        return $target instanceof MasterGuru || $target instanceof MasterSiswa ? $target->nama_lengkap : $target->name;
    }

    private function scoreAnswer(AssessmentQuestion $question, mixed $value): float
    {
        if ($question->answer_type === 'text') {
            return 0;
        }

        if ($question->answer_type === 'yes_no') {
            return strtolower((string) $value) === 'ya' ? (float) $question->max_score : 0;
        }

        $options = array_values($question->options ?? []);
        if (count($options) === 0) {
            return 0;
        }

        $scoreFor = function ($selected) use ($options, $question) {
            $index = array_search($selected, $options, true);
            if ($index === false) {
                return 0;
            }

            return count($options) === 1 ? $question->max_score : ($index / (count($options) - 1)) * $question->max_score;
        };

        if ($question->answer_type === 'multiple_choice') {
            $values = is_array($value) ? $value : [$value];
            return collect($values)->avg(fn ($selected) => $scoreFor($selected)) ?? 0;
        }

        return $scoreFor($value);
    }

    private function ranking(AssessmentPeriod $period, string $type, string $targetKind)
    {
        $class = $targetKind === 'student' ? MasterSiswa::class : MasterGuru::class;

        return AssessmentResponse::with('assessable')
            ->where('assessment_period_id', $period->id)
            ->where('assessable_type', $class)
            ->when($type !== 'all', fn ($q) => $q->whereHas('instrument', fn ($sq) => $sq->where('type', $type)))
            ->get()
            ->groupBy('assessable_id')
            ->map(function ($rows) {
                $target = $rows->first()->assessable;
                return [
                    'id' => $target->id,
                    'name' => $this->targetName($target),
                    'score' => round($rows->avg('score'), 2),
                    'responses' => $rows->count(),
                ];
            })
            ->sortByDesc('score')
            ->values();
    }

    private function summary(AssessmentPeriod $period): array
    {
        return AssessmentInstrument::where('assessment_period_id', $period->id)
            ->withCount('responses')
            ->get()
            ->mapWithKeys(fn ($instrument) => [$instrument->type_label => [
                'responses' => $instrument->responses_count,
                'average' => round($instrument->responses()->avg('score') ?? 0, 2),
            ]])
            ->all();
    }

    private function authorizeAssessmentAdmin(): void
    {
        abort_unless(auth()->user()->hasAnyRole($this->adminRoles), 403);
    }
}
