<?php

namespace Tests\Feature;

use App\Models\Erapor\EraporRefSubject;
use App\Models\Erapor\EraporSubjectMapping;
use App\Models\Erapor\EraporTeachingAssignment;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MasterGuru;
use App\Models\MataPelajaran;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Models\User;
use App\Services\Erapor\EraporAssignmentSyncService;
use App\Services\Erapor\EraporReferenceImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class EraporFoundationTest extends TestCase
{
    use RefreshDatabase;

    private ?string $fixtureDirectory = null;

    protected function tearDown(): void
    {
        if ($this->fixtureDirectory && File::isDirectory($this->fixtureDirectory)) {
            File::deleteDirectory($this->fixtureDirectory);
        }

        parent::tearDown();
    }

    public function test_reference_import_is_versioned_and_idempotent(): void
    {
        $directory = $this->referenceFixture();
        $importer = app(EraporReferenceImportService::class);

        $first = $importer->import($directory, 'test-1');
        $second = $importer->import($directory, 'test-1');

        $this->assertCount(3, $first);
        $this->assertTrue(collect($second)->every(fn (array $result) => $result['unchanged']));
        $this->assertDatabaseCount('erapor_reference_imports', 3);
        $this->assertDatabaseCount('erapor_ref_subjects', 2);
        $this->assertDatabaseCount('erapor_ref_curricula', 1);
        $this->assertDatabaseCount('erapor_ref_curriculum_subjects', 2);
        $this->assertDatabaseHas('erapor_ref_subjects', [
            'external_id' => 101,
            'name' => 'Matematika',
            'is_active' => true,
        ]);
    }

    public function test_schedule_sync_creates_one_persistent_assignment_and_preserves_configuration(): void
    {
        [$period, $rombel, $subject, $teacher] = $this->scheduleContext();

        foreach ([1, 2] as $lessonNumber) {
            JadwalPelajaran::create([
                'rombel_id' => $rombel->id,
                'mata_pelajaran_id' => $subject->id,
                'master_guru_id' => $teacher->id,
                'hari' => 'Senin',
                'jam_ke' => $lessonNumber,
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '08:45:00',
            ]);
        }

        $service = app(EraporAssignmentSyncService::class);
        $first = $service->sync($period);
        $assignment = EraporTeachingAssignment::firstOrFail();
        $assignment->update(['passing_grade' => 78, 'subject_group' => 'C1']);
        $second = $service->sync($period);

        $this->assertSame(1, $first['created']);
        $this->assertSame(1, $second['updated']);
        $this->assertDatabaseCount('erapor_teaching_assignments', 1);
        $this->assertSame('78.00', $assignment->fresh()->passing_grade);
        $this->assertSame('C1', $assignment->fresh()->subject_group);
        $this->assertSame(2, $assignment->fresh()->sync_metadata['schedule_slot_count']);

        JadwalPelajaran::query()->delete();
        $third = $service->sync($period);

        $this->assertSame(1, $third['deactivated']);
        $this->assertFalse($assignment->fresh()->is_active);
    }

    public function test_configurator_can_map_local_subject_and_assignment_receives_mapping(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::findOrCreate('configure erapor', 'web'));
        $reference = EraporRefSubject::create([
            'external_id' => 101,
            'name' => 'Matematika',
            'is_active' => true,
        ]);
        [$period, $rombel, $subject, $teacher] = $this->scheduleContext();
        JadwalPelajaran::create([
            'rombel_id' => $rombel->id,
            'mata_pelajaran_id' => $subject->id,
            'master_guru_id' => $teacher->id,
            'hari' => 'Senin',
            'jam_ke' => 1,
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '08:45:00',
        ]);
        app(EraporAssignmentSyncService::class)->sync($period);
        MataPelajaran::create([
            'kode_mapel' => 'MTK-2',
            'nama_mapel' => 'Matematika',
        ]);

        $this->actingAs($user)
            ->get(route('erapor.references.index'))
            ->assertOk()
            ->assertSee('Referensi & Pemetaan e-Rapor', false);
        $this->actingAs($user)
            ->get(route('erapor.assignments.index'))
            ->assertOk()
            ->assertSee('Penugasan e-Rapor');

        $this->actingAs($user)
            ->post(route('erapor.mappings.store'), [
                'mata_pelajaran_id' => $subject->id,
                'erapor_ref_subject_id' => $reference->id,
                'apply_same_name' => true,
            ])
            ->assertRedirect();

        $mapping = EraporSubjectMapping::firstOrFail();
        $this->assertDatabaseCount('erapor_subject_mappings', 2);
        $this->assertDatabaseHas('erapor_teaching_assignments', [
            'mata_pelajaran_id' => $subject->id,
            'erapor_subject_mapping_id' => $mapping->id,
        ]);
    }

    private function referenceFixture(): string
    {
        $this->fixtureDirectory = storage_path('framework/testing/erapor-'.Str::uuid());
        File::ensureDirectoryExists($this->fixtureDirectory);

        File::put($this->fixtureDirectory.'/mata_pelajaran.json', json_encode([
            [
                'mata_pelajaran_id' => 101,
                'nama' => 'Matematika',
                'jurusan_id' => null,
                'create_date' => '2022-07-01 00:00:00',
                'last_update' => '2022-07-01 00:00:00',
                'expired_date' => null,
            ],
            [
                'mata_pelajaran_id' => 102,
                'nama' => 'Bahasa Indonesia',
                'jurusan_id' => null,
                'create_date' => '2022-07-01 00:00:00',
                'last_update' => '2022-07-01 00:00:00',
                'expired_date' => null,
            ],
        ], JSON_THROW_ON_ERROR));
        File::put($this->fixtureDirectory.'/kurikulum.json', json_encode([[
            'kurikulum_id' => 501,
            'nama_kurikulum' => 'Kurikulum SMK Uji',
            'jenjang_pendidikan_id' => 6,
            'jurusan_id' => null,
            'mulai_berlaku' => '2022-07-01',
            'last_update' => '2022-07-01 00:00:00',
            'expired_date' => null,
        ]], JSON_THROW_ON_ERROR));
        File::put($this->fixtureDirectory.'/mata_pelajaran_kurikulum-1.json', json_encode([
            [
                'kurikulum_id' => 501,
                'mata_pelajaran_id' => 101,
                'tingkat_pendidikan_id' => 10,
                'jumlah_jam' => 4,
                'jumlah_jam_maksimum' => 6,
                'status_di_kurikulum' => 1,
                'wajib' => 1,
                'expired_date' => null,
            ],
            [
                'kurikulum_id' => 501,
                'mata_pelajaran_id' => 102,
                'tingkat_pendidikan_id' => 10,
                'jumlah_jam' => 3,
                'jumlah_jam_maksimum' => 4,
                'status_di_kurikulum' => 1,
                'wajib' => 1,
                'expired_date' => null,
            ],
        ], JSON_THROW_ON_ERROR));

        return $this->fixtureDirectory;
    }

    /**
     * @return array{TahunPelajaran, Rombel, MataPelajaran, MasterGuru}
     */
    private function scheduleContext(): array
    {
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
        $class = Kelas::create(['nama_kelas' => 'X TJKT 1', 'jurusan' => 'TJKT']);
        $rombel = Rombel::create([
            'tahun_ajaran' => '2026/2027',
            'tahun_pelajaran_id' => $period->id,
            'kelas_id' => $class->id,
            'wali_kelas_id' => $teacherUser->id,
        ]);
        $subject = MataPelajaran::create([
            'kode_mapel' => 'MTK',
            'nama_mapel' => 'Matematika',
            'kelas_id' => $class->id,
        ]);

        return [$period, $rombel, $subject, $teacher];
    }
}
