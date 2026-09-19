<?php

namespace Tests\Feature;

use App\Models\MasterSiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterSiswaSoftDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_soft_delete_student_with_reason_and_notes(): void
    {
        $this->withoutMiddleware();
        $admin = User::factory()->create(['name' => 'Operator Siswa']);

        $studentUser = User::factory()->create(['name' => 'Siswa Test']);

        $student = MasterSiswa::create([
            'nama_lengkap' => 'Siswa Test',
            'nis' => '12345',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '2008-01-01',
            'status' => 'aktif',
            'user_id' => $studentUser->id,
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('master-data.siswa.destroy', $student->id), [
                'deletion_reason' => 'Pindah Sekolah',
                'deletion_notes' => 'Pindah ke SMKN 1 Surabaya ikut orang tua',
            ]);

        $response->assertRedirect(route('master-data.siswa.index', ['tab' => 'aktif']));

        // Assert soft delete
        $this->assertSoftDeleted('master_siswa', ['id' => $student->id]);

        $student->refresh();
        $this->assertEquals('keluar', $student->status);
        $this->assertEquals('Pindah Sekolah', $student->deletion_reason);
        $this->assertEquals('Pindah ke SMKN 1 Surabaya ikut orang tua', $student->deletion_notes);
        $this->assertEquals($admin->id, $student->deleted_by);

        // Assert linked user account is removed
        $this->assertDatabaseMissing('users', ['id' => $studentUser->id]);
    }

    public function test_index_filters_active_and_soft_deleted_students(): void
    {
        $this->withoutMiddleware();
        $admin = User::factory()->create();

        $activeStudent = MasterSiswa::create([
            'nama_lengkap' => 'Active Student',
            'nis' => '11111',
            'jenis_kelamin' => 'L',
            'status' => 'aktif',
        ]);

        $deletedStudent = MasterSiswa::create([
            'nama_lengkap' => 'Deleted Student',
            'nis' => '22222',
            'jenis_kelamin' => 'P',
            'status' => 'keluar',
            'deletion_reason' => 'Mengundurkan Diri',
            'deleted_by' => $admin->id,
        ]);
        $deletedStudent->delete();

        // Active tab
        $responseActive = $this->actingAs($admin)->get(route('master-data.siswa.index', ['tab' => 'aktif']));
        $responseActive->assertOk();
        $responseActive->assertSee('Active Student');
        $responseActive->assertDontSee('Deleted Student');

        // Keluar tab
        $responseKeluar = $this->actingAs($admin)->get(route('master-data.siswa.index', ['tab' => 'keluar']));
        $responseKeluar->assertOk();
        $responseKeluar->assertSee('Deleted Student');
        $responseKeluar->assertSee('Mengundurkan Diri');
        $responseKeluar->assertDontSee('Active Student');
    }

    public function test_can_restore_soft_deleted_student(): void
    {
        $this->withoutMiddleware();
        $admin = User::factory()->create();

        $deletedStudent = MasterSiswa::create([
            'nama_lengkap' => 'Student To Restore',
            'nis' => '33333',
            'jenis_kelamin' => 'L',
            'status' => 'keluar',
            'deletion_reason' => 'Salah Data',
            'deleted_by' => $admin->id,
        ]);
        $deletedStudent->delete();

        $this->assertSoftDeleted('master_siswa', ['id' => $deletedStudent->id]);

        $response = $this->actingAs($admin)
            ->patch(route('master-data.siswa.restore', $deletedStudent->id));

        $response->assertRedirect(route('master-data.siswa.index', ['tab' => 'keluar']));

        // Assert student is restored
        $this->assertNotSoftDeleted('master_siswa', ['id' => $deletedStudent->id]);

        $deletedStudent->refresh();
        $this->assertEquals('aktif', $deletedStudent->status);
        $this->assertNull($deletedStudent->deletion_reason);
        $this->assertNull($deletedStudent->deletion_notes);
        $this->assertNull($deletedStudent->deleted_by);
    }
}
