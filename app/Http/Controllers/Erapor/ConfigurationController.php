<?php

namespace App\Http\Controllers\Erapor;

use App\Http\Controllers\Controller;
use App\Models\Erapor\EraporPeriodSetting;
use App\Models\Erapor\EraporRefCurriculum;
use App\Models\Erapor\EraporRombelCurriculum;
use App\Models\Kelas;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Services\Erapor\EraporPeriodConfigurationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConfigurationController extends Controller
{
    public function index(Request $request, EraporPeriodConfigurationService $configuration): View
    {
        $period = TahunPelajaran::query()->where('is_active', true)->first();
        $setting = $period
            ? EraporPeriodSetting::query()
                ->with('rombelCurricula')
                ->where('tahun_pelajaran_id', $period->id)
                ->first()
            : null;
        $classId = $request->integer('kelas_id') ?: null;
        $level = strtoupper(trim((string) $request->query('tingkat')));
        $level = in_array($level, ['X', 'XI', 'XII'], true) ? $level : null;
        $status = trim((string) $request->query('status'));
        $status = in_array($status, ['configured', 'unconfigured'], true) ? $status : null;

        $rombels = Rombel::query()
            ->with(['kelas', 'waliKelas', 'eraporCurriculum.referenceCurriculum'])
            ->withCount(['siswa', 'jadwalPelajaran'])
            ->when($period, fn ($query) => $query->where('tahun_pelajaran_id', $period->id),
                fn ($query) => $query->whereRaw('1 = 0'))
            ->when($classId, fn ($query) => $query->where('kelas_id', $classId))
            ->when($level, fn ($query) => $query->whereHas(
                'kelas',
                fn ($query) => $query->where('nama_kelas', 'like', $level.' %')
            ))
            ->when($status === 'configured', fn ($query) => $query->whereHas('eraporCurriculum'))
            ->when($status === 'unconfigured', fn ($query) => $query->whereDoesntHave('eraporCurriculum'))
            ->whereHas('kelas')
            ->get()
            ->sortBy('kelas.nama_kelas', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        return view('pages.erapor.configuration', [
            'period' => $period,
            'setting' => $setting,
            'rombels' => $rombels,
            'readiness' => $period
                ? $configuration->readiness($period, $setting)
                : $this->emptyReadiness(),
            'blockers' => $period
                ? $configuration->blockers($period, $setting)
                : ['Tahun pelajaran aktif belum tersedia.'],
            'classes' => $period
                ? Kelas::query()
                    ->whereHas('rombels', fn ($query) => $query->where('tahun_pelajaran_id', $period->id))
                    ->orderBy('nama_kelas')
                    ->get(['id', 'nama_kelas'])
                : collect(),
            'selectedClassId' => $classId,
            'selectedLevel' => $level,
            'selectedStatus' => $status,
        ]);
    }

    public function updatePeriod(
        Request $request,
        EraporPeriodConfigurationService $configuration
    ): RedirectResponse {
        $period = TahunPelajaran::query()->where('is_active', true)->firstOrFail();
        $validated = $request->validate([
            'workflow_status' => ['required', Rule::in(['setup', 'assessment'])],
            'score_entry_starts_at' => ['nullable', 'date'],
            'score_entry_ends_at' => ['nullable', 'date', 'after_or_equal:score_entry_starts_at'],
            'report_date' => ['nullable', 'date'],
        ]);
        $setting = EraporPeriodSetting::query()->firstOrNew([
            'tahun_pelajaran_id' => $period->id,
        ]);

        if ($validated['workflow_status'] === 'assessment') {
            $blockers = $configuration->blockers($period, $setting->exists ? $setting : null);

            if ($blockers !== []) {
                throw ValidationException::withMessages([
                    'workflow_status' => implode(' ', $blockers),
                ]);
            }
        }

        $setting->fill($validated + [
            'configured_by' => $request->user()->id,
            'configured_at' => now(),
        ])->save();

        return back()->with('success', 'Pengaturan periode e-Rapor berhasil disimpan.');
    }

    public function storeRombelCurriculum(Request $request): RedirectResponse
    {
        $period = TahunPelajaran::query()->where('is_active', true)->firstOrFail();
        $validated = $request->validate([
            'rombel_id' => [
                'required',
                'integer',
                Rule::exists('rombels', 'id')
                    ->where(fn ($query) => $query->where('tahun_pelajaran_id', $period->id)),
            ],
            'erapor_ref_curriculum_id' => [
                'required',
                'integer',
                Rule::exists('erapor_ref_curricula', 'id')
                    ->where(fn ($query) => $query->where('is_active', true)),
            ],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
        $setting = EraporPeriodSetting::query()->firstOrCreate(
            ['tahun_pelajaran_id' => $period->id],
            [
                'workflow_status' => 'setup',
                'configured_by' => $request->user()->id,
                'configured_at' => now(),
            ]
        );

        EraporRombelCurriculum::query()->updateOrCreate(
            ['rombel_id' => $validated['rombel_id']],
            [
                'erapor_period_setting_id' => $setting->id,
                'erapor_ref_curriculum_id' => $validated['erapor_ref_curriculum_id'],
                'configured_by' => $request->user()->id,
                'configured_at' => now(),
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return back()->with('success', 'Kurikulum rombel berhasil disimpan.');
    }

    public function destroyRombelCurriculum(EraporRombelCurriculum $rombelCurriculum): RedirectResponse
    {
        abort_unless(
            $rombelCurriculum->periodSetting?->academicPeriod?->is_active,
            404
        );

        $rombelCurriculum->delete();

        return back()->with('success', 'Kurikulum rombel berhasil dilepas.');
    }

    public function curriculumOptions(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('q'));
        $selectedId = $request->integer('selected_id') ?: null;
        $options = EraporRefCurriculum::query()
            ->where('is_active', true)
            ->where('education_level_id', 6)
            ->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('external_id', 'like', "%{$search}%")
                    ->orWhere('major_external_id', 'like', "%{$search}%");
            }))
            ->orderByDesc('valid_from')
            ->orderBy('name')
            ->limit(30)
            ->get(['id', 'external_id', 'name', 'major_external_id']);

        if ($selectedId && ! $options->contains('id', $selectedId)) {
            $selected = EraporRefCurriculum::query()
                ->find($selectedId, ['id', 'external_id', 'name', 'major_external_id']);

            if ($selected) {
                $options->prepend($selected);
            }
        }

        return response()->json([
            'data' => $options->map(fn (EraporRefCurriculum $curriculum) => [
                'id' => (string) $curriculum->id,
                'label' => "{$curriculum->name} [{$curriculum->external_id}]",
                'meta' => $curriculum->major_external_id
                    ? "Jurusan {$curriculum->major_external_id}"
                    : 'Umum',
            ])->values(),
        ]);
    }

    /**
     * @return array<string, int|bool>
     */
    private function emptyReadiness(): array
    {
        return [
            'rombels' => 0,
            'configured_rombels' => 0,
            'unconfigured_rombels' => 0,
            'assignments' => 0,
            'unmapped_assignments' => 0,
            'assignments_without_passing_grade' => 0,
            'can_open_assessment' => false,
        ];
    }
}
