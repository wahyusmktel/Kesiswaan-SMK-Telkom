<?php

namespace App\Http\Controllers\GuruKelas;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeachingModuleContentRequest;
use App\Http\Requests\TeachingModuleMetadataRequest;
use App\Models\AppSetting;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TahunPelajaran;
use App\Models\TeachingModule;
use App\Support\TeachingModuleSchema;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TeachingModuleController extends Controller
{
    public function index(Request $request)
    {
        $query = TeachingModule::query()
            ->ownedBy(Auth::id())
            ->with(['subject', 'academicYear']);

        $search = trim((string) $request->input('search'));
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('nama_modul', 'like', "%{$search}%")
                    ->orWhere('kode_modul', 'like', "%{$search}%")
                    ->orWhere('mata_pelajaran', 'like', "%{$search}%")
                    ->orWhere('lingkup_materi', 'like', "%{$search}%");
            });
        }

        if (in_array($request->input('status'), ['draft', 'complete'], true)) {
            $query->where('status', $request->input('status'));
        }

        $modules = $query->latest('updated_at')->paginate(10)->withQueryString();
        $baseStats = TeachingModule::query()->ownedBy(Auth::id());
        $stats = [
            'total' => (clone $baseStats)->count(),
            'draft' => (clone $baseStats)->where('status', 'draft')->count(),
            'complete' => (clone $baseStats)->where('status', 'complete')->count(),
        ];

        return view('pages.guru-kelas.teaching-module.index', compact('modules', 'stats', 'search'));
    }

    public function create()
    {
        $options = $this->formOptions();
        $module = new TeachingModule([
            'nama_penyusun' => $options['teacherName'],
            'instansi' => $options['schoolName'],
            'tahun_pelajaran_id' => $options['activeAcademicYear']?->id,
            'jenjang' => 'SMK',
            'fase' => 'F',
            'kelas' => 'XI',
            'jumlah_murid' => 'Disesuaikan',
            'alokasi_waktu' => '4 JP',
        ]);

        return view('pages.guru-kelas.teaching-module.create', $options + compact('module'));
    }

    public function store(TeachingModuleMetadataRequest $request)
    {
        $module = TeachingModule::create($this->metadataPayload($request->validated()) + [
            'teacher_id' => Auth::id(),
            'content' => TeachingModuleSchema::defaults($this->contentContext($request->validated())),
            'content_version' => TeachingModuleSchema::VERSION,
            'status' => 'draft',
        ]);

        return redirect()
            ->route('guru-kelas.teaching-module.content.edit', $module)
            ->with('success', 'Data perangkat pembelajaran dibuat. Silakan lanjutkan isi modul ajar.');
    }

    public function editMetadata(TeachingModule $teachingModule)
    {
        $this->ensureOwned($teachingModule);

        return view(
            'pages.guru-kelas.teaching-module.edit',
            $this->formOptions() + ['module' => $teachingModule]
        );
    }

    public function updateMetadata(
        TeachingModuleMetadataRequest $request,
        TeachingModule $teachingModule
    ) {
        $this->ensureOwned($teachingModule);

        $validated = $request->validated();
        $oldAllocation = $teachingModule->alokasi_waktu;
        $oldTeacherName = $teachingModule->nama_penyusun;
        $content = $teachingModule->normalizedContent();

        foreach ($content['experiences'] as &$meeting) {
            if (($meeting['allocation'] ?? '') === $oldAllocation) {
                $meeting['allocation'] = $validated['alokasi_waktu'];
            }
        }
        unset($meeting);

        if (($content['approval']['teacher_name'] ?? '') === $oldTeacherName
            || trim((string) ($content['approval']['teacher_name'] ?? '')) === '') {
            $content['approval']['teacher_name'] = $validated['nama_penyusun'];
        }

        $teachingModule->update($this->metadataPayload($validated) + [
            'content' => TeachingModuleSchema::sanitize(
                $content,
                $this->contentContext($validated)
            ),
        ]);

        return redirect()
            ->route('guru-kelas.teaching-module.index')
            ->with('success', 'Data perangkat pembelajaran berhasil diperbarui.');
    }

    public function editContent(TeachingModule $teachingModule)
    {
        $this->ensureOwned($teachingModule);
        $content = TeachingModuleSchema::normalize(
            $teachingModule->content,
            $this->contentContext([
                'alokasi_waktu' => $teachingModule->alokasi_waktu,
                'nama_penyusun' => $teachingModule->nama_penyusun,
            ])
        );

        $oldJson = old('content_json');
        if (is_string($oldJson)) {
            $decoded = json_decode($oldJson, true);
            if (is_array($decoded)) {
                $content = TeachingModuleSchema::normalize($decoded, [
                    'allocation' => $teachingModule->alokasi_waktu,
                    'teacher_name' => $teachingModule->nama_penyusun,
                ]);
            }
        }

        return view('pages.guru-kelas.teaching-module.content', [
            'module' => $teachingModule,
            'content' => $content,
        ]);
    }

    public function updateContent(
        TeachingModuleContentRequest $request,
        TeachingModule $teachingModule
    ) {
        $this->ensureOwned($teachingModule);
        $validated = $request->validated();

        $teachingModule->update([
            'content' => TeachingModuleSchema::sanitize($validated['content'], [
                'allocation' => $teachingModule->alokasi_waktu,
                'teacher_name' => $teachingModule->nama_penyusun,
                'teacher_nip' => $teachingModule->teacher?->masterGuru?->nik
                    ?? $teachingModule->teacher?->masterGuru?->nuptk
                    ?? '',
            ]),
            'content_version' => TeachingModuleSchema::VERSION,
            'status' => $validated['status'],
        ]);

        $message = $validated['status'] === 'complete'
            ? 'Modul ajar berhasil disimpan dan ditandai lengkap.'
            : 'Draft modul ajar berhasil disimpan.';

        return redirect()
            ->route('guru-kelas.teaching-module.content.edit', $teachingModule)
            ->with('success', $message);
    }

    public function destroy(TeachingModule $teachingModule)
    {
        $this->ensureOwned($teachingModule);
        $teachingModule->delete();

        return redirect()
            ->route('guru-kelas.teaching-module.index')
            ->with('success', 'Perangkat pembelajaran berhasil dihapus.');
    }

    public function previewPdf(TeachingModule $teachingModule)
    {
        $this->ensureOwned($teachingModule);

        return $this->buildPdf($teachingModule)->stream($this->pdfFilename($teachingModule));
    }

    public function downloadPdf(TeachingModule $teachingModule)
    {
        $this->ensureOwned($teachingModule);

        return $this->buildPdf($teachingModule)->download($this->pdfFilename($teachingModule));
    }

    private function metadataPayload(array $validated): array
    {
        $subject = MataPelajaran::findOrFail($validated['mata_pelajaran_id']);
        $academicYear = TahunPelajaran::findOrFail($validated['tahun_pelajaran_id']);

        return $validated + [
            'mata_pelajaran' => $subject->nama_mapel,
            'tahun_pelajaran' => $academicYear->tahun,
            'semester' => $academicYear->semester,
        ];
    }

    private function formOptions(): array
    {
        $user = Auth::user()->loadMissing('masterGuru');
        $subjects = collect();
        $programs = Kelas::query()
            ->whereNotNull('jurusan')
            ->where('jurusan', '!=', '')
            ->distinct()
            ->orderBy('jurusan')
            ->pluck('jurusan');

        if ($user->masterGuru) {
            $schedules = JadwalPelajaran::with(['mataPelajaran', 'rombel.kelas'])
                ->where('master_guru_id', $user->masterGuru->id)
                ->get();

            $subjects = $schedules->pluck('mataPelajaran')->filter()->unique('id')->sortBy('nama_mapel')->values();
            $programs = $programs
                ->merge($schedules->pluck('rombel.kelas.jurusan')->filter())
                ->unique()
                ->sort()
                ->values();
        }

        if ($subjects->isEmpty()) {
            $subjects = MataPelajaran::query()->orderBy('nama_mapel')->get();
        }

        $academicYears = TahunPelajaran::query()
            ->orderByDesc('is_active')
            ->orderByDesc('tahun')
            ->orderByRaw("CASE WHEN semester = 'Ganjil' THEN 0 ELSE 1 END")
            ->get();

        return [
            'subjects' => $subjects,
            'programs' => $programs,
            'academicYears' => $academicYears,
            'activeAcademicYear' => $academicYears->firstWhere('is_active', true),
            'teacherName' => $user->masterGuru?->nama_lengkap ?? $user->name,
            'schoolName' => AppSetting::first()?->school_name ?? config('app.name', 'Sekolah'),
            'allocationOptions' => collect(range(1, 30))->map(fn (int $value) => ($value * 2).' JP'),
            'studentCountOptions' => array_map('strval', range(1, 40)),
        ];
    }

    private function contentContext(array $metadata): array
    {
        $guru = Auth::user()?->masterGuru;

        return [
            'allocation' => $metadata['alokasi_waktu'] ?? '4 JP',
            'teacher_name' => $metadata['nama_penyusun'] ?? Auth::user()?->name ?? '',
            'teacher_nip' => $guru?->nik ?? $guru?->nuptk ?? '',
            'location' => 'Pringsewu',
            'date' => now()->format('Y-m-d'),
        ];
    }

    private function buildPdf(TeachingModule $teachingModule)
    {
        $teachingModule->loadMissing(['teacher.masterGuru', 'subject', 'academicYear']);
        $content = TeachingModuleSchema::normalize($teachingModule->content, [
            'allocation' => $teachingModule->alokasi_waktu,
            'teacher_name' => $teachingModule->nama_penyusun,
            'teacher_nip' => $teachingModule->teacher?->masterGuru?->nik
                ?? $teachingModule->teacher?->masterGuru?->nuptk
                ?? '',
        ]);

        $approvalDate = null;
        if (! empty($content['approval']['date'])) {
            try {
                $approvalDate = Carbon::parse($content['approval']['date']);
            } catch (\Throwable) {
                $approvalDate = null;
            }
        }

        return Pdf::loadView('pdf.teaching-module', [
            'module' => $teachingModule,
            'content' => $content,
            'settings' => AppSetting::first(),
            'approvalDate' => $approvalDate,
            'logoDataUri' => $this->imageDataUri(public_path('images/teaching-module/smk-telkom-lampung.png')),
            'ribbonDataUri' => $this->imageDataUri(public_path('images/teaching-module/header-ribbon.png')),
        ])
            ->setPaper('a4', 'landscape')
            ->setOption('defaultFont', 'Arial')
            ->setOption('isRemoteEnabled', false);
    }

    private function imageDataUri(string $path): ?string
    {
        if (! is_file($path) || ! is_readable($path)) {
            return null;
        }

        $mime = mime_content_type($path) ?: 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($path));
    }

    private function pdfFilename(TeachingModule $teachingModule): string
    {
        return 'Modul_Ajar_'.Str::slug($teachingModule->kode_modul.'_'.$teachingModule->nama_modul, '_').'.pdf';
    }

    private function ensureOwned(TeachingModule $teachingModule): void
    {
        abort_unless((int) $teachingModule->teacher_id === (int) Auth::id(), 404);
    }
}
