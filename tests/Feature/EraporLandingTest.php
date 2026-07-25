<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MasterGuru;
use App\Models\MasterSiswa;
use App\Models\MataPelajaran;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Models\User;
use App\Services\Erapor\EraporReadinessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class EraporLandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('erapor.index'))
            ->assertRedirect(route('login'));
    }

    public function test_user_without_permission_cannot_open_erapor(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('erapor.index'))
            ->assertForbidden();
    }

    public function test_authorized_user_sees_blocked_readiness_when_active_period_is_missing(): void
    {
        $user = $this->authorizedUser();

        $this->actingAs($user)
            ->get(route('erapor.index'))
            ->assertOk()
            ->assertSee('e-Rapor SISFO')
            ->assertSee('Belum siap')
            ->assertSee('Belum ada tahun pelajaran/semester yang ditandai aktif.');
    }

    public function test_schedule_readiness_counts_multiple_slots_as_one_assignment(): void
    {
        $user = $this->authorizedUser();
        $teacherUser = User::factory()->create();
        $teacher = MasterGuru::create([
            'nama_lengkap' => 'Guru Produktif',
            'jenis_kelamin' => 'L',
            'user_id' => $teacherUser->id,
        ]);
        $period = TahunPelajaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);
        $class = Kelas::create([
            'nama_kelas' => 'XII TJKT 1',
            'jurusan' => 'Teknik Jaringan Komputer dan Telekomunikasi',
        ]);
        $rombel = Rombel::create([
            'tahun_ajaran' => '2026/2027',
            'tahun_pelajaran_id' => $period->id,
            'kelas_id' => $class->id,
            'wali_kelas_id' => $teacherUser->id,
        ]);
        $student = MasterSiswa::create([
            'nis' => '26001',
            'nama_lengkap' => 'Siswa Pilot',
            'tempat_lahir' => 'Bandar Lampung',
            'tanggal_lahir' => '2009-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Pendidikan',
            'status' => 'aktif',
        ]);
        $rombel->siswa()->attach($student->id);
        $subject = MataPelajaran::create([
            'kode_mapel' => 'PK-01',
            'nama_mapel' => 'Konsentrasi Keahlian',
        ]);

        foreach ([1, 2] as $lessonNumber) {
            JadwalPelajaran::create([
                'rombel_id' => $rombel->id,
                'mata_pelajaran_id' => $subject->id,
                'master_guru_id' => $teacher->id,
                'hari' => 'Senin',
                'jam_ke' => $lessonNumber,
                'jam_mulai' => sprintf('%02d:00:00', 7 + $lessonNumber),
                'jam_selesai' => sprintf('%02d:45:00', 7 + $lessonNumber),
            ]);
        }

        AppSetting::create([
            'school_name' => 'SMK Telkom Lampung',
            'address' => 'Jl. Pendidikan',
            'email' => 'sekolah@example.test',
            'phone' => '0721000000',
        ]);

        $readiness = app(EraporReadinessService::class)->inspect();

        $this->assertSame('warning', $readiness['overall_status']);
        $this->assertSame(1, $readiness['stats']['assignments']);
        $this->assertSame(1, $readiness['stats']['students']);
        $this->assertSame(1, $readiness['stats']['rombels']);

        $this->actingAs($user)
            ->get(route('erapor.index'))
            ->assertOk()
            ->assertSee('Perlu perhatian')
            ->assertSee('1 penugasan unik ditemukan');
    }

    private function authorizedUser(): User
    {
        $permission = Permission::findOrCreate('view erapor', 'web');
        $user = User::factory()->create();
        $user->givePermissionTo($permission);

        return $user;
    }
}
