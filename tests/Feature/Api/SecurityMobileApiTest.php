<?php

namespace Tests\Feature\Api;

use App\Models\MasterSiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SecurityMobileApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_can_scan_and_record_student_lateness_once_per_day(): void
    {
        Role::create(['name' => 'Security']);
        $security = User::factory()->create();
        $security->assignRole('Security');
        $student = MasterSiswa::create([
            'nis' => '12345678',
            'nama_lengkap' => 'Andi Sujatmiko',
            'jenis_kelamin' => 'L',
            'status' => 'aktif',
        ]);

        $this->actingAs($security)
            ->postJson('/api/security/students/scan', ['code' => $student->nis])
            ->assertOk()
            ->assertJsonPath('data.name', 'Andi Sujatmiko')
            ->assertJsonPath('data.already_late_today', false);

        $payload = [
            'master_siswa_id' => $student->id,
            'reason' => 'Kendaraan mengalami kendala.',
        ];

        $this->actingAs($security)
            ->postJson('/api/security/lateness', $payload)
            ->assertCreated()
            ->assertJsonPath('data.student.nis', '12345678');

        $this->actingAs($security)
            ->postJson('/api/security/lateness', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('master_siswa_id');

        $this->assertDatabaseCount('keterlambatans', 1);
    }

    public function test_non_security_user_cannot_access_security_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/api/security/dashboard')
            ->assertForbidden();
    }
}
