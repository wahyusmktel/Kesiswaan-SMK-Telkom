<?php

namespace Tests\Feature;

use App\Models\OkrKeyResult;
use App\Models\OkrPeriod;
use App\Models\OkrPlan;
use App\Models\OkrUnit;
use Database\Seeders\ItOkrProgramSeeder;
use Database\Seeders\SchoolOkrObjectiveSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItOkrProgramSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_imports_the_it_program_hierarchy_and_reuses_legacy_data_idempotently(): void
    {
        OkrPeriod::create([
            'title' => 'Program Kerja OKR 2026/2027',
            'starts_at' => '2026-07-01',
            'ends_at' => '2027-06-30',
            'status' => 'active',
        ]);
        $this->seed(SchoolOkrObjectiveSeeder::class);

        $unit = OkrUnit::query()->firstOrCreate([
            'code' => 'IT',
        ], [
            'name' => 'IT',
            'role_names' => ['IT', 'Super Admin'],
            'sort_order' => 9,
            'is_active' => true,
        ]);
        $legacy = OkrPlan::create([
            'okr_key_result_id' => OkrKeyResult::where('code', 'KR 1.1')->value('id'),
            'okr_unit_id' => $unit->id,
            'level' => 'annual',
            'title' => 'Perawatan dan keandalan jaringan CCTV',
            'target_value' => 100,
            'metric_unit' => '%',
            'weight' => 1,
            'progress_percent' => 0,
            'status' => 'not_started',
        ]);

        $this->seed(ItOkrProgramSeeder::class);
        $this->seed(ItOkrProgramSeeder::class);

        $unit->refresh();
        $this->assertSame(11, OkrPlan::where('okr_unit_id', $unit->id)->where('level', 'annual')->count());
        $this->assertSame(22, OkrPlan::where('okr_unit_id', $unit->id)->where('level', 'monthly')->count());
        $this->assertSame(88, OkrPlan::where('okr_unit_id', $unit->id)->where('level', 'weekly')->count());

        $legacy->refresh();
        $this->assertSame('KR-IT.6 · Perawatan dan keandalan jaringan CCTV', $legacy->title);
        $this->assertSame('KR 4.2', $legacy->keyResult->code);
        $this->assertSame(25.0, (float) $legacy->progress_percent);

        $completedMonth = OkrPlan::query()
            ->where('okr_unit_id', $unit->id)
            ->where('level', 'monthly')
            ->where('title', 'like', 'Jun 2026 · KR-IT.5%')
            ->firstOrFail();
        $this->assertSame('completed', $completedMonth->status);
        $this->assertSame(100.0, (float) $completedMonth->progress_percent);
        $this->assertStringContainsString('Preventive maintenance jaringan telah dilaksanakan', $completedMonth->latest_evaluation);

        $weekly = OkrPlan::query()
            ->where('okr_unit_id', $unit->id)
            ->where('level', 'weekly')
            ->where('title', 'like', 'Agu 2026 · KR-IT.6 · Minggu 2%')
            ->firstOrFail();
        $this->assertSame('in_progress', $weekly->status);
        $this->assertSame(50.0, (float) $weekly->progress_percent);
        $this->assertStringContainsString('Pembersihan kamera', $weekly->latest_evaluation);

        $weekly->update(['progress_percent' => 73, 'status' => 'in_progress']);
        $this->seed(ItOkrProgramSeeder::class);
        $this->assertSame(73.0, (float) $weekly->fresh()->progress_percent);

        $mismatchedHierarchy = OkrPlan::query()
            ->with('parent:id,okr_key_result_id')
            ->where('okr_unit_id', $unit->id)
            ->whereNotNull('parent_id')
            ->get()
            ->filter(fn (OkrPlan $plan) => $plan->okr_key_result_id !== $plan->parent?->okr_key_result_id)
            ->count();
        $this->assertSame(0, $mismatchedHierarchy);
    }
}
