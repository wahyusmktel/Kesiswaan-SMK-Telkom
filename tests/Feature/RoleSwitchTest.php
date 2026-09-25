<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleSwitchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.key' => 'base64:'.base64_encode(str_repeat('r', 32))]);
        $this->withoutVite();
    }

    public function test_user_can_switch_role_to_kaur_lab_and_redirect_to_okr_index(): void
    {
        Role::findOrCreate('Guru Kelas', 'web');
        Role::findOrCreate('Guru Piket', 'web');
        Role::findOrCreate('KAUR LAB', 'web');

        $user = User::factory()->create([
            'email' => 'imam@smktelkom-lpg.sch.id',
        ]);
        $user->assignRole(['Guru Kelas', 'Guru Piket', 'KAUR LAB']);

        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->post('/switch-role', [
                'role' => 'KAUR LAB',
            ]);

        $response->assertRedirect(route('okr.index'));
        $response->assertSessionHas('active_role', 'KAUR LAB');
    }

    public function test_user_cannot_switch_to_role_they_do_not_have(): void
    {
        Role::findOrCreate('Guru Kelas', 'web');
        Role::findOrCreate('Super Admin', 'web');

        $user = User::factory()->create();
        $user->assignRole('Guru Kelas');

        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->post('/switch-role', [
                'role' => 'Super Admin',
            ]);

        $response->assertSessionHas('error');
        $this->assertSame('Guru Kelas', session('active_role'));
    }
}
