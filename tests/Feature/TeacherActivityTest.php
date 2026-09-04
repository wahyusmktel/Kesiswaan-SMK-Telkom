<?php

namespace Tests\Feature;

use App\Models\MasterGuru;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TeacherActivityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        $this->withoutVite();
    }

    private function login(string $role): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate($role, 'web'));
        $this->actingAs($user)->withSession(['active_role' => $role]);
    }

    public function test_sdm_and_superadmin_can_manage_status_without_deleting_teacher_or_account(): void
    {
        $account = User::factory()->create();
        $teacher = MasterGuru::create(['nama_lengkap' => 'Guru Resign', 'jenis_kelamin' => 'L', 'user_id' => $account->id]);
        $this->assertTrue($teacher->refresh()->is_active);
        foreach (['KAUR SDM', 'Super Admin'] as $role) {
            $this->login($role);
            $this->get(route('teacher-activity.index'))->assertOk()->assertSee('Guru Resign')
                ->assertSee('href="'.route('teacher-activity.index').'"', false);
            $this->patch(route('teacher-activity.update', $teacher), ['is_active' => 0, 'nama_lengkap' => 'Tampered'])->assertRedirect()->assertSessionHas('success');
            $this->assertFalse($teacher->refresh()->is_active);
            $this->assertSame('Guru Resign', $teacher->nama_lengkap);
            $this->assertDatabaseHas('users', ['id' => $account->id]);
            $this->get(route('teacher-activity.index', ['status' => 'active']))->assertOk()->assertDontSee('Guru Resign');
            $this->get(route('teacher-activity.index', ['status' => 'inactive', 'search' => 'Resign']))->assertOk()->assertSee('Guru Resign');
            $this->patch(route('teacher-activity.update', $teacher), ['is_active' => 1])->assertRedirect();
            $this->assertTrue($teacher->refresh()->is_active);
            $this->patch(route('teacher-activity.update', $teacher), ['is_active' => 'invalid'])->assertSessionHasErrors('is_active');
        }
    }

    public function test_other_roles_and_guests_cannot_manage_status(): void
    {
        $teacher = MasterGuru::create(['nama_lengkap' => 'Guru Aktif', 'jenis_kelamin' => 'P']);
        $this->get(route('teacher-activity.index'))->assertRedirect(route('login'));
        $this->patch(route('teacher-activity.update', $teacher), ['is_active' => 0])->assertRedirect(route('login'));
        $this->patch(route('teacher-activity.employment.update', $teacher), ['status_kepegawaian' => 'Pegawai Tetap'])->assertRedirect(route('login'));
        foreach (['Kepala Sekolah', 'Guru Kelas', 'Operator', 'Kurikulum'] as $role) {
            $this->login($role);
            $this->get(route('teacher-activity.index'))->assertForbidden();
            $this->patch(route('teacher-activity.update', $teacher), ['is_active' => 0])->assertForbidden();
            $this->patch(route('teacher-activity.employment.update', $teacher), ['status_kepegawaian' => 'Pegawai Tetap'])->assertForbidden();
        }
        $this->assertTrue($teacher->refresh()->is_active);
    }

    public function test_employment_quick_action_updates_only_linked_employment_and_rejects_invalid_values(): void
    {
        $teacher = MasterGuru::create(['nama_lengkap' => 'Guru Test', 'jenis_kelamin' => 'L']);
        $dapodik = $teacher->dapodikGuru()->create(['nama' => 'Nama Dapodik', 'status_kepegawaian' => null]);
        $url = route('teacher-activity.employment.update', $teacher);
        foreach (['KAUR SDM', 'Super Admin'] as $role) {
            $this->login($role);
            $this->get(route('teacher-activity.index'))->assertOk()->assertSee('action="'.$url.'"', false);
            foreach (['Pegawai Tetap', 'Pegawai Full Time', 'Pegawai Part Time'] as $status) {
                $this->patch($url, ['status_kepegawaian' => $status, 'nama' => 'Tampered', 'is_active' => 0])
                    ->assertRedirect()->assertSessionHas('success');
                $this->assertSame($status, $dapodik->refresh()->status_kepegawaian);
                $this->assertSame('Nama Dapodik', $dapodik->nama);
                $this->assertTrue($teacher->refresh()->is_active);
            }
            foreach (['', 'Invalid', 'Security'] as $invalid) {
                $this->patch($url, ['status_kepegawaian' => $invalid])->assertSessionHasErrors('status_kepegawaian');
                $this->assertSame('Pegawai Part Time', $dapodik->refresh()->status_kepegawaian);
            }
        }
    }

    public function test_unlinked_teacher_requires_mapping_without_creating_duplicate_dapodik(): void
    {
        $this->login('KAUR SDM');
        $teacher = MasterGuru::create(['nama_lengkap' => 'Belum Mapping', 'jenis_kelamin' => 'P']);
        $this->get(route('teacher-activity.index'))->assertOk()->assertSee('Data Dapodik belum terhubung');
        $this->patch(route('teacher-activity.employment.update', $teacher), ['status_kepegawaian' => 'Pegawai Tetap'])
            ->assertSessionHasErrors('status_kepegawaian');
        $this->assertDatabaseCount('dapodik_gurus', 0);
    }
}
