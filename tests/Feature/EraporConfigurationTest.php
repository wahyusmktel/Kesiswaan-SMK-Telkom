<?php

namespace Tests\Feature;

use App\Models\Erapor\EraporPeriodSetting;
use App\Models\Erapor\EraporRefCurriculum;
use App\Models\Erapor\EraporRefSubject;
use App\Models\Erapor\EraporSubjectMapping;
use App\Models\Erapor\EraporTeachingAssignment;
use App\Models\Kelas;
use App\Models\MasterGuru;
use App\Models\MataPelajaran;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class EraporConfigurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_configurator_can_select_curriculum_for_active_period_rombel(): void
    {
        [$user, $period, $rombel, $curriculum] = $this->configurationContext();

        $this->actingAs($user)
            ->get(route('erapor.configuration.index', ['tingkat' => 'X', 'status' => 'unconfigured']))
            ->assertOk()
            ->assertSee('Konfigurasi e-Rapor')
            ->assertSee('X TJKT 1');

        $this->actingAs($user)
            ->getJson(route('erapor.configuration.curriculum-options', ['q' => 'Merdeka']))
            ->assertOk()
            ->assertJsonPath('data.0.id', (string) $curriculum->id)
            ->assertJsonPath('data.0.label', 'Kurikulum Merdeka SMK [501]');

        $this->actingAs($user)
            ->post(route('erapor.configuration.rombel-curricula.store'), [
                'rombel_id' => $rombel->id,
                'erapor_ref_curriculum_id' => $curriculum->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('erapor_period_settings', [
            'tahun_pelajaran_id' => $period->id,
            'workflow_status' => 'setup',
        ]);
        $this->assertDatabaseHas('erapor_rombel_curricula', [
            'rombel_id' => $rombel->id,
            'erapor_ref_curriculum_id' => $curriculum->id,
        ]);

        $this->actingAs($user)
            ->get(route('erapor.configuration.index', ['status' => 'configured']))
            ->assertOk()
            ->assertSee('Kurikulum Merdeka SMK');
    }

    public function test_assessment_cannot_open_until_assignment_mapping_and_passing_grade_are_complete(): void
    {
        [$user, $period, $rombel, $curriculum] = $this->configurationContext();

        $this->actingAs($user)->post(route('erapor.configuration.rombel-curricula.store'), [
            'rombel_id' => $rombel->id,
            'erapor_ref_curriculum_id' => $curriculum->id,
        ]);

        $this->actingAs($user)
            ->patch(route('erapor.configuration.period.update'), ['workflow_status' => 'assessment'])
            ->assertSessionHasErrors('workflow_status');

        $this->assertSame('setup', EraporPeriodSetting::firstOrFail()->workflow_status);

        $teacher = MasterGuru::create([
            'nama_lengkap' => 'Guru Produktif',
            'jenis_kelamin' => 'L',
            'user_id' => User::factory()->create()->id,
        ]);
        $subject = MataPelajaran::create([
            'kode_mapel' => 'MTK',
            'nama_mapel' => 'Matematika',
            'kelas_id' => $rombel->kelas_id,
        ]);
        $referenceSubject = EraporRefSubject::create([
            'external_id' => 101,
            'name' => 'Matematika',
            'is_active' => true,
        ]);
        $mapping = EraporSubjectMapping::create([
            'mata_pelajaran_id' => $subject->id,
            'erapor_ref_subject_id' => $referenceSubject->id,
            'mapped_by' => $user->id,
            'mapped_at' => now(),
        ]);
        EraporTeachingAssignment::create([
            'tahun_pelajaran_id' => $period->id,
            'rombel_id' => $rombel->id,
            'mata_pelajaran_id' => $subject->id,
            'master_guru_id' => $teacher->id,
            'erapor_subject_mapping_id' => $mapping->id,
            'passing_grade' => 75,
            'source' => 'schedule',
            'source_key' => 'test-assignment',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->patch(route('erapor.configuration.period.update'), [
                'workflow_status' => 'assessment',
                'score_entry_starts_at' => '2026-08-01T07:00',
                'score_entry_ends_at' => '2026-08-15T23:59',
                'report_date' => '2026-08-20',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('erapor_period_settings', [
            'tahun_pelajaran_id' => $period->id,
            'workflow_status' => 'assessment',
        ]);
        $this->assertSame('2026-08-20', EraporPeriodSetting::firstOrFail()->report_date->format('Y-m-d'));
    }

    /**
     * @return array{User, TahunPelajaran, Rombel, EraporRefCurriculum}
     */
    private function configurationContext(): array
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::findOrCreate('configure erapor', 'web'));
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
            'wali_kelas_id' => $user->id,
        ]);
        $curriculum = EraporRefCurriculum::create([
            'external_id' => 501,
            'name' => 'Kurikulum Merdeka SMK',
            'education_level_id' => 6,
            'valid_from' => '2022-07-01',
            'is_active' => true,
        ]);

        return [$user, $period, $rombel, $curriculum];
    }
}
