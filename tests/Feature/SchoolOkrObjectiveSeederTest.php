<?php

namespace Tests\Feature;

use App\Models\OkrKeyResult;
use App\Models\OkrObjective;
use App\Models\OkrPeriod;
use Database\Seeders\SchoolOkrObjectiveSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolOkrObjectiveSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_imports_the_school_objectives_and_key_results_idempotently(): void
    {
        $period = OkrPeriod::create([
            'title' => 'OKR Sekolah 2026/2027',
            'vision' => 'Visi sekolah',
            'starts_at' => '2026-07-01',
            'ends_at' => '2027-06-30',
            'status' => 'active',
        ]);

        $this->seed(SchoolOkrObjectiveSeeder::class);
        $this->seed(SchoolOkrObjectiveSeeder::class);

        $this->assertSame(4, OkrObjective::where('okr_period_id', $period->id)->count());
        $this->assertSame(12, OkrKeyResult::whereHas('objective', fn ($query) => $query->where('okr_period_id', $period->id))->count());
        $this->assertDatabaseHas('okr_objectives', [
            'okr_period_id' => $period->id,
            'code' => 'O1',
            'title' => 'Lulusan Vokasi Unggul (Kompetensi & Relevansi Industri)',
        ]);
        $this->assertDatabaseHas('okr_key_results', [
            'code' => 'KR 1.1',
            'title' => 'Keterserapan Lulusan di Industri Relevan',
            'target_value' => 85,
            'metric_unit' => '%',
        ]);
        $this->assertDatabaseHas('okr_key_results', [
            'code' => 'KR 4.2',
            'target_value' => 8.5,
            'metric_unit' => 'skor (maks. 10)',
        ]);
        $this->assertSame('2027-06-30', OkrKeyResult::where('code', 'KR 4.2')->firstOrFail()->due_date->format('Y-m-d'));
    }
}
