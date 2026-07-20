<?php

namespace Tests\Feature;

use App\Models\StudentRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentRegistrationSchoolOriginsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::findOrCreate('Waka Kesiswaan', 'web');
        $this->user = User::factory()->create();
        $this->user->assignRole($role);
    }

    public function test_can_access_school_origins_page(): void
    {
        StudentRegistration::create([
            'nama_lengkap' => 'Siswa A',
            'sekolah_asal' => 'SMP Negeri 1 Pesawaran',
            'source' => 'public',
            'status' => 'pending',
            'tanggal_lahir' => '2010-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Alamat A',
            'nomor_hp' => '08123456789',
        ]);

        StudentRegistration::create([
            'nama_lengkap' => 'Siswa B',
            'sekolah_asal' => 'SMPN 1 Pesawaran',
            'source' => 'public',
            'status' => 'pending',
            'tanggal_lahir' => '2010-02-02',
            'jenis_kelamin' => 'P',
            'alamat' => 'Alamat B',
            'nomor_hp' => '08123456790',
        ]);

        StudentRegistration::create([
            'nama_lengkap' => 'Siswa C',
            'sekolah_asal' => 'SMPN1Pesrawaran',
            'source' => 'public',
            'status' => 'pending',
            'tanggal_lahir' => '2010-03-03',
            'jenis_kelamin' => 'L',
            'alamat' => 'Alamat C',
            'nomor_hp' => '08123456791',
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['active_role' => 'Waka Kesiswaan'])
            ->get(route('master-data.student-registration.school-origins'));

        $response->assertStatus(200);
        $response->assertSee('SMP Negeri 1 Pesawaran');
        $response->assertSee('SMPN 1 Pesawaran');
        $response->assertSee('SMPN1Pesrawaran');
        $response->assertSee('3 Pendaftar');
    }

    public function test_can_standardize_school_origins(): void
    {
        StudentRegistration::create([
            'nama_lengkap' => 'Siswa A',
            'sekolah_asal' => 'SMP Negeri 1 Pesawaran',
            'source' => 'public',
            'status' => 'pending',
            'tanggal_lahir' => '2010-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Alamat A',
            'nomor_hp' => '08123456789',
        ]);

        StudentRegistration::create([
            'nama_lengkap' => 'Siswa B',
            'sekolah_asal' => 'SMPN 1 Pesawaran',
            'source' => 'public',
            'status' => 'pending',
            'tanggal_lahir' => '2010-02-02',
            'jenis_kelamin' => 'P',
            'alamat' => 'Alamat B',
            'nomor_hp' => '08123456790',
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['active_role' => 'Waka Kesiswaan'])
            ->post(route('master-data.student-registration.update-school-origins'), [
                'original_names' => ['SMP Negeri 1 Pesawaran', 'SMPN 1 Pesawaran'],
                'standardized_name' => 'SMP Negeri 1 Pesawaran',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check if database is updated
        $this->assertEquals(2, StudentRegistration::where('sekolah_asal', 'SMP Negeri 1 Pesawaran')->count());
        $this->assertEquals(0, StudentRegistration::where('sekolah_asal', 'SMPN 1 Pesawaran')->count());
    }
}
