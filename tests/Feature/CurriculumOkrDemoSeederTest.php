<?php

namespace Tests\Feature;

use App\Models\OkrPeriod;
use App\Models\OkrPlan;
use App\Models\OkrUnit;
use App\Models\OkrWeeklyReport;
use Database\Seeders\CurriculumOkrDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurriculumOkrDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_the_complete_curriculum_okr_and_weekly_demo_idempotently(): void
    {
        $period = OkrPeriod::create([
            'title' => 'OKR Sekolah 2026/2027',
            'starts_at' => '2026-07-01',
            'ends_at' => '2027-06-30',
            'status' => 'active',
        ]);

        $this->seed(CurriculumOkrDemoSeeder::class);
        $this->seed(CurriculumOkrDemoSeeder::class);

        $unit = OkrUnit::where('code', 'KURIKULUM')->firstOrFail();

        $this->assertSame(16, OkrPlan::where('okr_unit_id', $unit->id)->where('level', 'annual')->count());
        $this->assertSame(41, OkrPlan::where('okr_unit_id', $unit->id)->where('level', 'monthly')->count());
        $this->assertDatabaseHas('okr_plans', [
            'okr_unit_id' => $unit->id,
            'level' => 'monthly',
            'title' => 'KR.12-1 · Lulusan diterima di PTN, Kedinasan, atau Universitas Luar Negeri.',
            'target_value' => 20,
            'metric_unit' => '%',
        ]);

        $report = OkrWeeklyReport::with('items')
            ->where('okr_period_id', $period->id)
            ->where('okr_unit_id', $unit->id)
            ->whereDate('week_start', '2026-09-07')
            ->firstOrFail();

        $this->assertSame('submitted', $report->status);
        $this->assertSame('2026-09-11', $report->week_end->format('Y-m-d'));
        $this->assertCount(3, $report->items);
        $this->assertTrue($report->items->every(fn ($item) => $item->plan !== null));

        $plan = $report->items->first()->plan;
        $plan->update(['progress_percent' => 35, 'status' => 'in_progress']);
        $this->seed(CurriculumOkrDemoSeeder::class);

        $this->assertSame('35.00', $plan->fresh()->progress_percent);
        $this->assertSame('in_progress', $plan->fresh()->status);
    }

    public function test_rerunning_the_seeder_does_not_reopen_a_reviewed_report(): void
    {
        OkrPeriod::create([
            'title' => 'OKR Sekolah 2026/2027',
            'starts_at' => '2026-07-01',
            'ends_at' => '2027-06-30',
            'status' => 'active',
        ]);

        $this->seed(CurriculumOkrDemoSeeder::class);

        $report = OkrWeeklyReport::firstOrFail();
        $report->update([
            'status' => 'reviewed',
            'reviewed_at' => now(),
            'review_notes' => 'Catatan Kepala Sekolah tetap dipertahankan.',
        ]);

        $this->seed(CurriculumOkrDemoSeeder::class);

        $this->assertDatabaseHas('okr_weekly_reports', [
            'id' => $report->id,
            'status' => 'reviewed',
            'review_notes' => 'Catatan Kepala Sekolah tetap dipertahankan.',
        ]);
    }
}
