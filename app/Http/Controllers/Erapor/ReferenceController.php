<?php

namespace App\Http\Controllers\Erapor;

use App\Http\Controllers\Controller;
use App\Models\Erapor\EraporRefCurriculum;
use App\Models\Erapor\EraporReferenceImport;
use App\Models\Erapor\EraporRefSubject;
use App\Models\Erapor\EraporSubjectMapping;
use App\Models\Erapor\EraporTeachingAssignment;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReferenceController extends Controller
{
    public function index(Request $request): View
    {
        $localSearch = trim((string) $request->query('search'));
        $classId = $request->integer('kelas_id') ?: null;
        $level = strtoupper(trim((string) $request->query('tingkat')));
        $level = in_array($level, ['X', 'XI', 'XII'], true) ? $level : null;
        $mappingStatus = trim((string) $request->query('status_pemetaan'));
        $mappingStatus = in_array($mappingStatus, ['mapped', 'unmapped'], true) ? $mappingStatus : null;

        $subjects = MataPelajaran::query()
            ->with(['kelas', 'eraporMapping.referenceSubject'])
            ->withCount('jadwalPelajaran')
            ->when($classId, fn ($query) => $query->where('kelas_id', $classId))
            ->when($level, fn ($query) => $query->whereHas(
                'kelas',
                fn ($query) => $query->where('nama_kelas', 'like', $level.' %')
            ))
            ->when($mappingStatus === 'mapped', fn ($query) => $query->whereHas('eraporMapping'))
            ->when($mappingStatus === 'unmapped', fn ($query) => $query->whereDoesntHave('eraporMapping'))
            ->when($localSearch, fn ($query) => $query->where(function ($query) use ($localSearch) {
                $query->where('nama_mapel', 'like', "%{$localSearch}%")
                    ->orWhere('kode_mapel', 'like', "%{$localSearch}%");
            }))
            ->orderBy('nama_mapel')
            ->paginate(20)
            ->withQueryString();

        return view('pages.erapor.references', [
            'subjects' => $subjects,
            'imports' => EraporReferenceImport::query()
                ->with('importer:id,name')
                ->latest()
                ->limit(10)
                ->get(),
            'stats' => [
                'curricula' => EraporRefCurriculum::query()->count(),
                'reference_subjects' => EraporRefSubject::query()->count(),
                'active_reference_subjects' => EraporRefSubject::query()->where('is_active', true)->count(),
                'local_subjects' => MataPelajaran::query()->count(),
                'mapped_subjects' => EraporSubjectMapping::query()->count(),
            ],
            'classes' => Kelas::query()->orderBy('nama_kelas')->get(['id', 'nama_kelas']),
            'selectedClassId' => $classId,
            'selectedLevel' => $level,
            'selectedMappingStatus' => $mappingStatus,
        ]);
    }

    public function subjectOptions(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('q'));
        $selectedId = $request->integer('selected_id') ?: null;

        $options = EraporRefSubject::query()
            ->where('is_active', true)
            ->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('external_id', 'like', "%{$search}%");
            }))
            ->orderBy('name')
            ->limit(30)
            ->get(['id', 'external_id', 'name']);

        if ($selectedId && ! $options->contains('id', $selectedId)) {
            $selected = EraporRefSubject::query()
                ->find($selectedId, ['id', 'external_id', 'name']);

            if ($selected) {
                $options->prepend($selected);
            }
        }

        return response()->json([
            'data' => $options->map(fn (EraporRefSubject $subject) => [
                'id' => (string) $subject->id,
                'label' => "{$subject->name} [{$subject->external_id}]",
            ])->values(),
        ]);
    }

    public function storeMapping(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mata_pelajaran_id' => ['required', 'integer', 'exists:mata_pelajarans,id'],
            'erapor_ref_subject_id' => ['required', 'integer', 'exists:erapor_ref_subjects,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'apply_same_name' => ['nullable', 'boolean'],
        ]);

        $localSubject = MataPelajaran::query()->findOrFail($validated['mata_pelajaran_id']);
        $localSubjectIds = $request->boolean('apply_same_name')
            ? MataPelajaran::query()
                ->whereRaw('LOWER(TRIM(nama_mapel)) = ?', [mb_strtolower(trim($localSubject->nama_mapel))])
                ->pluck('id')
            : collect([$localSubject->id]);

        DB::transaction(function () use ($validated, $request, $localSubjectIds) {
            foreach ($localSubjectIds as $localSubjectId) {
                $mapping = EraporSubjectMapping::query()->updateOrCreate(
                    ['mata_pelajaran_id' => $localSubjectId],
                    [
                        'erapor_ref_subject_id' => $validated['erapor_ref_subject_id'],
                        'confidence' => 100,
                        'notes' => $validated['notes'] ?? null,
                        'mapped_by' => $request->user()->id,
                        'mapped_at' => now(),
                    ]
                );

                EraporTeachingAssignment::query()
                    ->where('mata_pelajaran_id', $localSubjectId)
                    ->update(['erapor_subject_mapping_id' => $mapping->id]);
            }
        });

        return back()->with(
            'success',
            $localSubjectIds->count().' mata pelajaran SISFO berhasil dipetakan.'
        );
    }

    public function destroyMapping(EraporSubjectMapping $mapping): RedirectResponse
    {
        $mapping->delete();

        return back()->with('success', 'Pemetaan mata pelajaran e-Rapor dilepas.');
    }
}
