# Prompt: Fitur Rencana Pembelajaran Guru SMK (Modern Teaching Plan)

> **Untuk:** GitHub Copilot / Codex / AI Agent  
> **Konteks:** Tambahkan fitur **Rencana Pembelajaran** pada halaman role **Guru Kelas** di aplikasi Laravel Sisfo yang sudah berjalan.  
> **Pendekatan:** Mengadopsi gaya mengajar modern ala Finlandia & Kanada — berpusat pada siswa, berbasis kompetensi, reflektif, dan kolaboratif.

---

## 1. GAMBARAN FITUR

Guru SMK perlu tahu **besok mau mengajar apa** dan **apa yang harus disiapkan**. Fitur ini menyediakan:

- **Dashboard Rencana Harian** — tampilan "Besok Saya Mengajar" dengan ringkasan materi, metode, dan to-do list
- **Penyusunan RPP Modern** — alur wizard langkah demi langkah mengikuti prinsip Finlandia/Kanada
- **To-Do List Persiapan** — checklist otomatis berdasarkan jenis pembelajaran yang dipilih
- **Kalender Rencana Mingguan** — overview 7 hari ke depan
- **Refleksi Setelah Mengajar** — catatan refleksi post-lesson (ciri khas Finlandia)

---

## 2. ALUR PENYUSUNAN RENCANA PEMBELAJARAN (MODERN)

Ikuti alur ini saat membangun wizard/form penyusunan RPP:

```
TAHAP 1 — TUJUAN BELAJAR (Learning Outcomes)
  └─ Guru menjawab: "Setelah pelajaran ini, siswa MAMPU melakukan apa?"
  └─ Format: Kata kerja operasional + konteks nyata (bukan hafalan)
  └─ Contoh: "Siswa mampu menganalisis kesalahan kode PHP dan memperbaikinya secara mandiri"

TAHAP 2 — ASESMEN AWAL (Pre-Assessment)
  └─ Guru memilih: Apa yang sudah diketahui siswa sebelum materi ini?
  └─ Metode: kuis singkat / pertanyaan pemantik / mind-map awal

TAHAP 3 — AKTIVITAS PEMBELAJARAN (Learning Activities)
  └─ Pilih metode utama (multi-select):
      [ ] Problem-Based Learning (PBL)
      [ ] Project-Based Learning (PjBL)
      [ ] Collaborative Learning
      [ ] Inquiry-Based Learning
      [ ] Flipped Classroom
      [ ] Direct Instruction (ceramah singkat ≤15 menit)
  └─ Susun urutan: Pembuka → Eksplorasi → Elaborasi → Konfirmasi → Penutup

TAHAP 4 — BAHAN & MEDIA
  └─ Upload/link modul, video, slide
  └─ Centang kebutuhan: Lab, Proyektor, Internet, Alat Praktik, dll.

TAHAP 5 — ASESMEN AKHIR (Formative Assessment)
  └─ Bagaimana guru tahu siswa sudah paham?
  └─ Pilihan: Unjuk kerja, kuis, portofolio, presentasi kelompok, exit ticket

TAHAP 6 — DIFERENSIASI (Inclusivity)
  └─ Rencana untuk siswa yang sudah mahir (pengayaan)
  └─ Rencana untuk siswa yang tertinggal (remedial/scaffolding)

TAHAP 7 — REFLEKSI (Post-Lesson, diisi setelah mengajar)
  └─ Apa yang berjalan baik?
  └─ Apa yang perlu diperbaiki?
  └─ Tindak lanjut untuk pertemuan berikutnya
```

---

## 3. STRUKTUR DATABASE

Buat migration berikut (jika belum ada):

```sql
-- Tabel rencana pembelajaran
CREATE TABLE lesson_plans (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    teacher_id      BIGINT UNSIGNED NOT NULL,         -- FK ke users
    class_id        BIGINT UNSIGNED NOT NULL,         -- FK ke kelas
    subject_id      BIGINT UNSIGNED NOT NULL,         -- FK ke mata pelajaran
    teach_date      DATE NOT NULL,                    -- Tanggal mengajar
    topic           VARCHAR(255) NOT NULL,            -- Topik/materi
    learning_objectives TEXT NOT NULL,               -- Tujuan pembelajaran
    pre_assessment  TEXT NULL,                        -- Asesmen awal
    methods         JSON NULL,                        -- Metode mengajar (array)
    activities      JSON NULL,                        -- Langkah aktivitas
    resources       JSON NULL,                        -- Bahan & media
    final_assessment TEXT NULL,                       -- Asesmen akhir
    differentiation JSON NULL,                        -- Diferensiasi
    duration_minutes INT DEFAULT 90,                  -- Durasi pelajaran
    status          ENUM('draft','published','done') DEFAULT 'draft',
    reflection      TEXT NULL,                        -- Diisi setelah mengajar
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id)   REFERENCES classes(id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id)
);

-- Tabel to-do list persiapan
CREATE TABLE lesson_todos (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lesson_plan_id  BIGINT UNSIGNED NOT NULL,
    todo_text       VARCHAR(255) NOT NULL,
    category        ENUM('materi','media','administrasi','ruangan','lainnya') DEFAULT 'lainnya',
    is_done         TINYINT(1) DEFAULT 0,
    due_before      DATETIME NULL,                    -- Deadline persiapan
    sort_order      INT DEFAULT 0,
    FOREIGN KEY (lesson_plan_id) REFERENCES lesson_plans(id) ON DELETE CASCADE
);
```

---

## 4. ROUTES

Tambahkan di `routes/web.php` dalam group middleware `auth` + role `guru`:

```php
Route::prefix('guru/rencana-pembelajaran')->name('guru.lesson-plan.')->middleware(['auth', 'role:guru'])->group(function () {
    Route::get('/',              [LessonPlanController::class, 'index'])   ->name('index');      // Dashboard harian
    Route::get('/buat',          [LessonPlanController::class, 'create'])  ->name('create');     // Wizard buat RPP
    Route::post('/',             [LessonPlanController::class, 'store'])   ->name('store');
    Route::get('/{id}',          [LessonPlanController::class, 'show'])    ->name('show');       // Detail RPP
    Route::get('/{id}/edit',     [LessonPlanController::class, 'edit'])    ->name('edit');
    Route::put('/{id}',          [LessonPlanController::class, 'update'])  ->name('update');
    Route::delete('/{id}',       [LessonPlanController::class, 'destroy']) ->name('destroy');
    Route::get('/kalender',      [LessonPlanController::class, 'calendar'])->name('calendar');   // Kalender mingguan
    Route::post('/{id}/refleksi',[LessonPlanController::class, 'reflect']) ->name('reflect');    // Simpan refleksi
    Route::post('/todo/{todoId}/toggle', [LessonPlanController::class, 'toggleTodo'])->name('todo.toggle'); // Toggle to-do
});
```

---

## 5. CONTROLLER

Buat `app/Http/Controllers/Guru/LessonPlanController.php`:

```php
<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\LessonPlan;
use App\Models\LessonTodo;
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

        $weekPlans    = LessonPlan::where('teacher_id', $teacher->id)
                            ->whereBetween('teach_date', [$today, $today->copy()->addDays(6)])
                            ->orderBy('teach_date')
                            ->get();

        $pendingTodos = LessonTodo::whereHas('lessonPlan', fn($q) => $q->where('teacher_id', $teacher->id))
                            ->where('is_done', 0)
                            ->with('lessonPlan')
                            ->orderBy('due_before')
                            ->limit(10)
                            ->get();

        return view('guru.lesson-plan.index', compact(
            'todayPlan', 'tomorrowPlan', 'weekPlans', 'pendingTodos'
        ));
    }

    public function create()
    {
        // Ambil daftar kelas & mapel yang diampu guru ini
        $classes  = Auth::user()->classes;   // relasi many-to-many
        $subjects = Auth::user()->subjects;
        return view('guru.lesson-plan.create', compact('classes', 'subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id'            => 'required|exists:classes,id',
            'subject_id'          => 'required|exists:subjects,id',
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

        $plan = LessonPlan::create([
            ...$validated,
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

        return redirect()->route('guru.lesson-plan.show', $plan->id)
                         ->with('success', 'Rencana pembelajaran berhasil disimpan!');
    }

    public function show($id)
    {
        $plan = LessonPlan::with(['todos', 'class', 'subject'])
                    ->where('teacher_id', Auth::id())
                    ->findOrFail($id);
        return view('guru.lesson-plan.show', compact('plan'));
    }

    public function edit($id)
    {
        $plan     = LessonPlan::where('teacher_id', Auth::id())->findOrFail($id);
        $classes  = Auth::user()->classes;
        $subjects = Auth::user()->subjects;
        return view('guru.lesson-plan.edit', compact('plan', 'classes', 'subjects'));
    }

    public function update(Request $request, $id)
    {
        $plan = LessonPlan::where('teacher_id', Auth::id())->findOrFail($id);
        $validated = $request->validate([
            'topic'               => 'required|string|max:255',
            'learning_objectives' => 'required|string',
            'pre_assessment'      => 'nullable|string',
            'methods'             => 'nullable|array',
            'activities'          => 'nullable|array',
            'resources'           => 'nullable|array',
            'final_assessment'    => 'nullable|string',
            'differentiation'     => 'nullable|array',
            'duration_minutes'    => 'nullable|integer',
            'teach_date'          => 'required|date',
        ]);
        $plan->update($validated);
        return redirect()->route('guru.lesson-plan.show', $plan->id)
                         ->with('success', 'Rencana pembelajaran diperbarui.');
    }

    public function destroy($id)
    {
        LessonPlan::where('teacher_id', Auth::id())->findOrFail($id)->delete();
        return redirect()->route('guru.lesson-plan.index')->with('success', 'RPP dihapus.');
    }

    public function calendar()
    {
        $plans = LessonPlan::where('teacher_id', Auth::id())
                    ->whereBetween('teach_date', [now()->startOfWeek(), now()->endOfWeek()->addWeek()])
                    ->orderBy('teach_date')
                    ->get();
        return view('guru.lesson-plan.calendar', compact('plans'));
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
```

---

## 6. MODEL

### `app/Models/LessonPlan.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LessonPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id', 'class_id', 'subject_id', 'teach_date', 'topic',
        'learning_objectives', 'pre_assessment', 'methods', 'activities',
        'resources', 'final_assessment', 'differentiation',
        'duration_minutes', 'status', 'reflection',
    ];

    protected $casts = [
        'teach_date'      => 'date',
        'methods'         => 'array',
        'activities'      => 'array',
        'resources'       => 'array',
        'differentiation' => 'array',
    ];

    public function teacher()  { return $this->belongsTo(User::class,    'teacher_id'); }
    public function class()    { return $this->belongsTo(SchoolClass::class, 'class_id'); }
    public function subject()  { return $this->belongsTo(Subject::class, 'subject_id'); }
    public function todos()    { return $this->hasMany(LessonTodo::class)->orderBy('sort_order'); }

    public function pendingTodosCount(): int
    {
        return $this->todos()->where('is_done', 0)->count();
    }

    public function completionPercent(): int
    {
        $total = $this->todos()->count();
        if ($total === 0) return 100;
        return (int) round(($this->todos()->where('is_done', 1)->count() / $total) * 100);
    }

    public function statusBadge(): string
    {
        return match($this->status) {
            'draft'     => '<span class="badge bg-secondary">Draft</span>',
            'published' => '<span class="badge bg-primary">Siap Mengajar</span>',
            'done'      => '<span class="badge bg-success">Selesai ✓</span>',
            default     => ''
        };
    }
}
```

### `app/Models/LessonTodo.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonTodo extends Model
{
    protected $fillable = [
        'lesson_plan_id', 'todo_text', 'category', 'is_done', 'due_before', 'sort_order'
    ];

    protected $casts = ['is_done' => 'boolean', 'due_before' => 'datetime'];

    public function lessonPlan() { return $this->belongsTo(LessonPlan::class); }
}
```

---

## 7. VIEWS (BLADE TEMPLATES)

### Struktur folder views:
```
resources/views/guru/lesson-plan/
├── index.blade.php        ← Dashboard harian + to-do
├── create.blade.php       ← Wizard multi-tahap
├── edit.blade.php         ← Edit RPP
├── show.blade.php         ← Detail RPP lengkap
└── calendar.blade.php     ← Kalender mingguan
```

---

### `index.blade.php` — Dashboard Harian Guru

```blade
@extends('layouts.guru')

@section('title', 'Rencana Pembelajaran')

@section('content')
<div class="container-fluid py-4">

  {{-- HEADER --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="mb-0 fw-bold">📚 Rencana Pembelajaran</h2>
      <p class="text-muted mb-0">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>
    <a href="{{ route('guru.lesson-plan.create') }}" class="btn btn-primary btn-lg">
      <i class="fas fa-plus me-2"></i>Buat RPP Baru
    </a>
  </div>

  <div class="row g-4">

    {{-- KOLOM KIRI: Hari Ini & Besok --}}
    <div class="col-lg-8">

      {{-- HARI INI --}}
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">🗓️ Hari Ini — {{ now()->translatedFormat('d F Y') }}</h5>
        </div>
        <div class="card-body">
          @if($todayPlan)
            <x-lesson-plan-card :plan="$todayPlan" />
          @else
            <div class="text-center py-4 text-muted">
              <i class="fas fa-clipboard-list fa-3x mb-3 opacity-50"></i>
              <p>Belum ada rencana pembelajaran hari ini.</p>
              <a href="{{ route('guru.lesson-plan.create', ['date' => today()->toDateString()]) }}"
                 class="btn btn-outline-primary btn-sm">Buat Sekarang</a>
            </div>
          @endif
        </div>
      </div>

      {{-- BESOK --}}
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-warning text-dark">
          <h5 class="mb-0">⏭️ Besok — {{ now()->addDay()->translatedFormat('d F Y') }}</h5>
        </div>
        <div class="card-body">
          @if($tomorrowPlan)
            <x-lesson-plan-card :plan="$tomorrowPlan" />
          @else
            <div class="text-center py-4 text-muted">
              <i class="fas fa-lightbulb fa-3x mb-3 opacity-50"></i>
              <p>Belum ada rencana untuk besok. Mulai rencanakan sekarang!</p>
              <a href="{{ route('guru.lesson-plan.create', ['date' => today()->addDay()->toDateString()]) }}"
                 class="btn btn-outline-warning btn-sm">Rencanakan Besok</a>
            </div>
          @endif
        </div>
      </div>

      {{-- MINGGUAN OVERVIEW --}}
      <div class="card border-0 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">📅 7 Hari Ke Depan</h5>
          <a href="{{ route('guru.lesson-plan.calendar') }}" class="btn btn-sm btn-outline-secondary">Lihat Kalender</a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light"><tr>
                <th>Tanggal</th><th>Kelas</th><th>Mata Pelajaran</th><th>Topik</th><th>Status</th><th></th>
              </tr></thead>
              <tbody>
                @forelse($weekPlans as $wp)
                <tr>
                  <td>{{ $wp->teach_date->translatedFormat('D, d M') }}</td>
                  <td>{{ $wp->class->name ?? '-' }}</td>
                  <td>{{ $wp->subject->name ?? '-' }}</td>
                  <td>{{ Str::limit($wp->topic, 40) }}</td>
                  <td>{!! $wp->statusBadge() !!}</td>
                  <td><a href="{{ route('guru.lesson-plan.show', $wp->id) }}" class="btn btn-xs btn-outline-primary">Detail</a></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-3">Tidak ada rencana minggu ini.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>

    {{-- KOLOM KANAN: To-Do List --}}
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
        <div class="card-header bg-danger text-white">
          <h5 class="mb-0">✅ To-Do Persiapan Mengajar</h5>
          <small>{{ $pendingTodos->count() }} item belum selesai</small>
        </div>
        <div class="card-body p-0">
          <ul class="list-group list-group-flush" id="todo-list">
            @forelse($pendingTodos as $todo)
            <li class="list-group-item d-flex align-items-start gap-2 py-3">
              <div class="form-check mt-1">
                <input class="form-check-input todo-checkbox"
                       type="checkbox"
                       data-id="{{ $todo->id }}"
                       {{ $todo->is_done ? 'checked' : '' }}>
              </div>
              <div class="flex-grow-1">
                <div class="{{ $todo->is_done ? 'text-decoration-line-through text-muted' : '' }}">
                  {{ $todo->todo_text }}
                </div>
                <div class="d-flex gap-2 mt-1">
                  <span class="badge bg-light text-dark border">{{ ucfirst($todo->category) }}</span>
                  @if($todo->lessonPlan)
                    <small class="text-muted">
                      {{ $todo->lessonPlan->teach_date->translatedFormat('d M') }}
                      · {{ $todo->lessonPlan->subject->name ?? '' }}
                    </small>
                  @endif
                </div>
                @if($todo->due_before)
                  <small class="text-danger">
                    <i class="fas fa-clock"></i> Sebelum {{ $todo->due_before->format('H:i, d M') }}
                  </small>
                @endif
              </div>
            </li>
            @empty
            <li class="list-group-item text-center text-success py-4">
              <i class="fas fa-check-circle fa-2x mb-2"></i>
              <p class="mb-0">Semua persiapan sudah selesai! 🎉</p>
            </li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>

  </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.todo-checkbox').forEach(cb => {
  cb.addEventListener('change', async function () {
    const id  = this.dataset.id;
    const res = await fetch(`/guru/rencana-pembelajaran/todo/${id}/toggle`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json'
      }
    });
    const data = await res.json();
    const textEl = this.closest('li').querySelector('.flex-grow-1 div:first-child');
    textEl.classList.toggle('text-decoration-line-through', data.is_done);
    textEl.classList.toggle('text-muted', data.is_done);
  });
});
</script>
@endpush
@endsection
```

---

### `create.blade.php` — Wizard Multi-Tahap Buat RPP

```blade
@extends('layouts.guru')
@section('title', 'Buat Rencana Pembelajaran')
@section('content')
<div class="container py-4" style="max-width: 860px;">

  <h2 class="fw-bold mb-1">📝 Buat Rencana Pembelajaran</h2>
  <p class="text-muted mb-4">Ikuti 7 tahap alur perencanaan modern (Finlandia/Kanada style)</p>

  {{-- PROGRESS STEPPER --}}
  <div class="d-flex align-items-center mb-4 gap-1" id="stepper">
    @foreach([
      ['1','Tujuan'],['2','Asesmen Awal'],['3','Aktivitas'],
      ['4','Media'],['5','Asesmen Akhir'],['6','Diferensiasi'],['7','To-Do']
    ] as $i => $step)
    <div class="d-flex align-items-center {{ $i < 6 ? 'flex-grow-1' : '' }}">
      <div class="step-indicator d-flex flex-column align-items-center" data-step="{{ $i+1 }}">
        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold
             {{ $i === 0 ? 'bg-primary text-white' : 'bg-light text-muted border' }}"
             style="width:36px;height:36px;font-size:14px;">
          {{ $step[0] }}
        </div>
        <small class="d-none d-md-block text-muted mt-1" style="font-size:11px;">{{ $step[1] }}</small>
      </div>
      @if($i < 6)<div class="flex-grow-1 border-top mx-2" style="margin-top:-12px;"></div>@endif
    </div>
    @endforeach
  </div>

  <form id="rpp-form" action="{{ route('guru.lesson-plan.store') }}" method="POST">
    @csrf

    {{-- INFO DASAR (selalu tampil) --}}
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body row g-3">
        <div class="col-md-4">
          <label class="form-label fw-semibold">Kelas</label>
          <select name="class_id" class="form-select" required>
            <option value="">-- Pilih Kelas --</option>
            @foreach($classes as $c)
              <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Mata Pelajaran</label>
          <select name="subject_id" class="form-select" required>
            <option value="">-- Pilih Mapel --</option>
            @foreach($subjects as $s)
              <option value="{{ $s->id }}">{{ $s->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label fw-semibold">Tanggal Mengajar</label>
          <input type="date" name="teach_date" class="form-control"
                 value="{{ request('date', today()->toDateString()) }}" required>
        </div>
        <div class="col-md-2">
          <label class="form-label fw-semibold">Durasi (menit)</label>
          <input type="number" name="duration_minutes" class="form-control" value="90" min="15" max="480">
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold">Topik / Judul Materi</label>
          <input type="text" name="topic" class="form-control" placeholder="cth: Pemrograman Berorientasi Objek — Inheritance" required>
        </div>
      </div>
    </div>

    {{-- TAHAP 1: TUJUAN PEMBELAJARAN --}}
    <div class="wizard-step" data-step="1">
      <div class="card border-primary border-2 shadow-sm mb-3">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">Tahap 1 — Tujuan Pembelajaran <span class="badge bg-light text-primary ms-2">Apa yang akan bisa siswa LAKUKAN?</span></h5>
        </div>
        <div class="card-body">
          <p class="text-muted small">💡 <strong>Tips Finlandia:</strong> Tulis tujuan dari perspektif siswa. Gunakan kata kerja aktif yang terukur.</p>
          <label class="form-label fw-semibold">Setelah pelajaran ini, siswa mampu...</label>
          <textarea name="learning_objectives" class="form-control" rows="4"
            placeholder="cth:&#10;1. Menjelaskan konsep inheritance dengan kata-kata sendiri&#10;2. Mengimplementasikan kelas turunan dalam bahasa Python&#10;3. Mengidentifikasi keuntungan penggunaan inheritance dalam proyek nyata" required></textarea>
          <div class="mt-2">
            <small class="text-muted">Saran kata kerja: menganalisis · membuat · mengevaluasi · mendemonstrasikan · merancang · memecahkan</small>
          </div>
        </div>
      </div>
    </div>

    {{-- TAHAP 2: ASESMEN AWAL --}}
    <div class="wizard-step" data-step="2">
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-light">
          <h5 class="mb-0">Tahap 2 — Asesmen Awal</h5>
        </div>
        <div class="card-body">
          <p class="text-muted small">💡 Ketahui titik awal siswa sebelum mengajar — ciri khas guru efektif di Kanada.</p>
          <label class="form-label fw-semibold">Bagaimana Anda akan mengecek pengetahuan awal siswa?</label>
          <textarea name="pre_assessment" class="form-control" rows="3"
            placeholder="cth: 3 pertanyaan kuis di awal (Kahoot), atau tanya langsung: 'Apa yang kalian ketahui tentang class di Python?'"></textarea>
        </div>
      </div>
    </div>

    {{-- TAHAP 3: METODE & AKTIVITAS --}}
    <div class="wizard-step" data-step="3">
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-light">
          <h5 class="mb-0">Tahap 3 — Metode & Aktivitas Pembelajaran</h5>
        </div>
        <div class="card-body">
          <label class="form-label fw-semibold">Pilih Metode Pembelajaran (bisa lebih dari satu)</label>
          <div class="row g-2 mb-4">
            @foreach([
              ['PBL',               'Problem-Based Learning',  'Siswa memecahkan masalah nyata', '🔍'],
              ['PjBL',              'Project-Based Learning',  'Siswa menghasilkan produk/proyek', '🏗️'],
              ['Collaborative',     'Collaborative Learning',  'Belajar berkelompok & diskusi', '🤝'],
              ['Inquiry',           'Inquiry-Based Learning',  'Siswa bertanya & mencari sendiri', '🔬'],
              ['Flipped Classroom', 'Flipped Classroom',       'Materi di rumah, praktik di kelas', '🔄'],
              ['Direct',            'Direct Instruction',      'Ceramah singkat ≤15 menit', '🎤'],
            ] as [$val, $label, $desc, $icon])
            <div class="col-md-6">
              <label class="method-card d-flex align-items-start gap-3 p-3 border rounded-3 cursor-pointer hover-bg-light">
                <input type="checkbox" name="methods[]" value="{{ $val }}" class="method-checkbox mt-1">
                <div>
                  <div class="fw-semibold">{{ $icon }} {{ $label }}</div>
                  <small class="text-muted">{{ $desc }}</small>
                </div>
              </label>
            </div>
            @endforeach
          </div>

          <label class="form-label fw-semibold">Urutan Aktivitas Kelas</label>
          <div class="row g-2">
            @foreach([
              ['pembuka',    '🌅 Pembuka (5-10 mnt)',   'Apersepsi, motivasi, tujuan belajar'],
              ['eksplorasi', '🔍 Eksplorasi (20-30 mnt)','Siswa menjelajahi materi/masalah'],
              ['elaborasi',  '✍️ Elaborasi (30-40 mnt)', 'Siswa mengerjakan tugas/proyek'],
              ['konfirmasi', '✅ Konfirmasi (10-15 mnt)', 'Pembahasan, klarifikasi, feedback'],
              ['penutup',    '🌙 Penutup (5-10 mnt)',    'Rangkuman, exit ticket, PR'],
            ] as [$key, $label, $hint])
            <div class="col-12">
              <label class="form-label fw-semibold small">{{ $label }}</label>
              <textarea name="activities[{{ $key }}]" class="form-control form-control-sm" rows="2"
                        placeholder="{{ $hint }}"></textarea>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    {{-- TAHAP 4: MEDIA & BAHAN --}}
    <div class="wizard-step" data-step="4">
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-light">
          <h5 class="mb-0">Tahap 4 — Bahan & Media Pembelajaran</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Link Materi / Modul</label>
              <input type="url" name="resources[link]" class="form-control" placeholder="https://...">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Video Pembelajaran</label>
              <input type="url" name="resources[video]" class="form-control" placeholder="https://youtube.com/...">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Kebutuhan Ruangan / Fasilitas</label>
              <div class="d-flex flex-wrap gap-3">
                @foreach(['Proyektor','Lab Komputer','Internet','Papan Tulis','Alat Praktik','Headset','Printer'] as $fac)
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="resources[fasilitas][]" value="{{ $fac }}" id="fac_{{ $fac }}">
                  <label class="form-check-label" for="fac_{{ $fac }}">{{ $fac }}</label>
                </div>
                @endforeach
              </div>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Catatan Bahan Lainnya</label>
              <textarea name="resources[notes]" class="form-control" rows="2" placeholder="cth: Siapkan dataset CSV untuk latihan, print lembar kerja siswa"></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- TAHAP 5: ASESMEN AKHIR --}}
    <div class="wizard-step" data-step="5">
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-light">
          <h5 class="mb-0">Tahap 5 — Asesmen Akhir (Formative)</h5>
        </div>
        <div class="card-body">
          <p class="text-muted small">💡 <strong>Finlandia:</strong> Asesmen bukan untuk nilai, tapi untuk mengecek pemahaman dan memberi feedback bermakna.</p>
          <label class="form-label fw-semibold">Bagaimana Anda tahu siswa sudah mencapai tujuan belajar?</label>
          <div class="row g-2 mb-3">
            @foreach(['Unjuk Kerja / Demo','Kuis Singkat','Exit Ticket (kartu keluar)','Portofolio','Presentasi Kelompok','Peer Review','Observasi Langsung'] as $as)
            <div class="col-md-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="final_assessment_methods[]" value="{{ $as }}">
                <label class="form-check-label">{{ $as }}</label>
              </div>
            </div>
            @endforeach
          </div>
          <label class="form-label fw-semibold">Detail Asesmen</label>
          <textarea name="final_assessment" class="form-control" rows="3"
            placeholder="cth: Siswa membuat class sederhana dan mendemonstrasikan inheritance di depan kelas (3-5 menit per kelompok)"></textarea>
        </div>
      </div>
    </div>

    {{-- TAHAP 6: DIFERENSIASI --}}
    <div class="wizard-step" data-step="6">
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-light">
          <h5 class="mb-0">Tahap 6 — Diferensiasi Pembelajaran <span class="badge bg-info ms-2">Inklusivitas</span></h5>
        </div>
        <div class="card-body">
          <p class="text-muted small">💡 Setiap siswa belajar dengan cara dan kecepatan berbeda. Rencanakan untuk keduanya.</p>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold text-success">🚀 Pengayaan (siswa sudah mahir)</label>
              <textarea name="differentiation[pengayaan]" class="form-control" rows="3"
                placeholder="cth: Tantang mereka membuat implementasi multiple inheritance dengan kasus nyata"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold text-warning">🧩 Remedial / Scaffolding (siswa tertinggal)</label>
              <textarea name="differentiation[remedial]" class="form-control" rows="3"
                placeholder="cth: Berikan contoh kode bertahap dengan komentar penjelasan, dampingi 1-on-1"></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- TAHAP 7: TO-DO LIST --}}
    <div class="wizard-step" data-step="7">
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Tahap 7 — To-Do List Persiapan</h5>
          <span class="badge bg-info">To-do otomatis akan ditambahkan berdasarkan metode</span>
        </div>
        <div class="card-body">
          <div id="todo-container">
            <div class="todo-row row g-2 mb-2">
              <div class="col-6">
                <input type="text" name="todos[0][text]" class="form-control" placeholder="Yang perlu disiapkan...">
              </div>
              <div class="col-3">
                <select name="todos[0][category]" class="form-select">
                  <option value="materi">📄 Materi</option>
                  <option value="media">💻 Media</option>
                  <option value="administrasi">📋 Administrasi</option>
                  <option value="ruangan">🏫 Ruangan</option>
                  <option value="lainnya">📌 Lainnya</option>
                </select>
              </div>
              <div class="col-3">
                <input type="datetime-local" name="todos[0][due_before]" class="form-control" title="Deadline persiapan">
              </div>
            </div>
          </div>
          <button type="button" class="btn btn-outline-secondary btn-sm" id="add-todo">
            <i class="fas fa-plus me-1"></i>Tambah Item
          </button>
        </div>
      </div>
    </div>

    {{-- TOMBOL AKSI --}}
    <div class="d-flex justify-content-between mt-4">
      <button type="button" class="btn btn-outline-secondary" id="btn-prev" style="display:none!important;">
        ← Sebelumnya
      </button>
      <div class="ms-auto d-flex gap-2">
        <button type="submit" name="action" value="draft" class="btn btn-outline-primary">
          <i class="fas fa-save me-1"></i>Simpan Draft
        </button>
        <button type="submit" name="action" value="publish" class="btn btn-primary" id="btn-next">
          <i class="fas fa-check me-1"></i>Simpan & Siap Mengajar
        </button>
      </div>
    </div>

  </form>
</div>

@push('styles')
<style>
.method-card { cursor: pointer; transition: all .2s; }
.method-card:has(input:checked) { background: #eef2ff; border-color: #6366f1 !important; }
.wizard-step { display: none; }
.wizard-step.active { display: block; }
.wizard-step[data-step="1"] { display: block; } /* tahap 1 tampil default */
</style>
@endpush

@push('scripts')
<script>
// Simple wizard navigation — untuk simplisitas semua tahap ditampilkan sekaligus
// (single-page scroll), stepper hanya sebagai visual progress
// Jika ingin multi-step hidden, uncomment logika di bawah.

let todoIndex = 1;
document.getElementById('add-todo').addEventListener('click', () => {
  const c = document.getElementById('todo-container');
  c.insertAdjacentHTML('beforeend', `
    <div class="todo-row row g-2 mb-2">
      <div class="col-6">
        <input type="text" name="todos[${todoIndex}][text]" class="form-control" placeholder="Yang perlu disiapkan...">
      </div>
      <div class="col-3">
        <select name="todos[${todoIndex}][category]" class="form-select">
          <option value="materi">📄 Materi</option>
          <option value="media">💻 Media</option>
          <option value="administrasi">📋 Administrasi</option>
          <option value="ruangan">🏫 Ruangan</option>
          <option value="lainnya">📌 Lainnya</option>
        </select>
      </div>
      <div class="col-3">
        <input type="datetime-local" name="todos[${todoIndex}][due_before]" class="form-control">
      </div>
    </div>`);
  todoIndex++;
});

// Tampilkan semua steps (scroll-based)
document.querySelectorAll('.wizard-step').forEach(s => s.style.display = 'block');
</script>
@endpush
@endsection
```

---

### `show.blade.php` — Detail RPP + Refleksi

```blade
@extends('layouts.guru')
@section('title', 'Detail RPP')
@section('content')
<div class="container py-4" style="max-width: 860px;">

  <div class="d-flex justify-content-between align-items-start mb-4">
    <div>
      <h2 class="fw-bold mb-1">{{ $plan->topic }}</h2>
      <p class="text-muted">
        {{ $plan->class->name ?? '-' }} · {{ $plan->subject->name ?? '-' }} ·
        {{ $plan->teach_date->translatedFormat('l, d F Y') }} ·
        {{ $plan->duration_minutes }} menit
        {!! $plan->statusBadge() !!}
      </p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('guru.lesson-plan.edit', $plan->id) }}" class="btn btn-outline-primary">Edit</a>
    </div>
  </div>

  {{-- PROGRESS TO-DO --}}
  @if($plan->todos->count())
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between mb-2">
        <strong>Progres Persiapan</strong>
        <span class="text-muted">{{ $plan->completionPercent() }}% selesai</span>
      </div>
      <div class="progress mb-3" style="height: 8px;">
        <div class="progress-bar bg-success" style="width: {{ $plan->completionPercent() }}%"></div>
      </div>
      <ul class="list-group list-group-flush">
        @foreach($plan->todos as $todo)
        <li class="list-group-item d-flex align-items-center gap-2 px-0">
          <input type="checkbox" class="form-check-input todo-checkbox" data-id="{{ $todo->id }}" {{ $todo->is_done ? 'checked' : '' }}>
          <span class="{{ $todo->is_done ? 'text-decoration-line-through text-muted' : '' }}">{{ $todo->todo_text }}</span>
          <span class="badge bg-light text-dark border ms-auto">{{ $todo->category }}</span>
        </li>
        @endforeach
      </ul>
    </div>
  </div>
  @endif

  {{-- DETAIL RPP --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <div class="row g-4">

        <div class="col-12">
          <h6 class="text-primary fw-bold">🎯 Tujuan Pembelajaran</h6>
          <p>{{ $plan->learning_objectives }}</p>
        </div>

        @if($plan->pre_assessment)
        <div class="col-md-6">
          <h6 class="text-info fw-bold">🔎 Asesmen Awal</h6>
          <p>{{ $plan->pre_assessment }}</p>
        </div>
        @endif

        @if($plan->methods)
        <div class="col-md-6">
          <h6 class="text-success fw-bold">⚙️ Metode Pembelajaran</h6>
          <div class="d-flex flex-wrap gap-2">
            @foreach($plan->methods as $m)
            <span class="badge bg-success-subtle text-success border border-success-subtle">{{ $m }}</span>
            @endforeach
          </div>
        </div>
        @endif

        @if($plan->activities)
        <div class="col-12">
          <h6 class="text-warning fw-bold">📋 Aktivitas Kelas</h6>
          @foreach($plan->activities as $phase => $act)
            @if($act)
            <div class="mb-2">
              <strong class="text-capitalize">{{ str_replace('_',' ',$phase) }}:</strong>
              <span class="text-muted ms-2">{{ $act }}</span>
            </div>
            @endif
          @endforeach
        </div>
        @endif

        @if($plan->final_assessment)
        <div class="col-md-6">
          <h6 class="text-danger fw-bold">📊 Asesmen Akhir</h6>
          <p>{{ $plan->final_assessment }}</p>
        </div>
        @endif

        @if($plan->differentiation)
        <div class="col-md-6">
          <h6 class="text-purple fw-bold">🎨 Diferensiasi</h6>
          @if(!empty($plan->differentiation['pengayaan']))
            <p><strong class="text-success">Pengayaan:</strong> {{ $plan->differentiation['pengayaan'] }}</p>
          @endif
          @if(!empty($plan->differentiation['remedial']))
            <p><strong class="text-warning">Remedial:</strong> {{ $plan->differentiation['remedial'] }}</p>
          @endif
        </div>
        @endif

      </div>
    </div>
  </div>

  {{-- REFLEKSI (diisi setelah mengajar) --}}
  <div class="card border-0 shadow-sm {{ $plan->status === 'done' ? 'border-success' : '' }}">
    <div class="card-header {{ $plan->status === 'done' ? 'bg-success text-white' : 'bg-light' }}">
      <h5 class="mb-0">💭 Refleksi Setelah Mengajar</h5>
      @if($plan->status !== 'done')
        <small>Isi setelah pelajaran selesai — kunci guru profesional ala Finlandia</small>
      @endif
    </div>
    <div class="card-body">
      @if($plan->reflection)
        <blockquote class="blockquote">
          <p>{{ $plan->reflection }}</p>
        </blockquote>
        <a href="#" data-bs-toggle="collapse" data-bs-target="#edit-reflection">Edit Refleksi</a>
        <div class="collapse" id="edit-reflection">
      @endif

      <form action="{{ route('guru.lesson-plan.reflect', $plan->id) }}" method="POST">
        @csrf
        <textarea name="reflection" class="form-control mb-3" rows="4"
          placeholder="Apa yang berjalan baik? Apa yang perlu diperbaiki? Apa yang akan dilakukan berbeda di pertemuan berikutnya?">{{ $plan->reflection }}</textarea>
        <button type="submit" class="btn btn-success">
          <i class="fas fa-save me-1"></i>Simpan Refleksi
        </button>
      </form>

      @if($plan->reflection) </div> @endif
    </div>
  </div>

</div>
@endsection
```

---

## 8. KOMPONEN BLADE (OPSIONAL)

Buat `resources/views/components/lesson-plan-card.blade.php`:

```blade
@props(['plan'])

<div class="d-flex justify-content-between align-items-start">
  <div class="flex-grow-1">
    <h5 class="fw-bold mb-1">{{ $plan->topic }}</h5>
    <div class="d-flex gap-2 mb-2 flex-wrap">
      <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $plan->class->name ?? '-' }}</span>
      <span class="badge bg-secondary-subtle text-secondary border">{{ $plan->subject->name ?? '-' }}</span>
      <span class="badge bg-light text-dark border">⏱ {{ $plan->duration_minutes }} mnt</span>
      @foreach($plan->methods ?? [] as $m)
        <span class="badge bg-info-subtle text-info border border-info-subtle">{{ $m }}</span>
      @endforeach
    </div>
    <p class="text-muted small mb-2">🎯 {{ Str::limit($plan->learning_objectives, 120) }}</p>

    @if($plan->todos->count())
    <div class="mb-2">
      <div class="d-flex justify-content-between mb-1">
        <small class="text-muted">Persiapan: {{ $plan->completionPercent() }}%</small>
        <small class="text-danger">{{ $plan->pendingTodosCount() }} item belum</small>
      </div>
      <div class="progress" style="height:6px;">
        <div class="progress-bar bg-success" style="width:{{ $plan->completionPercent() }}%"></div>
      </div>
    </div>
    @endif
  </div>
  <div class="ms-3 text-end">
    {!! $plan->statusBadge() !!}
    <br>
    <a href="{{ route('guru.lesson-plan.show', $plan->id) }}" class="btn btn-primary btn-sm mt-2">
      Lihat Detail →
    </a>
  </div>
</div>
```

---

## 9. SIDEBAR MENU (Tambahkan ke navigasi guru)

Tambahkan item berikut di sidebar/navbar role guru:

```blade
<li class="nav-item">
  <a class="nav-link {{ request()->routeIs('guru.lesson-plan.*') ? 'active' : '' }}"
     href="{{ route('guru.lesson-plan.index') }}">
    <i class="fas fa-book-open me-2"></i>Rencana Pembelajaran
  </a>
</li>
```

---

## 10. FILOSOFI & CHECKLIST PRINSIP MODERN

Pastikan setiap RPP yang dibuat guru mengikuti prinsip ini (bisa dijadikan tooltip/helper text di UI):

| # | Prinsip | Sumber |
|---|---------|--------|
| 1 | Tujuan belajar berpusat pada siswa (bukan materi) | Finlandia |
| 2 | Asesmen awal sebelum mengajar | Kanada |
| 3 | Pembelajaran aktif ≥ 70% waktu kelas | Keduanya |
| 4 | Ceramah ≤ 15 menit | Finlandia |
| 5 | Ada diferensiasi untuk semua level siswa | Kanada |
| 6 | Refleksi guru setelah setiap sesi | Finlandia |
| 7 | Asesmen formatif (bukan hanya sumatif) | Keduanya |
| 8 | Koneksi materi dengan dunia nyata/industri | SMK |

---

## 11. PERINTAH ARTISAN

Jalankan setelah semua file dibuat:

```bash
# Buat migration
php artisan make:migration create_lesson_plans_table
php artisan make:migration create_lesson_todos_table

# Jalankan migration
php artisan migrate

# (Opsional) Buat seeder untuk data contoh
php artisan make:seeder LessonPlanSeeder
php artisan db:seed --class=LessonPlanSeeder

# Clear cache
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan optimize
```

---

## 12. DEPENDENSI FRONTEND

Pastikan sudah ter-include di layout:

```html
<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<!-- CSRF meta tag (wajib untuk AJAX) -->
<meta name="csrf-token" content="{{ csrf_token() }}">
```

---

*File ini dibuat sebagai prompt lengkap untuk implementasi fitur Rencana Pembelajaran modern pada aplikasi Laravel Sisfo SMK — mengadopsi pendekatan pedagogis Finlandia & Kanada.*
