<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\MasterSiswa;
use App\Models\Rombel;
use App\Models\StudentMasterBook;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentMasterBookTest extends TestCase
{
    use RefreshDatabase;

    private User $homeroomTeacher;

    private Rombel $homeroom;

    private MasterSiswa $student;

    protected function setUp(): void
    {
        parent::setUp();

        $viewPermission = Permission::findOrCreate('view wali kelas dashboard', 'web');
        $managePermission = Permission::findOrCreate('manage buku induk', 'web');
        $role = Role::findOrCreate('Wali Kelas', 'web');
        $role->syncPermissions([$viewPermission, $managePermission]);

        $this->homeroomTeacher = User::factory()->create();
        $this->homeroomTeacher->assignRole($role);

        $year = TahunPelajaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);
        $class = Kelas::create([
            'nama_kelas' => 'X TKJ 1',
            'jurusan' => 'Teknik Komputer dan Jaringan',
        ]);
        $this->homeroom = Rombel::create([
            'tahun_ajaran' => '2026/2027',
            'tahun_pelajaran_id' => $year->id,
            'kelas_id' => $class->id,
            'wali_kelas_id' => $this->homeroomTeacher->id,
        ]);
        $this->student = MasterSiswa::create([
            'nis' => '260001',
            'nama_lengkap' => 'Siswa Binaan',
            'jenis_kelamin' => 'L',
            'status' => 'aktif',
        ]);
        $this->homeroom->siswa()->attach($this->student);
    }

    public function test_homeroom_teacher_can_open_and_update_own_students_master_book(): void
    {
        $this->asHomeroom()
            ->get(route('wali-kelas.buku-induk.edit', $this->student))
            ->assertOk()
            ->assertSee('Siswa Binaan');

        $this->asHomeroom()
            ->put(route('wali-kelas.buku-induk.update', $this->student), [
                'admission_date' => '2026-07-13',
                'admission_status' => 'Siswa baru',
                'previous_school' => 'SMP Telkom',
                'blood_type' => 'O',
                'student_status' => 'aktif',
                'homeroom_notes' => 'Perkembangan baik.',
                'mark_complete' => '1',
            ])
            ->assertRedirect();

        $book = StudentMasterBook::where('master_siswa_id', $this->student->id)->firstOrFail();
        $this->assertSame('SMP Telkom', $book->previous_school);
        $this->assertNotNull($book->completed_at);
    }

    public function test_homeroom_teacher_cannot_access_a_student_from_another_class(): void
    {
        $otherTeacher = User::factory()->create();
        $otherTeacher->assignRole('Wali Kelas');
        $otherClass = Kelas::create(['nama_kelas' => 'X TKJ 2', 'jurusan' => 'Teknik Komputer dan Jaringan']);
        $otherRombel = Rombel::create([
            'tahun_ajaran' => '2026/2027',
            'tahun_pelajaran_id' => $this->homeroom->tahun_pelajaran_id,
            'kelas_id' => $otherClass->id,
            'wali_kelas_id' => $otherTeacher->id,
        ]);
        $otherStudent = MasterSiswa::create([
            'nis' => '260002',
            'nama_lengkap' => 'Siswa Kelas Lain',
            'jenis_kelamin' => 'P',
            'status' => 'aktif',
        ]);
        $otherRombel->siswa()->attach($otherStudent);

        $this->asHomeroom()
            ->get(route('wali-kelas.buku-induk.edit', $otherStudent))
            ->assertForbidden();

        $this->asHomeroom()
            ->get(route('wali-kelas.buku-induk.print-student', $otherStudent))
            ->assertForbidden();
    }

    public function test_semester_data_is_updated_without_creating_a_duplicate_period(): void
    {
        $payload = [
            'school_year' => '2026/2027',
            'semester' => 'Ganjil',
            'grades' => [['subject' => 'Matematika', 'score' => 88.75]],
            'extracurriculars' => [['name' => 'Pramuka', 'predicate' => 'Baik', 'description' => 'Aktif']],
            'sick_days' => 1,
            'permitted_days' => 2,
            'absent_days' => 0,
            'conduct' => 'Sangat Baik',
        ];

        $this->asHomeroom()->post(route('wali-kelas.buku-induk.periods.store', $this->student), $payload)->assertRedirect();
        $payload['grades'][0]['score'] = 90;
        $this->asHomeroom()->post(route('wali-kelas.buku-induk.periods.store', $this->student), $payload)->assertRedirect();

        $book = StudentMasterBook::where('master_siswa_id', $this->student->id)->firstOrFail();
        $this->assertCount(1, $book->periods);
        $this->assertSame(90, (int) $book->periods->first()->grades[0]['score']);
    }

    private function asHomeroom()
    {
        return $this->actingAs($this->homeroomTeacher)
            ->withSession(['active_role' => 'Wali Kelas']);
    }
}
