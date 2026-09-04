<?php

namespace Tests\Feature;

use App\Models\OkrPeriod;
use App\Models\User;
use App\Support\DashboardRedirector;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HeadmasterDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        Cache::flush();
        $this->withoutVite();
    }

    public function test_headmaster_can_open_read_only_executive_dashboard(): void
    {
        $headmaster = $this->userWithRoleAndPermission('Kepala Sekolah', 'view executive dashboard');

        $this->actingAs($headmaster)
            ->withSession(['active_role' => 'Kepala Sekolah'])
            ->get(route('kepala-sekolah.dashboard.index'))
            ->assertOk()
            ->assertSee('Dashboard Eksekutif Kepala Sekolah')
            ->assertSee('Indikator Operasional')
            ->assertSee('Perkembangan Seluruh Unit')
            ->assertDontSee('Dashboard Super Admin');

        $this->assertSame('kepala-sekolah.dashboard.index', DashboardRedirector::routeNameForRole('Kepala Sekolah'));
    }

    public function test_other_roles_cannot_open_executive_dashboard(): void
    {
        $teacher = $this->userWithRoleAndPermission('Guru Kelas', 'view executive dashboard');

        $this->actingAs($teacher)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->get(route('kepala-sekolah.dashboard.index'))
            ->assertForbidden();
    }

    public function test_headmaster_cannot_access_super_admin_dashboard_or_change_okr(): void
    {
        $headmaster = $this->userWithRoleAndPermission('Kepala Sekolah', 'view executive dashboard');
        $period = OkrPeriod::create([
            'title' => 'OKR Tahun Berjalan',
            'status' => 'active',
            'created_by' => $headmaster->id,
        ]);

        $this->actingAs($headmaster)
            ->withSession(['active_role' => 'Kepala Sekolah'])
            ->get(route('super-admin.dashboard.index'))
            ->assertForbidden();

        $this->actingAs($headmaster)
            ->withSession(['active_role' => 'Kepala Sekolah'])
            ->get(route('admin.roles.index'))
            ->assertForbidden();

        $this->actingAs($headmaster)
            ->withSession(['active_role' => 'Kepala Sekolah'])
            ->get(route('okr.index'))
            ->assertOk()
            ->assertDontSee('Tambah Objektif');

        $this->actingAs($headmaster)
            ->withSession(['active_role' => 'Kepala Sekolah'])
            ->post(route('okr.objectives.store'), [
                'okr_period_id' => $period->id,
                'code' => 'O-TEST',
                'title' => 'Tidak boleh dibuat oleh viewer',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('okr_objectives', ['code' => 'O-TEST']);
    }

    public function test_permission_seeder_keeps_headmaster_away_from_administration_permissions(): void
    {
        $role = Role::findOrCreate('Kepala Sekolah', 'web');
        Permission::findOrCreate('manage permissions', 'web');
        $role->givePermissionTo('manage permissions');

        $this->seed(PermissionSeeder::class);

        $this->assertEqualsCanonicalizing(
            ['view erapor', 'view executive dashboard'],
            $role->fresh()->permissions->pluck('name')->all()
        );
    }

    private function userWithRoleAndPermission(string $roleName, string $permissionName): User
    {
        $role = Role::findOrCreate($roleName, 'web');
        $permission = Permission::findOrCreate($permissionName, 'web');
        $role->givePermissionTo($permission);

        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole($role);

        return $user;
    }
}
