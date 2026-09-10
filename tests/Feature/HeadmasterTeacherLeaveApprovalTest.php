<?php

namespace Tests\Feature;

use App\Models\GuruIzin;
use App\Models\MasterGuru;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HeadmasterTeacherLeaveApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        $this->withoutVite();
        Notification::fake();
    }

    private function userWithRole(string $roleName, array $permissions = []): User
    {
        $user = User::factory()->create();
        $role = Role::findOrCreate($roleName, 'web');
        foreach ($permissions as $permission) {
            $role->givePermissionTo(Permission::findOrCreate($permission, 'web'));
        }
        $user->assignRole($role);

        return $user;
    }

    private function leave(string $employment, string $category = 'luar'): GuruIzin
    {
        $teacherUser = User::factory()->create();
        $teacher = MasterGuru::create(['nama_lengkap' => 'Guru '.$employment, 'jenis_kelamin' => 'L', 'user_id' => $teacherUser->id]);
        $teacher->dapodikGuru()->create(['nama' => $teacher->nama_lengkap, 'status_kepegawaian' => $employment]);

        return GuruIzin::create([
            'master_guru_id' => $teacher->id, 'tanggal_mulai' => '2026-09-08 06:00:00', 'tanggal_selesai' => '2026-09-08 16:00:00',
            'jenis_izin' => 'Sakit', 'kategori_penyetujuan' => $category, 'deskripsi' => 'Istirahat',
            'status_piket' => 'disetujui', 'status_kurikulum' => 'disetujui', 'status_sdm' => 'menunggu',
        ]);
    }

    public function test_permanent_employee_requires_headmaster_after_sdm_approval(): void
    {
        $headmaster = $this->userWithRole('Kepala Sekolah', ['view executive dashboard']);
        $sdm = $this->userWithRole('KAUR SDM', ['view sdm dashboard', 'manage perizinan guru']);
        $leave = $this->leave('Pegawai Tetap');

        $this->actingAs($sdm)->withSession(['active_role' => 'KAUR SDM'])
            ->patch(route('sdm.persetujuan-izin-guru.approve', $leave))->assertRedirect()->assertSessionHas('success');
        $this->assertSame('disetujui', $leave->refresh()->status_sdm);
        $this->assertSame('menunggu', $leave->status_kepala_sekolah);
        $this->assertFalse($leave->isFullyApproved());
        $this->assertSame(0, GuruIzin::fullyApproved()->whereKey($leave)->count());
        Notification::assertSentTo($headmaster, \App\Notifications\PengajuanIzinGuruNotification::class);

        $this->actingAs($headmaster)->withSession(['active_role' => 'Kepala Sekolah'])
            ->get(route('kepala-sekolah.persetujuan-izin-guru.index'))->assertOk()->assertSee($leave->guru->nama_lengkap)
            ->assertSee('Persetujuan Izin Pegawai');
        $this->patch(route('kepala-sekolah.persetujuan-izin-guru.approve', $leave))->assertRedirect()->assertSessionHas('success');
        $this->assertSame('disetujui', $leave->refresh()->status_kepala_sekolah);
        $this->assertTrue($leave->isFullyApproved());
        $this->assertSame(1, GuruIzin::fullyApproved()->whereKey($leave)->count());
        $this->assertSame($headmaster->id, $leave->kepala_sekolah_id);
    }

    public function test_teacher_can_submit_absence_directly_to_sdm(): void
    {
        $teacherUser = $this->userWithRole('Guru Kelas');
        $teacher = MasterGuru::create([
            'nama_lengkap' => 'Guru Tidak Masuk',
            'jenis_kelamin' => 'L',
            'user_id' => $teacherUser->id,
        ]);
        $teacher->dapodikGuru()->create([
            'nama' => $teacher->nama_lengkap,
            'status_kepegawaian' => 'Pegawai Full Time',
        ]);
        $this->userWithRole('KAUR SDM');

        $this->actingAs($teacherUser)->withSession(['active_role' => 'Guru Kelas'])
            ->post(route('guru.izin.store'), [
                'tanggal_mulai' => '2026-09-12 07:00:00',
                'tanggal_selesai' => '2026-09-12 16:00:00',
                'jenis_izin' => 'Sakit',
                'kategori_penyetujuan' => 'tidak_masuk',
                'deskripsi' => 'Perlu beristirahat di rumah.',
            ])->assertRedirect(route('guru.izin.index'));

        $this->assertDatabaseHas('guru_izins', [
            'master_guru_id' => $teacher->id,
            'kategori_penyetujuan' => 'tidak_masuk',
            'status_piket' => 'disetujui',
            'status_kurikulum' => 'disetujui',
            'status_sdm' => 'menunggu',
        ]);
    }

    public function test_school_category_finishes_at_piket_even_for_permanent_employee(): void
    {
        $headmaster = $this->userWithRole('Kepala Sekolah', ['view executive dashboard']);
        $piket = $this->userWithRole('Guru Piket');
        $leave = $this->leave('Pegawai Tetap');
        $leave->update(['kategori_penyetujuan' => 'sekolah', 'status_piket' => 'menunggu', 'status_kurikulum' => 'menunggu']);

        $this->actingAs($piket)->withSession(['active_role' => 'Guru Piket'])
            ->patch(route('piket.persetujuan-izin-guru.approve', $leave))->assertRedirect()->assertSessionHas('success');
        $leave->refresh();
        $this->assertSame('disetujui', $leave->status_sdm);
        $this->assertSame('tidak_diperlukan', $leave->status_kepala_sekolah);
        $this->assertTrue($leave->isFullyApproved());
        Notification::assertNotSentTo($headmaster, \App\Notifications\PengajuanIzinGuruNotification::class);
    }

    public function test_absence_category_starts_at_sdm_and_only_permanent_employee_continues_to_headmaster(): void
    {
        $headmaster = $this->userWithRole('Kepala Sekolah', ['view executive dashboard']);
        $sdm = $this->userWithRole('KAUR SDM', ['view sdm dashboard', 'manage perizinan guru']);

        $permanent = $this->leave('Pegawai Tetap', 'tidak_masuk');
        $fullTime = $this->leave('Pegawai Full Time', 'tidak_masuk');

        foreach ([$permanent, $fullTime] as $leave) {
            $this->actingAs($sdm)->withSession(['active_role' => 'KAUR SDM'])
                ->patch(route('sdm.persetujuan-izin-guru.approve', $leave))->assertRedirect();
        }

        $this->assertSame('menunggu', $permanent->refresh()->status_kepala_sekolah);
        $this->assertSame('tidak_diperlukan', $fullTime->refresh()->status_kepala_sekolah);
        $this->assertFalse($permanent->isFullyApproved());
        $this->assertTrue($fullTime->isFullyApproved());
        Notification::assertSentTo($headmaster, \App\Notifications\PengajuanIzinGuruNotification::class);
    }

    public function test_non_permanent_employee_finishes_at_sdm_and_cannot_enter_headmaster_action(): void
    {
        $headmaster = $this->userWithRole('Kepala Sekolah', ['view executive dashboard']);
        $sdm = $this->userWithRole('KAUR SDM', ['view sdm dashboard', 'manage perizinan guru']);
        $leave = $this->leave('Pegawai Full Time');
        $this->actingAs($sdm)->withSession(['active_role' => 'KAUR SDM'])
            ->patch(route('sdm.persetujuan-izin-guru.approve', $leave))->assertRedirect();
        $this->assertSame('tidak_diperlukan', $leave->refresh()->status_kepala_sekolah);
        $this->assertTrue($leave->isFullyApproved());

        $leave->update(['status_kepala_sekolah' => 'menunggu']);
        $this->actingAs($headmaster)->withSession(['active_role' => 'Kepala Sekolah'])
            ->patch(route('kepala-sekolah.persetujuan-izin-guru.approve', $leave))->assertStatus(409);
    }

    public function test_headmaster_can_reject_with_mandatory_note_and_other_roles_are_forbidden(): void
    {
        $headmaster = $this->userWithRole('Kepala Sekolah', ['view executive dashboard']);
        $leave = $this->leave('Pegawai Tetap');
        $leave->update(['status_sdm' => 'disetujui', 'status_kepala_sekolah' => 'menunggu']);
        $this->actingAs($headmaster)->withSession(['active_role' => 'Kepala Sekolah']);
        $this->patch(route('kepala-sekolah.persetujuan-izin-guru.reject', $leave), [])->assertSessionHasErrors('catatan_kepala_sekolah');
        $this->patch(route('kepala-sekolah.persetujuan-izin-guru.reject', $leave), ['catatan_kepala_sekolah' => 'Dokumen belum lengkap'])->assertRedirect();
        $this->assertSame('ditolak', $leave->refresh()->status_kepala_sekolah);
        $this->assertSame('Dokumen belum lengkap', $leave->catatan_kepala_sekolah);

        $teacher = $this->userWithRole('Guru Kelas');
        $this->actingAs($teacher)->withSession(['active_role' => 'Guru Kelas'])
            ->get(route('kepala-sekolah.persetujuan-izin-guru.index'))->assertForbidden();
    }
}
