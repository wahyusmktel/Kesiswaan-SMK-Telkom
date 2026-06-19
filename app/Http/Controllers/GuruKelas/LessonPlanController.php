<?php

namespace App\Http\Controllers\GuruKelas;

use App\Http\Controllers\Controller;
use App\Models\LessonPlan;
use App\Models\LessonTodo;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\JadwalPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LessonPlanController extends Controller
{
    /**
     * Dashboard: tampilkan RPP hari ini & besok, plus to-do belum selesai
     */
    public function index()
    {
        $teacher   = Auth::user();
        $today     = Carbon::today();
        $tomorrow  = Carbon::tomorrow();

        $todayPlan    = LessonPlan::with(['todos', 'class', 'subject'])
                            ->where('teacher_id', $teacher->id)
                            ->whereDate('teach_date', $today)
                            ->first();

        $tomorrowPlan = LessonPlan::with(['todos', 'class', 'subject'])
                            ->where('teacher_id', $teacher->id)
                            ->whereDate('teach_date', $tomorrow)
                            ->first();

        $weekPlans    = LessonPlan::with(['class', 'subject'])
                            ->where('teacher_id', $teacher->id)
                            ->whereBetween('teach_date', [$today, $today->copy()->addDays(6)])
                            ->orderBy('teach_date')
                            ->get();

        $pendingTodos = LessonTodo::whereHas('lessonPlan', fn($q) => $q->where('teacher_id', $teacher->id))
                            ->where('is_done', 0)
                            ->with(['lessonPlan.subject'])
                            ->orderBy('due_before')
                            ->limit(10)
                            ->get();

        return view('pages.guru-kelas.lesson-plan.index', compact(
            'todayPlan', 'tomorrowPlan', 'weekPlans', 'pendingTodos'
        ));
    }

    public function create()
    {
        $user = Auth::user();
        $classes = collect();
        $subjects = collect();

        if ($user->masterGuru) {
            $jadwals = JadwalPelajaran::with(['rombel.kelas', 'mataPelajaran'])
                        ->where('master_guru_id', $user->masterGuru->id)
                        ->get();
            $classes = $jadwals->pluck('rombel.kelas')->filter()->unique('id');
            $subjects = $jadwals->pluck('mataPelajaran')->filter()->unique('id');
        }
        
        if ($classes->isEmpty()) {
            $classes = Kelas::all();
        }
        if ($subjects->isEmpty()) {
            $subjects = MataPelajaran::all();
        }

        return view('pages.guru-kelas.lesson-plan.create', compact('classes', 'subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id'            => 'required|exists:kelas,id',
            'subject_id'          => 'required|exists:mata_pelajarans,id',
            'teach_date'          => 'required|date',
            'topic'               => 'required|string|max:255',
            'learning_objectives' => 'required|string',
            'pre_assessment'      => 'nullable|string',
            'methods'             => 'nullable|array',
            'activities'          => 'nullable|array',
            'resources'           => 'nullable|array',
            'final_assessment'    => 'nullable|string',
            'differentiation'     => 'nullable|array',
            'duration_minutes'    => 'nullable|integer|min:15|max:480',
            'todos'               => 'nullable|array',
            'todos.*.text'        => 'required_with:todos|string|max:255',
            'todos.*.category'    => 'nullable|in:materi,media,administrasi,ruangan,lainnya',
            'todos.*.due_before'  => 'nullable|date',
        ]);

        // Remove todos from validated data for LessonPlan creation
        $planData = $validated;
        unset($planData['todos']);
        
        $plan = LessonPlan::create([
            ...$planData,
            'teacher_id' => Auth::id(),
            'status'     => $request->input('action') === 'publish' ? 'published' : 'draft',
        ]);

        // Simpan to-do list
        if (!empty($validated['todos'])) {
            foreach ($validated['todos'] as $i => $todo) {
                LessonTodo::create([
                    'lesson_plan_id' => $plan->id,
                    'todo_text'      => $todo['text'],
                    'category'       => $todo['category'] ?? 'lainnya',
                    'due_before'     => $todo['due_before'] ?? null,
                    'sort_order'     => $i,
                ]);
            }
        }

        // Auto-generate to-do berdasarkan metode yang dipilih
        $this->autoGenerateTodos($plan);

        return redirect()->route('guru-kelas.lesson-plan.show', $plan->id)
                         ->with('success', 'Rencana pembelajaran berhasil disimpan!');
    }

    public function show($id)
    {
        $plan = LessonPlan::with(['todos', 'class', 'subject'])
                    ->where('teacher_id', Auth::id())
                    ->findOrFail($id);
        return view('pages.guru-kelas.lesson-plan.show', compact('plan'));
    }

    public function edit($id)
    {
        $plan     = LessonPlan::where('teacher_id', Auth::id())->findOrFail($id);
        $user = Auth::user();
        $classes = collect();
        $subjects = collect();

        if ($user->masterGuru) {
            $jadwals = JadwalPelajaran::with(['rombel.kelas', 'mataPelajaran'])
                        ->where('master_guru_id', $user->masterGuru->id)
                        ->get();
            $classes = $jadwals->pluck('rombel.kelas')->filter()->unique('id');
            $subjects = $jadwals->pluck('mataPelajaran')->filter()->unique('id');
        }
        
        if ($classes->isEmpty()) {
            $classes = Kelas::all();
        }
        if ($subjects->isEmpty()) {
            $subjects = MataPelajaran::all();
        }

        return view('pages.guru-kelas.lesson-plan.edit', compact('plan', 'classes', 'subjects'));
    }

    public function update(Request $request, $id)
    {
        $plan = LessonPlan::where('teacher_id', Auth::id())->findOrFail($id);
        $validated = $request->validate([
            'class_id'            => 'required|exists:kelas,id',
            'subject_id'          => 'required|exists:mata_pelajarans,id',
            'topic'               => 'required|string|max:255',
            'learning_objectives' => 'required|string',
            'pre_assessment'      => 'nullable|string',
            'methods'             => 'nullable|array',
            'activities'          => 'nullable|array',
            'resources'           => 'nullable|array',
            'final_assessment'    => 'nullable|string',
            'differentiation'     => 'nullable|array',
            'duration_minutes'    => 'nullable|integer|min:15|max:480',
            'teach_date'          => 'required|date',
            'status'              => 'nullable|in:draft,published,done'
        ]);
        
        if ($request->has('action')) {
            $validated['status'] = $request->input('action') === 'publish' ? 'published' : 'draft';
        }

        $plan->update($validated);
        return redirect()->route('guru-kelas.lesson-plan.show', $plan->id)
                         ->with('success', 'Rencana pembelajaran diperbarui.');
    }

    public function destroy($id)
    {
        LessonPlan::where('teacher_id', Auth::id())->findOrFail($id)->delete();
        return redirect()->route('guru-kelas.lesson-plan.index')->with('success', 'RPP dihapus.');
    }

    public function calendar()
    {
        $plans = LessonPlan::with(['class', 'subject'])
                    ->where('teacher_id', Auth::id())
                    ->whereBetween('teach_date', [now()->startOfWeek(), now()->endOfWeek()->addWeek()])
                    ->orderBy('teach_date')
                    ->get();
        return view('pages.guru-kelas.lesson-plan.calendar', compact('plans'));
    }

    public function reflect(Request $request, $id)
    {
        $plan = LessonPlan::where('teacher_id', Auth::id())->findOrFail($id);
        $plan->update([
            'reflection' => $request->input('reflection'),
            'status'     => 'done',
        ]);
        return back()->with('success', 'Refleksi disimpan. Kerja bagus! 🎉');
    }

    public function toggleTodo($todoId)
    {
        $todo = LessonTodo::whereHas('lessonPlan', fn($q) => $q->where('teacher_id', Auth::id()))
                    ->findOrFail($todoId);
        $todo->update(['is_done' => !$todo->is_done]);
        return response()->json(['is_done' => $todo->is_done]);
    }

    /**
     * Auto-generate to-do list berdasarkan metode pembelajaran yang dipilih
     */
    private function autoGenerateTodos(LessonPlan $plan): void
    {
        $methods = $plan->methods ?? [];
        $autoTodos = [];

        $baseTodos = [
            ['text' => 'Siapkan absensi siswa', 'category' => 'administrasi'],
            ['text' => 'Cek kondisi proyektor/layar', 'category' => 'ruangan'],
            ['text' => 'Unggah materi ke LMS / grup kelas', 'category' => 'materi'],
        ];

        $methodTodos = [
            'PBL'              => [
                ['text' => 'Siapkan studi kasus / skenario masalah nyata', 'category' => 'materi'],
                ['text' => 'Buat lembar kerja kelompok (LKK)', 'category' => 'materi'],
                ['text' => 'Siapkan rubrik penilaian proses', 'category' => 'administrasi'],
            ],
            'PjBL'             => [
                ['text' => 'Tentukan deliverable proyek siswa', 'category' => 'materi'],
                ['text' => 'Siapkan jadwal milestone proyek', 'category' => 'administrasi'],
                ['text' => 'Cek ketersediaan alat/bahan praktik', 'category' => 'ruangan'],
            ],
            'Flipped Classroom'=> [
                ['text' => 'Upload video/materi pra-belajar ke LMS', 'category' => 'media'],
                ['text' => 'Buat kuis singkat sebelum tatap muka', 'category' => 'materi'],
            ],
            'Collaborative'    => [
                ['text' => 'Susun pembagian kelompok belajar', 'category' => 'administrasi'],
                ['text' => 'Siapkan tugas peran dalam kelompok', 'category' => 'materi'],
            ],
            'Inquiry'          => [
                ['text' => 'Siapkan pertanyaan pemantik (5-10 pertanyaan)', 'category' => 'materi'],
                ['text' => 'Sediakan sumber referensi beragam', 'category' => 'media'],
            ],
        ];

        $autoTodos = array_merge($baseTodos, ...array_map(
            fn($m) => $methodTodos[$m] ?? [],
            $methods
        ));

        foreach ($autoTodos as $i => $todo) {
            LessonTodo::firstOrCreate(
                ['lesson_plan_id' => $plan->id, 'todo_text' => $todo['text']],
                ['category' => $todo['category'], 'sort_order' => 100 + $i]
            );
        }
    }
}
