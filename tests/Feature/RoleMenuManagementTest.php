<?php

namespace Tests\Feature;

use App\Models\RoleMenuSetting;
use App\Models\User;
use App\Services\RoleMenuService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleMenuManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.key' => 'base64:'.base64_encode(str_repeat('r', 32))]);
        $this->withoutVite();
    }

    public function test_only_super_admin_can_open_role_menu_management(): void
    {
        $admin = $this->userWithRole('Super Admin');
        $teacher = $this->userWithRole('Guru Kelas');

        $this->actingAs($teacher)->withSession(['active_role' => 'Guru Kelas'])
            ->get(route('super-admin.role-menus.index'))
            ->assertForbidden();

        $this->actingAs($admin)->withSession(['active_role' => 'Super Admin'])
            ->get(route('super-admin.role-menus.index', ['role' => $admin->roles->first()->id]))
            ->assertOk()
            ->assertSee('Pengaturan Menu per Role')
            ->assertSee('Dashboard Super Admin')
            ->assertSee('Pengaturan Menu');
    }

    public function test_super_admin_can_hide_and_reorder_a_role_menu_without_removing_its_route(): void
    {
        $admin = $this->userWithRole('Super Admin');
        $role = $admin->roles->first();

        $response = $this->actingAs($admin)->withSession(['active_role' => 'Super Admin'])
            ->put(route('super-admin.role-menus.update', $role), [
                'menus' => [
                    ['key' => 'menu-utama-dashboard-super-admin', 'visible' => 0, 'order' => 1],
                    ['key' => 'menu-utama-pengaturan-menu', 'visible' => 0, 'order' => 0],
                ],
            ]);

        $response->assertRedirect(route('super-admin.role-menus.index', ['role' => $role->id]));
        $this->assertDatabaseHas('role_menu_settings', [
            'role_id' => $role->id,
            'menu_key' => 'menu-utama-dashboard-super-admin',
            'is_visible' => false,
            'sort_order' => 1,
        ]);
        $this->assertDatabaseHas('role_menu_settings', [
            'role_id' => $role->id,
            'menu_key' => 'menu-utama-pengaturan-menu',
            'is_visible' => true,
        ]);
        $this->assertFalse(app(RoleMenuService::class)->settingsForRole($role)['menu-utama-dashboard-super-admin']['visible']);

        // Hiding the menu is intentionally not authorization: the original page remains reachable.
        $this->get(route('super-admin.dashboard.index'))
            ->assertOk()
            ->assertSee('menu-utama-dashboard-super-admin');
    }

    public function test_super_admin_can_restore_the_default_menu(): void
    {
        $admin = $this->userWithRole('Super Admin');
        $role = $admin->roles->first();
        RoleMenuSetting::create([
            'role_id' => $role->id,
            'menu_key' => 'menu-utama-dashboard-super-admin',
            'is_visible' => false,
            'sort_order' => 5,
        ]);

        $this->actingAs($admin)->withSession(['active_role' => 'Super Admin'])
            ->delete(route('super-admin.role-menus.reset', $role))
            ->assertRedirect(route('super-admin.role-menus.index', ['role' => $role->id]));

        $this->assertDatabaseCount('role_menu_settings', 0);
    }

    private function userWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user->load('roles');
    }
}
