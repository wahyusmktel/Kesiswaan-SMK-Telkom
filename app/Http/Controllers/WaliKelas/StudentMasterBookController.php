<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\MasterSiswa;
use App\Models\Rombel;
use App\Models\StudentMasterBook;
use App\Models\StudentMasterBookAttachment;
use App\Models\StudentMasterBookPeriod;
use App\Models\TahunPelajaran;
use App\Services\StudentMasterBookPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StudentMasterBookController extends Controller
{
    public function index(Request $request)
    {
        $rombels = $this->homeroomRombels();
        $selectedRombel = $rombels->firstWhere('id', (int) $request->integer('rombel_id')) ?? $rombels->first();

        $students = MasterSiswa::query()
            ->with(['dapodik', 'masterBook:id,master_siswa_id,completed_at,updated_at'])
            ->when($selectedRombel, fn ($query) => $query->whereHas(
                'rombels',
                fn ($rombelQuery) => $rombelQuery->where('rombels.id', $selectedRombel->id)
            ))
            ->when(! $selectedRombel, fn ($query) => $query->whereRaw('1 = 0'))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));
                $query->where(function ($studentQuery) use ($search) {
                    $studentQuery->where('nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%")
                        ->orWhereHas('dapodik', fn ($dapodik) => $dapodik->where('nisn', 'like', "%{$search}%"));
                });
            })
            ->orderBy('nama_lengkap')
            ->paginate(20)
            ->withQueryString();

        return view('pages.wali-kelas.buku-induk.index', compact('rombels', 'selectedRombel', 'students'));
    }

    public function edit(MasterSiswa $student)
    {
        $rombel = $this->authorizeStudent($student);
        $book = StudentMasterBook::firstOrCreate(
            ['master_siswa_id' => $student->id],
            [
                'previous_school' => $student->dapodik?->sekolah_asal,
                'student_status' => $student->status ?? 'aktif',
                'updated_by' => Auth::id(),
            ]
        );

        $student->load('dapodik');
        $book->load(['periods.rombel.kelas', 'attachments']);
        $academicYears = TahunPelajaran::orderByDesc('tahun')->orderBy('semester')->get();

        return view('pages.wali-kelas.buku-induk.edit', compact('student', 'book', 'rombel', 'academicYears'));
    }

    public function update(Request $request, MasterSiswa $student)
    {
        $this->authorizeStudent($student);

        $validated = $request->validate([
            'admission_date' => ['nullable', 'date'],
            'admission_status' => ['nullable', 'string', 'max:100'],
            'previous_school' => ['nullable', 'string', 'max:255'],
            'previous_diploma_number' => ['nullable', 'string', 'max:100'],
            'previous_diploma_date' => ['nullable', 'date'],
            'blood_type' => ['nullable', Rule::in(['A', 'B', 'AB', 'O', '-'])],
            'medical_history' => ['nullable', 'string', 'max:3000'],
            'special_needs_notes' => ['nullable', 'string', 'max:3000'],
            'student_status' => ['required', Rule::in(['aktif', 'pindah', 'lulus', 'keluar'])],
            'transfer_date' => ['nullable', 'date'],
            'transfer_destination' => ['nullable', 'string', 'max:255'],
            'transfer_reason' => ['nullable', 'string', 'max:2000'],
            'graduation_date' => ['nullable', 'date'],
            'graduation_certificate_number' => ['nullable', 'string', 'max:100'],
            'homeroom_notes' => ['nullable', 'string', 'max:5000'],
            'additional_data' => ['nullable', 'array'],
            'additional_data.citizenship' => ['nullable', 'string', 'max:100'],
            'additional_data.child_status' => ['nullable', 'string', 'max:100'],
            'additional_data.daily_language' => ['nullable', 'string', 'max:100'],
            'additional_data.hobby' => ['nullable', 'string', 'max:255'],
            'additional_data.aspiration' => ['nullable', 'string', 'max:255'],
            'additional_data.accepted_grade' => ['nullable', 'string', 'max:50'],
            'additional_data.accepted_program' => ['nullable', 'string', 'max:255'],
            'additional_data.guardian_phone' => ['nullable', 'string', 'max:30'],
            'additional_data.health_notes' => ['nullable', 'string', 'max:3000'],
            'additional_data.education_notes' => ['nullable', 'string', 'max:3000'],
            'mark_complete' => ['nullable', 'boolean'],
        ]);

        $validated['updated_by'] = Auth::id();
        $validated['completed_at'] = $request->boolean('mark_complete') ? now() : null;
        unset($validated['mark_complete']);

        StudentMasterBook::updateOrCreate(['master_siswa_id' => $student->id], $validated);

        return back()->with('success', 'Data Buku Induk berhasil disimpan.');
    }

    public function storePeriod(Request $request, MasterSiswa $student)
    {
        $rombel = $this->authorizeStudent($student);
        $book = StudentMasterBook::firstOrCreate(
            ['master_siswa_id' => $student->id],
            ['student_status' => 'aktif', 'updated_by' => Auth::id()]
        );

        $validated = $request->validate([
            'period_id' => ['nullable', 'integer'],
            'tahun_pelajaran_id' => ['nullable', 'exists:tahun_pelajaran,id'],
            'school_year' => ['required', 'string', 'max:20'],
            'semester' => ['required', Rule::in(['Ganjil', 'Genap'])],
            'grades' => ['nullable', 'array', 'max:60'],
            'grades.*.subject' => ['required_with:grades', 'string', 'max:255'],
            'grades.*.score' => ['required_with:grades', 'numeric', 'min:0', 'max:100'],
            'extracurriculars' => ['nullable', 'array', 'max:20'],
            'extracurriculars.*.name' => ['required_with:extracurriculars', 'string', 'max:255'],
            'extracurriculars.*.predicate' => ['nullable', 'string', 'max:100'],
            'extracurriculars.*.description' => ['nullable', 'string', 'max:500'],
            'sick_days' => ['required', 'integer', 'min:0', 'max:366'],
            'permitted_days' => ['required', 'integer', 'min:0', 'max:366'],
            'absent_days' => ['required', 'integer', 'min:0', 'max:366'],
            'conduct' => ['nullable', 'string', 'max:100'],
            'development_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $period = null;
        if (! empty($validated['period_id'])) {
            $period = $book->periods()->findOrFail($validated['period_id']);
        }

        $payload = Arr::except($validated, 'period_id') + [
            'rombel_id' => $rombel->id,
            'updated_by' => Auth::id(),
        ];

        if ($period) {
            $period->update($payload);
        } else {
            $book->periods()->updateOrCreate(
                ['school_year' => $payload['school_year'], 'semester' => $payload['semester']],
                $payload
            );
        }

        return back()->with('success', 'Riwayat semester berhasil disimpan.');
    }

    public function destroyPeriod(MasterSiswa $student, StudentMasterBookPeriod $period)
    {
        $this->authorizeStudent($student);
        abort_unless($period->masterBook?->master_siswa_id === $student->id, 404);
        $period->delete();

        return back()->with('success', 'Riwayat semester berhasil dihapus.');
    }

    public function storeAttachment(Request $request, MasterSiswa $student)
    {
        $this->authorizeStudent($student);
        $book = StudentMasterBook::firstOrCreate(
            ['master_siswa_id' => $student->id],
            ['student_status' => 'aktif', 'updated_by' => Auth::id()]
        );

        $validated = $request->validate([
            'category' => ['required', Rule::in(['foto', 'akta', 'kartu_keluarga', 'ijazah', 'kesehatan', 'prestasi', 'mutasi', 'lainnya'])],
            'title' => ['required', 'string', 'max:255'],
            'files' => ['required', 'array', 'max:10'],
            'files.*' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'],
        ]);

        foreach ($request->file('files') as $file) {
            $path = $file->store("buku-induk/{$student->id}");
            $book->attachments()->create([
                'category' => $validated['category'],
                'title' => $validated['title'],
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType() ?: $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => Auth::id(),
            ]);
        }

        return back()->with('success', 'Lampiran Buku Induk berhasil diunggah.');
    }

    public function downloadAttachment(MasterSiswa $student, StudentMasterBookAttachment $attachment)
    {
        $this->authorizeStudent($student);
        abort_unless($attachment->masterBook?->master_siswa_id === $student->id, 404);
        abort_unless(Storage::exists($attachment->file_path), 404);

        return Storage::download($attachment->file_path, $attachment->original_name);
    }

    public function destroyAttachment(MasterSiswa $student, StudentMasterBookAttachment $attachment)
    {
        $this->authorizeStudent($student);
        abort_unless($attachment->masterBook?->master_siswa_id === $student->id, 404);
        Storage::delete($attachment->file_path);
        $attachment->delete();

        return back()->with('success', 'Lampiran berhasil dihapus.');
    }

    public function printStudent(MasterSiswa $student, StudentMasterBookPdfService $pdfService)
    {
        $rombel = $this->authorizeStudent($student);
        $student->load(['dapodik', 'masterBook.periods', 'masterBook.attachments']);

        return response($pdfService->studentPacket($student, $rombel), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Buku_Induk_'.str($student->nama_lengkap)->slug('_').'.pdf"',
        ]);
    }

    public function printClass(Rombel $rombel, StudentMasterBookPdfService $pdfService)
    {
        abort_unless($this->homeroomRombels()->contains('id', $rombel->id), 403);
        $rombel->load(['kelas', 'tahunPelajaran', 'siswa' => fn ($query) => $query
            ->with(['dapodik', 'masterBook.periods', 'masterBook.attachments'])
            ->orderBy('nama_lengkap')]);

        abort_if($rombel->siswa->isEmpty(), 422, 'Rombel belum memiliki siswa.');

        return response($pdfService->classPacket($rombel), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Buku_Induk_'.str($rombel->kelas?->nama_kelas ?? 'kelas')->slug('_').'.pdf"',
        ]);
    }

    private function authorizeStudent(MasterSiswa $student): Rombel
    {
        $rombel = $this->homeroomRombels()
            ->first(fn (Rombel $item) => $item->siswa()->where('master_siswa.id', $student->id)->exists());

        abort_unless($rombel, 403, 'Siswa bukan anggota kelas binaan aktif Anda.');

        return $rombel;
    }

    private function homeroomRombels()
    {
        return Rombel::query()
            ->with(['kelas', 'tahunPelajaran'])
            ->withCount('siswa')
            ->where('wali_kelas_id', Auth::id())
            ->whereHas('tahunPelajaran', fn ($query) => $query->where('is_active', true))
            ->orderBy('kelas_id')
            ->get();
    }
}
