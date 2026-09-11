<?php

namespace Tests\Feature;

use App\Models\OkrPeriod;
use App\Models\OkrUnit;
use App\Models\User;
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

        foreach (['QMR', 'Kurikulum', 'Kesiswaan', 'Hubin Sinergi UP dan Alumni', 'Hubin PPDB', 'Human Capital', 'Keuangan', 'Sarana dan Prasarana', 'IT'] as $role) {
            $this->assertDatabaseHas('roles', ['name' => $role, 'guard_name' => 'web']);
        }

        $this->assertDatabaseCount('okr_units', 9);
        $this->assertContains('IT', OkrUnit::where('code', 'IT')->firstOrFail()->role_names);
        $this->assertContains('Sarana dan Prasarana', OkrUnit::where('code', 'SARPRAS')->firstOrFail()->role_names);
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
}
