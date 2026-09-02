<?php

namespace Tests\Feature;

use App\Models\Keterlambatan;
use App\Models\MasterSiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PiketDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        $this->withoutVite();
    }

    public function test_dashboard_handles_student_without_a_linked_user_account(): void
    {
        $guruPiket = User::factory()->create();
        $security = User::factory()->create(['name' => 'Petugas Security']);

        $role = Role::findOrCreate('Guru Piket', 'web');
        Role::findOrCreate('Siswa', 'web');
        $permission = Permission::findOrCreate('view piket dashboard', 'web');
        $role->givePermissionTo($permission);
        $guruPiket->assignRole($role);

        $siswa = MasterSiswa::create([
            'nis' => '999001',
            'nama_lengkap' => 'Siswa Tanpa Akun',
            'jenis_kelamin' => 'L',
            'user_id' => null,
        ]);

        Keterlambatan::create([
            'master_siswa_id' => $siswa->id,
            'dicatat_oleh_security_id' => $security->id,
            'waktu_dicatat_security' => now(),
            'alasan_siswa' => 'Kendaraan bermasalah.',
            'status' => 'dicatat_security',
        ]);

        $this->actingAs($guruPiket)
            ->withSession(['active_role' => 'Guru Piket'])
            ->get(route('piket.dashboard.index'))
            ->assertOk()
            ->assertSee('Siswa Tanpa Akun')
            ->assertSee('Petugas Security');
    }
}
