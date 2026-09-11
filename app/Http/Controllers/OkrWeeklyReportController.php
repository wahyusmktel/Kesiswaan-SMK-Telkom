<?php

namespace App\Http\Controllers;

use App\Models\OkrPeriod;
use App\Models\OkrPlan;
use App\Models\OkrUnit;
use App\Models\OkrWeeklyReport;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OkrWeeklyReportController extends Controller
{
    public function index(Request $request)
    {
        $periods = OkrPeriod::with('academicYear')->latest('id')->get();
        $period = $periods->firstWhere('id', (int) $request->integer('period_id'))
            ?? $periods->firstWhere('status', 'active')
            ?? $periods->first();
        abort_unless($period, 404, 'Periode OKR belum tersedia.');

        $units = OkrUnit::where('is_active', true)->orderBy('sort_order')->get();
        $editableUnitIds = $this->editableUnitIds($request->user(), $units);
        $selectedUnit = $units->firstWhere('id', (int) $request->integer('unit_id'))
            ?? $units->first(fn (OkrUnit $unit) => in_array($unit->id, $editableUnitIds, true))
            ?? $units->first();
        abort_unless($selectedUnit, 404, 'Unit OKR belum tersedia.');

        $weekStart = $this->weekStart($request->input('week_start'));
        $weekEnd = $weekStart->addDays(4);
        $reports = OkrWeeklyReport::with(['items.plan.keyResult', 'submitter:id,name', 'reviewer:id,name'])
            ->where('okr_period_id', $period->id)
            ->whereDate('week_start', $weekStart)
            ->get()
            ->keyBy('okr_unit_id');
        $report = $reports->get($selectedUnit->id);

        $scopeReports = $this->isExecutiveViewer($request->user())
            ? $reports->values()
            : $reports->only($editableUnitIds)->values();
        $scopeItems = $scopeReports->flatMap->items;

        $unitSummaries = $units->map(function (OkrUnit $unit) use ($reports) {
            $unitReport = $reports->get($unit->id);
            $items = $unitReport?->items ?? collect();

            return [
                'id' => $unit->id,
                'name' => $unit->name,
                'status' => $unitReport?->status ?? 'not_reported',
                'completion' => round((float) $items->avg('completion_percent'), 1),
                'completed' => $items->where('final_status', 'completed')->count(),
                'blocked' => $items->where('final_status', 'blocked')->count(),
                'submitter' => $unitReport?->submitter?->name,
                'submitted_at' => $unitReport?->submitted_at,
            ];
        });

        $trendWeeks = collect(range(7, 0))->map(fn (int $offset) => $weekStart->subWeeks($offset));
        $trendReports = OkrWeeklyReport::with('items')
            ->where('okr_period_id', $period->id)
            ->whereBetween('week_start', [$trendWeeks->first(), $trendWeeks->last()])
            ->when(
                ! $this->isExecutiveViewer($request->user()),
                fn ($query) => $query->whereIn('okr_unit_id', $editableUnitIds)
            )
            ->get()
            ->groupBy(fn (OkrWeeklyReport $weeklyReport) => $weeklyReport->week_start->format('Y-m-d'));
        $weeklyTrend = $trendWeeks->map(function (CarbonImmutable $week) use ($trendReports) {
            $items = $trendReports->get($week->format('Y-m-d'), collect())->flatMap->items;

            return [
                'label' => $week->translatedFormat('d M'),
                'completion' => round((float) $items->avg('completion_percent'), 1),
            ];
        });

        $availablePlans = OkrPlan::with('keyResult:id,code,title')
            ->where('okr_unit_id', $selectedUnit->id)
            ->whereHas('keyResult.objective', fn ($query) => $query->where('okr_period_id', $period->id))
            ->orderByRaw("CASE level WHEN 'annual' THEN 1 WHEN 'monthly' THEN 2 ELSE 3 END")
            ->orderBy('starts_at')
            ->get();

        return view('pages.okr.weekly.index', [
            'period' => $period,
            'periods' => $periods,
            'units' => $units,
            'selectedUnit' => $selectedUnit,
            'editableUnitIds' => $editableUnitIds,
            'canEditSelected' => in_array($selectedUnit->id, $editableUnitIds, true),
            'canReview' => $this->canReview($request->user()),
            'isExecutiveViewer' => $this->isExecutiveViewer($request->user()),
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'report' => $report,
            'availablePlans' => $availablePlans,
            'unitSummaries' => $unitSummaries,
            'weeklyTrend' => $weeklyTrend,
            'stats' => [
                'reported_units' => $scopeReports->whereIn('status', ['submitted', 'reviewed'])->count(),
                'expected_units' => $this->isExecutiveViewer($request->user()) ? $units->count() : count($editableUnitIds),
                'completion' => round((float) $scopeItems->avg('completion_percent'), 1),
                'completed' => $scopeItems->where('final_status', 'completed')->count(),
                'blocked' => $scopeItems->where('final_status', 'blocked')->count(),
            ],
        ]);
    }

    public function savePlanning(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'okr_period_id' => ['required', 'exists:okr_periods,id'],
            'okr_unit_id' => ['required', 'exists:okr_units,id'],
            'week_start' => ['required', 'date'],
            'weekly_focus' => ['required', 'string', 'max:3000'],
            'support_needed' => ['nullable', 'string', 'max:3000'],
            'items' => ['required', 'array', 'max:3'],
            'items.*.okr_plan_id' => ['nullable', 'integer', 'exists:okr_plans,id'],
            'items.*.commitment' => ['nullable', 'string', 'max:3000'],
            'items.*.measurable_target' => ['nullable', 'string', 'max:3000'],
            'items.*.cross_unit_dependencies' => ['nullable', 'string', 'max:2000'],
            'items.*.approval_needs' => ['nullable', 'string', 'max:2000'],
        ]);

        $period = OkrPeriod::findOrFail($validated['okr_period_id']);
        $unit = OkrUnit::findOrFail($validated['okr_unit_id']);
        $this->ensureUnitEditor($request->user(), $unit);
        $weekStart = $this->weekStart($validated['week_start']);
        $items = collect($validated['items'])
            ->filter(fn (array $item) => filled($item['commitment'] ?? null) || filled($item['measurable_target'] ?? null))
            ->values();

        if ($items->isEmpty()) {
            throw ValidationException::withMessages(['items' => 'Isi minimal satu komitmen target mingguan.']);
        }
        foreach ($items as $index => $item) {
            if (blank($item['commitment'] ?? null) || blank($item['measurable_target'] ?? null)) {
                throw ValidationException::withMessages(["items.{$index}.commitment" => 'Komitmen dan target terukur wajib diisi berpasangan.']);
            }
            $this->ensurePlanBelongsToScope($item['okr_plan_id'] ?? null, $unit, $period);
        }

        $report = DB::transaction(function () use ($request, $validated, $period, $unit, $weekStart, $items) {
            $report = OkrWeeklyReport::firstOrCreate(
                [
                    'okr_period_id' => $period->id,
                    'okr_unit_id' => $unit->id,
                    'week_start' => $weekStart,
                ],
                [
                    'week_end' => $weekStart->addDays(4),
                    'status' => 'draft',
                    'created_by' => $request->user()->id,
                ]
            );
            abort_if($report->status !== 'draft', 422, 'Laporan yang sudah dikirim tidak dapat mengubah rencana Senin.');
            $report->update([
                'week_end' => $weekStart->addDays(4),
                'weekly_focus' => $validated['weekly_focus'],
                'support_needed' => $validated['support_needed'] ?? null,
            ]);

            $orders = [];
            foreach ($items as $index => $item) {
                $order = $index + 1;
                $orders[] = $order;
                $report->items()->updateOrCreate(
                    ['priority_order' => $order],
                    [
                        'okr_plan_id' => $item['okr_plan_id'] ?? null,
                        'commitment' => $item['commitment'],
                        'measurable_target' => $item['measurable_target'],
                        'cross_unit_dependencies' => $item['cross_unit_dependencies'] ?? null,
                        'approval_needs' => $item['approval_needs'] ?? null,
                    ]
                );
            }
            $report->items()->whereNotIn('priority_order', $orders)->delete();

            return $report;
        });

        return redirect()->route('okr.weekly.index', [
            'period_id' => $period->id,
            'unit_id' => $unit->id,
            'week_start' => $weekStart->format('Y-m-d'),
        ])->with('success', 'Rencana rapat Senin berhasil disimpan.');
    }

    public function submitEvaluation(Request $request, OkrWeeklyReport $weeklyReport): RedirectResponse
    {
        $this->ensureUnitEditor($request->user(), $weeklyReport->unit);
        abort_if($weeklyReport->status === 'reviewed', 422, 'Laporan yang sudah ditinjau Kepala Sekolah tidak dapat diubah.');

        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.actual_result' => ['required', 'string', 'max:4000'],
            'items.*.completion_percent' => ['required', 'numeric', 'between:0,100'],
            'items.*.final_status' => ['required', Rule::in(['not_started', 'on_progress', 'completed', 'blocked'])],
            'items.*.blockers' => ['nullable', 'string', 'max:3000'],
            'items.*.next_follow_up' => ['nullable', 'string', 'max:3000'],
            'items.*.evidence' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx', 'max:10240'],
        ]);

        $weeklyReport->load('items');
        $expectedIds = $weeklyReport->items->pluck('id')->map(fn ($id) => (string) $id)->sort()->values();
        $submittedIds = collect(array_keys($validated['items']))->map(fn ($id) => (string) $id)->sort()->values();
        abort_unless($expectedIds->all() === $submittedIds->all(), 422, 'Item evaluasi tidak sesuai dengan rencana mingguan.');

        DB::transaction(function () use ($request, $validated, $weeklyReport) {
            foreach ($weeklyReport->items as $item) {
                $input = $validated['items'][$item->id];
                $status = $input['final_status'];
                $completion = $status === 'completed' ? 100 : ($status === 'not_started' ? 0 : $input['completion_percent']);
                $evidencePath = $request->file("items.{$item->id}.evidence")?->store('okr-weekly-evidence', 'public');
                $item->update([
                    'actual_result' => $input['actual_result'],
                    'completion_percent' => $completion,
                    'final_status' => $status,
                    'blockers' => $input['blockers'] ?? null,
                    'next_follow_up' => $input['next_follow_up'] ?? null,
                    'evidence_path' => $evidencePath ?: $item->evidence_path,
                ]);
            }
            $weeklyReport->update([
                'status' => 'submitted',
                'submitted_by' => $request->user()->id,
                'submitted_at' => now(),
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_notes' => null,
            ]);
        });

        return redirect()->route('okr.weekly.index', $this->reportQuery($weeklyReport))
            ->with('success', 'Evaluasi Jumat berhasil dikirim kepada Kepala Sekolah.');
    }

    public function review(Request $request, OkrWeeklyReport $weeklyReport): RedirectResponse
    {
        abort_unless($this->canReview($request->user()), 403);
        abort_unless($weeklyReport->status === 'submitted', 422, 'Hanya laporan yang sudah dikirim yang dapat ditinjau.');
        $validated = $request->validate([
            'review_notes' => ['nullable', 'string', 'max:4000'],
        ]);

        $weeklyReport->update([
            'status' => 'reviewed',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'review_notes' => $validated['review_notes'] ?? null,
        ]);

        return redirect()->route('okr.weekly.index', $this->reportQuery($weeklyReport))
            ->with('success', 'Laporan pekanan telah ditinjau.');
    }

    private function ensurePlanBelongsToScope(?int $planId, OkrUnit $unit, OkrPeriod $period): void
    {
        if (! $planId) {
            return;
        }

        abort_unless(
            OkrPlan::whereKey($planId)
                ->where('okr_unit_id', $unit->id)
                ->whereHas('keyResult.objective', fn ($query) => $query->where('okr_period_id', $period->id))
                ->exists(),
            422,
            'Target OKR yang dipilih tidak sesuai dengan unit atau periode.'
        );
    }

    private function weekStart(?string $date): CarbonImmutable
    {
        return CarbonImmutable::parse($date ?: now())->startOfWeek(CarbonInterface::MONDAY)->startOfDay();
    }

    private function editableUnitIds(User $user, $units): array
    {
        if ($this->isExecutiveViewer($user)) {
            return [];
        }
        if ($this->activeRole($user) === 'Super Admin') {
            return $units->pluck('id')->map(fn ($id) => (int) $id)->all();
        }

        $activeRole = $this->activeRole($user);

        return $units->filter(fn (OkrUnit $unit) => in_array($activeRole, $unit->role_names ?? [], true))
            ->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    private function ensureUnitEditor(User $user, OkrUnit $unit): void
    {
        abort_if($this->isExecutiveViewer($user), 403);
        abort_unless(
            $this->activeRole($user) === 'Super Admin'
            || in_array($this->activeRole($user), $unit->role_names ?? [], true),
            403
        );
    }

    private function canReview(User $user): bool
    {
        return in_array($this->activeRole($user), ['Super Admin', 'Kepala Sekolah'], true);
    }

    private function isExecutiveViewer(User $user): bool
    {
        return $this->activeRole($user) === 'Kepala Sekolah';
    }

    private function activeRole(User $user): string
    {
        return session('active_role') ?: (string) $user->getRoleNames()->first();
    }

    private function reportQuery(OkrWeeklyReport $report): array
    {
        return [
            'period_id' => $report->okr_period_id,
            'unit_id' => $report->okr_unit_id,
            'week_start' => $report->week_start->format('Y-m-d'),
        ];
    }
}
