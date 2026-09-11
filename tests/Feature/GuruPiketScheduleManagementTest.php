<?php

namespace Tests\Feature;

use App\Models\GuruPiketSchedule;
use App\Models\MasterGuru;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GuruPiketScheduleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.key' => 'base64:'.base64_encode(str_repeat('p', 32))]);
        $this->withoutVite();
    }

    public function test_superadmin_can_assign_exactly_two_active_class_teachers_for_each_weekday(): void
    {
        $admin = $this->userWithRole('Super Admin');
        $teachers = collect(range(1, 4))->map(fn (int $number) => $this->classTeacher('Guru '.$number));
        $schedule = $this->weeklySchedule($teachers->pluck('id')->all());

        $this->actingAs($admin)->withSession(['active_role' => 'Super Admin'])
            ->put(route('guru-piket-schedules.update'), ['schedules' => $schedule])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseCount('guru_piket_schedules', 10);
        $this->assertDatabaseHas('guru_piket_schedules', [
            'weekday' => 'Senin',
            'slot' => 1,
            'user_id' => $teachers[0]->id,
        ]);
        $this->assertDatabaseHas('guru_piket_schedules', [
            'weekday' => 'Jumat',
            'slot' => 2,
            'user_id' => $teachers[1]->id,
        ]);

        $schedule['Senin'] = [$teachers[1]->id, $teachers[0]->id];
        $this->put(route('guru-piket-schedules.update'), ['schedules' => $schedule])
            ->assertSessionHas('success');
        $this->assertDatabaseHas('guru_piket_schedules', [
            'weekday' => 'Senin',
            'slot' => 1,
            'user_id' => $teachers[1]->id,
        ]);
    }

    public function test_kaur_sdm_can_view_and_update_the_schedule(): void
    {
        $sdm = $this->userWithRole('KAUR SDM');
        $teachers = collect(range(1, 3))->map(fn (int $number) => $this->classTeacher('Guru SDM '.$number));

        $this->actingAs($sdm)->withSession(['active_role' => 'KAUR SDM'])
            ->get(route('guru-piket-schedules.index'))
            ->assertOk()
            ->assertSee('Jadwal Tugas Guru Piket')
            ->assertSee($teachers[0]->masterGuru->nama_lengkap);

        $this->put(route('guru-piket-schedules.update'), [
            'schedules' => $this->weeklySchedule($teachers->pluck('id')->all()),
        ])->assertSessionHas('success');

        $this->assertDatabaseCount('guru_piket_schedules', 10);
    }

    public function test_same_teacher_cannot_fill_both_slots_and_non_class_teacher_cannot_be_selected(): void
    {
        $admin = $this->userWithRole('Super Admin');
        $teacher = $this->classTeacher('Guru Sah');
        $otherRole = $this->userWithRole('Operator');
        MasterGuru::create([
            'nama_lengkap' => 'Bukan Guru Kelas',
            'jenis_kelamin' => 'L',
            'user_id' => $otherRole->id,
        ]);

        $duplicate = array_fill_keys(GuruPiketSchedule::WEEKDAYS, [$teacher->id, $teacher->id]);
        $this->actingAs($admin)->withSession(['active_role' => 'Super Admin'])
            ->put(route('guru-piket-schedules.update'), ['schedules' => $duplicate])
            ->assertSessionHasErrors('schedules.Senin.0');

        $secondTeacher = $this->classTeacher('Guru Sah Kedua');
        $invalidRole = array_fill_keys(GuruPiketSchedule::WEEKDAYS, [$teacher->id, $secondTeacher->id]);
        $invalidRole['Jumat'][1] = $otherRole->id;
        $this->put(route('guru-piket-schedules.update'), ['schedules' => $invalidRole])
            ->assertSessionHasErrors('schedules');

        $this->assertDatabaseCount('guru_piket_schedules', 0);
    }

    public function test_other_roles_cannot_manage_the_schedule(): void
    {
        $operator = $this->userWithRole('Operator');

        $this->actingAs($operator)->withSession(['active_role' => 'Operator'])
            ->get(route('guru-piket-schedules.index'))
            ->assertForbidden();
    }

    private function classTeacher(string $name): User
    {
        $user = $this->userWithRole('Guru Kelas', $name);
        MasterGuru::create([
            'nama_lengkap' => $name,
            'jenis_kelamin' => 'L',
            'user_id' => $user->id,
            'employee_category' => MasterGuru::CATEGORY_TEACHER,
        ]);

        return $user->load('masterGuru');
    }

    private function userWithRole(string $roleName, ?string $name = null): User
    {
        $user = User::factory()->create(['name' => $name ?? $roleName]);
        $user->assignRole(Role::findOrCreate($roleName, 'web'));

        return $user;
    }

    private function weeklySchedule(array $teacherIds): array
    {
        $count = count($teacherIds);

        return collect(GuruPiketSchedule::WEEKDAYS)->mapWithKeys(
            fn (string $day, int $index) => [$day => [
                $teacherIds[$index % $count],
                $teacherIds[($index + 1) % $count],
            ]]
        )->all();
    }
}
