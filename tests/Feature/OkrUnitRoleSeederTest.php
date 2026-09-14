<?php

namespace Tests\Feature;

use App\Models\OkrKeyResult;
use App\Models\OkrObjective;
use App\Models\OkrPeriod;
use App\Models\OkrUnit;
use App\Models\User;
use App\Support\DashboardRedirector;
use Database\Seeders\OkrUnitRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OkrUnitRoleSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_dedicated_editor_role_for_every_okr_unit(): void
    {
        $this->seed(OkrUnitRoleSeeder::class);
        $this->seed(OkrUnitRoleSeeder::class);

        foreach (['QMR', 'Kurikulum', 'Kesiswaan', 'Hubin Sinergi UP dan Alumni', 'Hubin PPDB', 'Human Capital', 'Keuangan', 'Sarana dan Prasarana', 'IT', 'KAUR LAB'] as $role) {
            $this->assertDatabaseHas('roles', ['name' => $role, 'guard_name' => 'web']);
        }

        $this->assertDatabaseCount('okr_units', 10);
        $this->assertContains('IT', OkrUnit::where('code', 'IT')->firstOrFail()->role_names);
        $this->assertContains('Sarana dan Prasarana', OkrUnit::where('code', 'SARPRAS')->firstOrFail()->role_names);
        $this->assertContains('KAUR LAB', OkrUnit::where('code', 'LAB')->firstOrFail()->role_names);
        $this->assertSame('okr.index', DashboardRedirector::routeNameForRole('KAUR LAB'));
    }

    public function test_dedicated_unit_role_can_create_its_weekly_plan(): void
    {
        $this->seed(OkrUnitRoleSeeder::class);
        $period = OkrPeriod::create([
            'title' => 'OKR Sekolah 2026/2027',
            'starts_at' => '2026-07-01',
            'ends_at' => '2027-06-30',
            'status' => 'active',
        ]);
        $unit = OkrUnit::where('code', 'IT')->firstOrFail();
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole(Role::findByName('IT'));

        $this->actingAs($user)
            ->withSession(['active_role' => 'IT'])
            ->post(route('okr.weekly.planning'), [
                'okr_period_id' => $period->id,
                'okr_unit_id' => $unit->id,
                'week_start' => '2026-09-14',
                'weekly_focus' => 'Stabilitas layanan SISFO.',
                'items' => [[
                    'commitment' => 'Audit layanan utama.',
                    'measurable_target' => 'Seluruh layanan diperiksa Jumat.',
                ]],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('okr_weekly_reports', [
            'okr_unit_id' => $unit->id,
            'status' => 'draft',
        ]);
    }

    public function test_kaur_lab_can_manage_lab_okr_and_headmaster_can_monitor_it(): void
    {
        $this->seed(OkrUnitRoleSeeder::class);
        $period = OkrPeriod::create([
            'title' => 'OKR Sekolah 2026/2027',
            'starts_at' => '2026-07-01',
            'ends_at' => '2027-06-30',
            'status' => 'active',
        ]);
        $objective = OkrObjective::create([
            'okr_period_id' => $period->id,
            'code' => 'O-LAB',
            'title' => 'Meningkatkan kualitas layanan laboratorium',
            'sort_order' => 1,
        ]);
        $keyResult = OkrKeyResult::create([
            'okr_objective_id' => $objective->id,
            'code' => 'KR-LAB-1',
            'title' => 'Kesiapan perangkat laboratorium',
            'description' => 'Seluruh perangkat laboratorium siap digunakan.',
            'metric_type' => 'percentage',
            'baseline_value' => 0,
            'target_value' => 100,
            'metric_unit' => '%',
            'weight' => 1,
            'sort_order' => 1,
        ]);
        $unit = OkrUnit::where('code', 'LAB')->firstOrFail();
        $kaurLab = User::factory()->create(['email_verified_at' => now()]);
        $kaurLab->assignRole(Role::findByName('KAUR LAB'));

        $this->actingAs($kaurLab)
            ->withSession(['active_role' => 'KAUR LAB'])
            ->post(route('okr.plans.store'), [
                'okr_key_result_id' => $keyResult->id,
                'okr_unit_id' => $unit->id,
                'level' => 'annual',
                'title' => 'Menjamin kesiapan seluruh laboratorium',
                'target_value' => 100,
                'metric_unit' => '%',
                'weight' => 1,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('okr_plans', [
            'okr_unit_id' => $unit->id,
            'okr_key_result_id' => $keyResult->id,
            'title' => 'Menjamin kesiapan seluruh laboratorium',
        ]);

        $headmaster = User::factory()->create(['email_verified_at' => now()]);
        $headmaster->assignRole(Role::findOrCreate('Kepala Sekolah', 'web'));

        $this->actingAs($headmaster)
            ->withSession(['active_role' => 'Kepala Sekolah'])
            ->get(route('okr.index', ['period_id' => $period->id, 'unit_id' => $unit->id]))
            ->assertOk()
            ->assertSee('Laboratorium')
            ->assertSee('Menjamin kesiapan seluruh laboratorium');
    }
}
