<?php

namespace App\Http\Controllers;

use App\Exceptions\OkrAiException;
use App\Models\AppSetting;
use App\Models\OkrKeyResult;
use App\Models\OkrObjective;
use App\Models\OkrPeriod;
use App\Models\OkrPlan;
use App\Models\OkrProgressUpdate;
use App\Models\OkrUnit;
use App\Models\TahunPelajaran;
use App\Models\User;
use App\Services\OkrAiService;
use App\Services\OkrProgressService;
use App\Services\OkrTemplateService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OkrController extends Controller
{
    public function index(Request $request, OkrTemplateService $templates)
    {
        $activePeriod = $templates->ensureForActiveAcademicYear($request->user());
        $periods = OkrPeriod::with('academicYear')->latest('id')->get();
        $period = $periods->firstWhere('id', (int) $request->integer('period_id')) ?? $activePeriod;
        $units = OkrUnit::where('is_active', true)->orderBy('sort_order')->get();
        $editableUnitIds = $this->editableUnitIds($request->user(), $units);
        $selectedUnit = $units->firstWhere('id', (int) $request->integer('unit_id'))
            ?? $units->first(fn (OkrUnit $unit) => in_array($unit->id, $editableUnitIds, true))
            ?? $units->first();

        $period->load([
            'academicYear',
            'objectives.keyResults' => fn ($query) => $query->orderBy('sort_order'),
            'objectives.keyResults.plans' => fn ($query) => $query
                ->where('okr_unit_id', $selectedUnit?->id)
                ->whereNull('parent_id')
                ->with([
                    'owner:id,name',
                    'updates.user:id,name',
                    'children' => fn ($child) => $child->with([
                        'owner:id,name',
                        'updates.user:id,name',
                        'children' => fn ($grandChild) => $grandChild->with(['owner:id,name', 'updates.user:id,name']),
                    ])->orderBy('starts_at'),
                ])
                ->orderBy('starts_at'),
        ]);

        $selectedPlans = $this->plansForPeriod($period)
            ->where('okr_unit_id', $selectedUnit?->id)
            ->with(['keyResult.objective', 'owner:id,name'])
            ->get();
        $topPlans = $selectedPlans->whereNull('parent_id');

        $stats = [
            'progress' => round((float) $topPlans->avg('progress_percent'), 1),
            'total' => $selectedPlans->count(),
            'completed' => $selectedPlans->where('status', 'completed')->count(),
            'at_risk' => $selectedPlans->where('status', 'at_risk')->count(),
        ];

        $unitStats = $units->map(function (OkrUnit $unit) use ($period) {
            $plans = $this->plansForPeriod($period)
                ->where('okr_unit_id', $unit->id)
                ->whereNull('parent_id')
                ->get();

            return [
                'id' => $unit->id,
                'name' => $unit->name,
                'progress' => round((float) $plans->avg('progress_percent'), 1),
                'plans' => $plans->count(),
            ];
        });

        $monthlyTrend = collect(range(5, 0))->map(function (int $offset) use ($period, $selectedUnit) {
            $month = now()->subMonths($offset);
            $updates = OkrProgressUpdate::whereHas('plan.keyResult.objective', fn ($query) => $query->where('okr_period_id', $period->id))
                ->whereHas('plan', fn ($query) => $query->where('okr_unit_id', $selectedUnit?->id))
                ->whereYear('recorded_at', $month->year)
                ->whereMonth('recorded_at', $month->month)
                ->get();

            return [
                'label' => $month->translatedFormat('M Y'),
                'progress' => round((float) $updates->avg('progress_after'), 1),
            ];
        });

        $recentUpdates = OkrProgressUpdate::with(['plan.unit', 'plan.keyResult', 'user:id,name'])
            ->whereHas('plan.keyResult.objective', fn ($query) => $query->where('okr_period_id', $period->id))
            ->latest('recorded_at')
            ->latest('id')
            ->limit(8)
            ->get();

        $owners = User::whereDoesntHave('roles', fn ($query) => $query->whereRaw('LOWER(name) = ?', ['siswa']))
            ->orderBy('name')
            ->get(['id', 'name']);
        $setting = AppSetting::first();

        return view('pages.okr.index', [
            'period' => $period,
            'periods' => $periods,
            'units' => $units,
            'selectedUnit' => $selectedUnit,
            'editableUnitIds' => $editableUnitIds,
            'canManageAll' => $this->canManageAll($request->user()),
            'canEditSelected' => $selectedUnit && in_array($selectedUnit->id, $editableUnitIds, true),
            'stats' => $stats,
            'unitStats' => $unitStats,
            'monthlyTrend' => $monthlyTrend,
            'recentUpdates' => $recentUpdates,
            'owners' => $owners,
            'aiReady' => (bool) ($setting?->stella_ai_enabled && $setting->stella_ai_api_key && $setting->stella_ai_chat_model),
            'academicYears' => TahunPelajaran::orderByDesc('tahun')->orderBy('semester')->get(),
        ]);
    }

    public function storePeriod(Request $request): RedirectResponse
    {
        $this->ensureManager($request->user());
        $validated = $request->validate([
            'tahun_pelajaran_id' => ['nullable', 'exists:tahun_pelajaran,id'],
            'title' => ['required', 'string', 'max:255'],
            'vision' => ['nullable', 'string', 'max:3000'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'status' => ['required', Rule::in(['draft', 'active', 'closed'])],
        ]);

        if ($validated['status'] === 'active') {
            OkrPeriod::where('status', 'active')->update(['status' => 'closed']);
        }

        $period = OkrPeriod::create($validated + ['created_by' => Auth::id()]);

        return redirect()->route('okr.index', ['period_id' => $period->id])
            ->with('success', 'Periode OKR berhasil dibuat.');
    }

    public function updatePeriod(Request $request, OkrPeriod $period): RedirectResponse
    {
        $this->ensureManager($request->user());
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'vision' => ['nullable', 'string', 'max:3000'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'status' => ['required', Rule::in(['draft', 'active', 'closed'])],
        ]);

        if ($validated['status'] === 'active') {
            OkrPeriod::where('id', '!=', $period->id)->where('status', 'active')->update(['status' => 'closed']);
        }
        $period->update($validated);

        return back()->with('success', 'Periode OKR berhasil diperbarui.');
    }

    public function storeObjective(Request $request): RedirectResponse
    {
        $this->ensureManager($request->user());
        $validated = $request->validate([
            'okr_period_id' => ['required', 'exists:okr_periods,id'],
            'code' => ['required', 'string', 'max:30'],
            'title' => ['required', 'string', 'max:2000'],
        ]);
        $validated['sort_order'] = OkrObjective::where('okr_period_id', $validated['okr_period_id'])->max('sort_order') + 1;
        OkrObjective::create($validated);

        return back()->with('success', 'Objektif sekolah berhasil ditambahkan.');
    }

    public function updateObjective(Request $request, OkrObjective $objective): RedirectResponse
    {
        $this->ensureManager($request->user());
        $objective->update($request->validate([
            'code' => ['required', 'string', 'max:30'],
            'title' => ['required', 'string', 'max:2000'],
        ]));

        return back()->with('success', 'Objektif sekolah berhasil diperbarui.');
    }

    public function destroyObjective(Request $request, OkrObjective $objective): RedirectResponse
    {
        $this->ensureManager($request->user());
        $objective->delete();

        return back()->with('success', 'Objektif sekolah dihapus.');
    }

    public function storeKeyResult(Request $request): RedirectResponse
    {
        $this->ensureManager($request->user());
        $validated = $this->validateKeyResult($request);
        $validated['sort_order'] = OkrKeyResult::where('okr_objective_id', $validated['okr_objective_id'])->max('sort_order') + 1;
        OkrKeyResult::create($validated);

        return back()->with('success', 'Key result berhasil ditambahkan.');
    }

    public function updateKeyResult(Request $request, OkrKeyResult $keyResult): RedirectResponse
    {
        $this->ensureManager($request->user());
        $keyResult->update($this->validateKeyResult($request, false));

        return back()->with('success', 'Key result berhasil diperbarui.');
    }

    public function destroyKeyResult(Request $request, OkrKeyResult $keyResult): RedirectResponse
    {
        $this->ensureManager($request->user());
        $keyResult->delete();

        return back()->with('success', 'Key result dihapus.');
    }

    public function storePlan(Request $request, OkrProgressService $progress): RedirectResponse
    {
        $validated = $this->validatePlan($request);
        $unit = OkrUnit::findOrFail($validated['okr_unit_id']);
        $this->ensureUnitEditor($request->user(), $unit);
        $this->validatePlanParent($validated);

        $plan = OkrPlan::create($validated + [
            'progress_percent' => 0,
            'status' => 'not_started',
            'created_by' => $request->user()->id,
        ]);
        $progress->rollUp($plan->parent);

        return back()->with('success', 'Rencana OKR berhasil ditambahkan.');
    }

    public function updatePlan(Request $request, OkrPlan $plan, OkrProgressService $progress): RedirectResponse
    {
        $this->ensureUnitEditor($request->user(), $plan->unit);
        $validated = $this->validatePlan($request);
        abort_unless((int) $validated['okr_unit_id'] === $plan->okr_unit_id, 422);
        $this->validatePlanParent($validated, $plan);
        $oldParent = $plan->parent;
        $plan->update($validated);
        $progress->rollUp($oldParent);
        $progress->rollUp($plan->parent);

        return back()->with('success', 'Rencana OKR berhasil diperbarui.');
    }

    public function destroyPlan(Request $request, OkrPlan $plan, OkrProgressService $progress): RedirectResponse
    {
        $this->ensureUnitEditor($request->user(), $plan->unit);
        $parent = $plan->parent;
        $plan->delete();
        $progress->rollUp($parent);

        return back()->with('success', 'Rencana OKR beserta turunannya dihapus.');
    }

    public function updateProgress(
        Request $request,
        OkrPlan $plan,
        OkrProgressService $progress
    ): RedirectResponse {
        $this->ensureUnitEditor($request->user(), $plan->unit);
        $validated = $request->validate([
            'progress_percent' => ['required', 'numeric', 'between:0,100'],
            'current_value' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['not_started', 'in_progress', 'at_risk', 'completed', 'cancelled'])],
            'note' => ['required', 'string', 'max:4000'],
            'recorded_at' => ['required', 'date'],
            'evidence' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx', 'max:10240'],
        ]);

        $before = (float) $plan->progress_percent;
        $evidencePath = $request->file('evidence')?->store('okr-evidence', 'public');
        $progressPercent = $validated['status'] === 'completed' ? 100 : (float) $validated['progress_percent'];
        $currentValue = $validated['status'] === 'completed' && $plan->target_value !== null
            ? $plan->target_value
            : ($validated['current_value'] ?? $plan->current_value);

        $plan->fill([
            'progress_percent' => $progressPercent,
            'current_value' => $currentValue,
            'status' => $validated['status'],
            'latest_evaluation' => $validated['note'],
        ]);
        $plan->save();

        OkrProgressUpdate::create([
            'okr_plan_id' => $plan->id,
            'user_id' => $request->user()->id,
            'progress_before' => $before,
            'progress_after' => $progressPercent,
            'current_value' => $currentValue,
            'status' => $validated['status'],
            'note' => $validated['note'],
            'evidence_path' => $evidencePath,
            'recorded_at' => $validated['recorded_at'],
        ]);

        $progress->syncFromValue($plan);

        return back()->with('success', 'Evaluasi dan progres OKR berhasil disimpan.');
    }

    public function aiSuggest(Request $request, OkrAiService $ai): JsonResponse
    {
        $validated = $request->validate([
            'okr_key_result_id' => ['required', 'exists:okr_key_results,id'],
            'okr_unit_id' => ['required', 'exists:okr_units,id'],
            'level' => ['required', Rule::in(['annual', 'monthly', 'weekly'])],
            'context' => ['nullable', 'string', 'max:2000'],
        ]);
        $unit = OkrUnit::findOrFail($validated['okr_unit_id']);
        $this->ensureUnitEditor($request->user(), $unit);
        $keyResult = OkrKeyResult::with('objective')->findOrFail($validated['okr_key_result_id']);

        try {
            return response()->json([
                'suggestions' => $ai->suggest($keyResult, $unit, $validated['level'], $validated['context'] ?? null),
            ]);
        } catch (OkrAiException $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->httpStatus);
        }
    }

    public function report(Request $request, OkrPeriod $period)
    {
        $unit = $request->filled('unit_id') ? OkrUnit::findOrFail($request->integer('unit_id')) : null;
        $plans = $this->plansForPeriod($period)
            ->when($unit, fn ($query) => $query->where('okr_unit_id', $unit->id))
            ->with(['unit', 'owner:id,name', 'keyResult.objective', 'updates.user:id,name'])
            ->orderBy('okr_unit_id')
            ->orderByRaw("CASE level WHEN 'annual' THEN 1 WHEN 'monthly' THEN 2 ELSE 3 END")
            ->orderBy('starts_at')
            ->get();
        $topPlans = $plans->whereNull('parent_id');
        $summary = [
            'progress' => round((float) $topPlans->avg('progress_percent'), 1),
            'total' => $plans->count(),
            'completed' => $plans->where('status', 'completed')->count(),
            'at_risk' => $plans->where('status', 'at_risk')->count(),
        ];

        $pdf = Pdf::loadView('pdf.okr-report', [
            'period' => $period->load('academicYear'),
            'unit' => $unit,
            'plans' => $plans,
            'summary' => $summary,
            'schoolName' => AppSetting::first()?->school_name ?? config('app.name'),
            'generatedBy' => $request->user(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-okr-'.Str::slug($period->title).($unit ? '-'.Str::slug($unit->name) : '-global').'.pdf');
    }

    private function plansForPeriod(OkrPeriod $period): Builder
    {
        return OkrPlan::whereHas(
            'keyResult.objective',
            fn ($query) => $query->where('okr_period_id', $period->id)
        );
    }

    private function validateKeyResult(Request $request, bool $includeObjective = true): array
    {
        $rules = [
            'code' => ['required', 'string', 'max:30'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:3000'],
            'metric_type' => ['required', Rule::in(['percentage', 'number', 'currency', 'boolean'])],
            'baseline_value' => ['required', 'numeric', 'min:0'],
            'target_value' => ['required', 'numeric', 'min:0'],
            'metric_unit' => ['required', 'string', 'max:40'],
            'weight' => ['required', 'numeric', 'min:0.01', 'max:100'],
            'due_date' => ['nullable', 'date'],
        ];
        if ($includeObjective) {
            $rules['okr_objective_id'] = ['required', 'exists:okr_objectives,id'];
        }

        return $request->validate($rules);
    }

    private function validatePlan(Request $request): array
    {
        return $request->validate([
            'okr_key_result_id' => ['required', 'exists:okr_key_results,id'],
            'okr_unit_id' => ['required', 'exists:okr_units,id'],
            'parent_id' => ['nullable', 'exists:okr_plans,id'],
            'owner_id' => ['nullable', 'exists:users,id'],
            'level' => ['required', Rule::in(['annual', 'monthly', 'weekly'])],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:3000'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'target_value' => ['nullable', 'numeric', 'min:0'],
            'metric_unit' => ['nullable', 'string', 'max:40'],
            'weight' => ['required', 'numeric', 'min:0.01', 'max:100'],
            'success_indicator' => ['nullable', 'string', 'max:3000'],
        ]);
    }

    private function validatePlanParent(array $validated, ?OkrPlan $currentPlan = null): void
    {
        $expectedParentLevel = ['annual' => null, 'monthly' => 'annual', 'weekly' => 'monthly'][$validated['level']];
        if ($expectedParentLevel === null) {
            abort_if(filled($validated['parent_id'] ?? null), 422, 'Target tahunan tidak boleh memiliki induk.');

            return;
        }

        $parent = OkrPlan::find($validated['parent_id'] ?? null);
        abort_unless(
            $parent
            && $parent->level === $expectedParentLevel
            && $parent->okr_key_result_id === (int) $validated['okr_key_result_id']
            && $parent->okr_unit_id === (int) $validated['okr_unit_id']
            && $parent->id !== $currentPlan?->id,
            422,
            'Induk target tidak sesuai dengan tingkat, unit, atau key result.'
        );
    }

    private function editableUnitIds(User $user, $units): array
    {
        if ($this->canManageAll($user)) {
            return $units->pluck('id')->map(fn ($id) => (int) $id)->all();
        }

        $activeRole = session('active_role') ?: $user->getRoleNames()->first();

        return $units
            ->filter(fn (OkrUnit $unit) => in_array($activeRole, $unit->role_names ?? [], true))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    private function canManageAll(User $user): bool
    {
        $activeRole = session('active_role') ?: $user->getRoleNames()->first();

        return in_array($activeRole, ['Super Admin', 'Kepala Sekolah'], true);
    }

    private function ensureManager(User $user): void
    {
        abort_unless($this->canManageAll($user), 403);
    }

    private function ensureUnitEditor(User $user, OkrUnit $unit): void
    {
        abort_unless(
            $this->canManageAll($user)
            || in_array(session('active_role') ?: $user->getRoleNames()->first(), $unit->role_names ?? [], true),
            403
        );
    }
}
