<?php

namespace App\Http\Controllers\Erapor;

use App\Http\Controllers\Controller;
use App\Models\Erapor\EraporTeachingAssignment;
use App\Models\TahunPelajaran;
use App\Services\Erapor\EraporAssignmentSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    public function index(Request $request): View
    {
        $period = TahunPelajaran::query()->where('is_active', true)->first();
        $search = trim((string) $request->query('search'));

        $assignments = EraporTeachingAssignment::query()
            ->with([
                'academicPeriod',
                'rombel.kelas',
                'subject',
                'teacher',
                'subjectMapping.referenceSubject',
            ])
            ->when($period, fn ($query) => $query->where('tahun_pelajaran_id', $period->id))
            ->when($request->boolean('inactive'), fn ($query) => $query->where('is_active', false),
                fn ($query) => $query->where('is_active', true))
            ->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                $query->whereHas('subject', fn ($query) => $query->where('nama_mapel', 'like', "%{$search}%"))
                    ->orWhereHas('teacher', fn ($query) => $query->where('nama_lengkap', 'like', "%{$search}%"))
                    ->orWhereHas('rombel.kelas', fn ($query) => $query->where('nama_kelas', 'like', "%{$search}%"));
            }))
            ->latest('last_synced_at')
            ->paginate(25)
            ->withQueryString();

        return view('pages.erapor.assignments', [
            'period' => $period,
            'assignments' => $assignments,
            'stats' => $period ? [
                'active' => EraporTeachingAssignment::query()
                    ->where('tahun_pelajaran_id', $period->id)->where('is_active', true)->count(),
                'mapped' => EraporTeachingAssignment::query()
                    ->where('tahun_pelajaran_id', $period->id)->where('is_active', true)
                    ->whereNotNull('erapor_subject_mapping_id')->count(),
                'unmapped' => EraporTeachingAssignment::query()
                    ->where('tahun_pelajaran_id', $period->id)->where('is_active', true)
                    ->whereNull('erapor_subject_mapping_id')->count(),
                'inactive' => EraporTeachingAssignment::query()
                    ->where('tahun_pelajaran_id', $period->id)->where('is_active', false)->count(),
            ] : ['active' => 0, 'mapped' => 0, 'unmapped' => 0, 'inactive' => 0],
        ]);
    }

    public function sync(EraporAssignmentSyncService $syncService): RedirectResponse
    {
        $period = TahunPelajaran::query()->where('is_active', true)->first();

        if (! $period) {
            return back()->with('error', 'Tahun pelajaran aktif belum tersedia.');
        }

        $stats = $syncService->sync($period);

        return back()->with(
            'success',
            "Sinkronisasi selesai: {$stats['created']} baru, {$stats['updated']} diperbarui, ".
            "{$stats['deactivated']} dinonaktifkan."
        );
    }

    public function update(Request $request, EraporTeachingAssignment $assignment): RedirectResponse
    {
        $validated = $request->validate([
            'subject_group' => ['nullable', 'string', 'max:40'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'passing_grade' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $assignment->update($validated);

        return back()->with('success', 'Konfigurasi penugasan e-Rapor berhasil disimpan.');
    }
}
