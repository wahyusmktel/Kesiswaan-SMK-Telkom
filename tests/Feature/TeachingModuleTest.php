<?php

namespace Tests\Feature;

use App\Models\MataPelajaran;
use App\Models\TahunPelajaran;
use App\Models\TeachingModule;
use App\Models\User;
use App\Support\TeachingModuleSchema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TeachingModuleTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;
    private MataPelajaran $subject;
    private TahunPelajaran $academicYear;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::findOrCreate('Guru Kelas', 'web');
        $this->teacher = User::factory()->create();
        $this->teacher->assignRole($role);
        $this->subject = MataPelajaran::create([
            'kode_mapel' => 'CC-01',
            'nama_mapel' => 'Cloud Computing',
            'jumlah_jam' => 4,
        ]);
        $this->academicYear = TahunPelajaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);
    }

    public function test_teacher_can_create_a_teaching_module_and_receives_default_editor_structure(): void
    {
        $response = $this->asTeacher()->post(
            route('guru-kelas.teaching-module.store'),
            $this->metadataPayload()
        );

        $module = TeachingModule::query()->firstOrFail();

        $response->assertRedirect(route('guru-kelas.teaching-module.content.edit', $module));
        $this->assertSame($this->teacher->id, $module->teacher_id);
        $this->assertSame('Cloud Computing', $module->mata_pelajaran);
        $this->assertSame('2026/2027', $module->tahun_pelajaran);
        $this->assertSame('Ganjil', $module->semester);
        $this->assertSame('draft', $module->status);
        $this->assertCount(5, $module->content['experiences'][0]['core_phases']);
    }

    public function test_teacher_can_save_repeatable_module_content_as_complete(): void
    {
        $module = $this->createModule($this->teacher);
        $content = TeachingModuleSchema::defaults([
            'allocation' => '4 JP',
            'teacher_name' => 'Guru Penguji',
        ]);
        $content['identification']['students'] = ['Peserta didik telah memahami jaringan dasar.'];
        $content['identification']['materials'] = ['Konsep dasar komputasi awan.'];
        $content['design']['learning_outcomes'] = ['Peserta didik memahami layanan cloud.'];
        $content['design']['learning_objectives'] = ['Menjelaskan perbedaan IaaS, PaaS, dan SaaS.'];
        $content['experiences'][0]['opening'] = ['Guru membuka pembelajaran dan melakukan apersepsi.'];
        $content['assessment']['initial'] = ['Kuis diagnostik.'];

        $response = $this->asTeacher()->put(
            route('guru-kelas.teaching-module.content.update', $module),
            [
                'status' => 'complete',
                'content_json' => json_encode($content, JSON_THROW_ON_ERROR),
            ]
        );

        $response->assertRedirect(route('guru-kelas.teaching-module.content.edit', $module));
        $module->refresh();
        $this->assertSame('complete', $module->status);
        $this->assertSame(
            'Peserta didik telah memahami jaringan dasar.',
            $module->content['identification']['students'][0]
        );
    }

    public function test_teacher_cannot_access_another_teachers_module(): void
    {
        $otherTeacher = User::factory()->create();
        $otherTeacher->assignRole('Guru Kelas');
        $module = $this->createModule($otherTeacher);

        $this->asTeacher()
            ->get(route('guru-kelas.teaching-module.content.edit', $module))
            ->assertNotFound();

        $this->asTeacher()
            ->get(route('guru-kelas.teaching-module.pdf.preview', $module))
            ->assertNotFound();
    }

    public function test_pdf_preview_is_rendered_for_the_owner(): void
    {
        $module = $this->createModule($this->teacher);

        $response = $this->asTeacher()
            ->get(route('guru-kelas.teaching-module.pdf.preview', $module));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
        $this->assertStringStartsWith('%PDF', (string) $response->getContent());
    }

    private function asTeacher()
    {
        return $this->actingAs($this->teacher)
            ->withSession(['active_role' => 'Guru Kelas']);
    }

    private function metadataPayload(): array
    {
        return [
            'program_keahlian' => 'Teknik Komputer dan Jaringan',
            'mata_pelajaran_id' => $this->subject->id,
            'fase' => 'F',
            'nama_penyusun' => 'Guru Penguji',
            'instansi' => 'SMK Telkom Lampung',
            'tahun_pelajaran_id' => $this->academicYear->id,
            'nama_modul' => 'Konsep Dasar Cloud Computing',
            'alokasi_waktu' => '4 JP',
            'jenjang' => 'SMK',
            'kelas' => 'XI',
            'kode_modul' => 'MA-1.1',
            'jumlah_murid' => 'Disesuaikan',
            'lingkup_materi' => 'Pengenalan Cloud Computing',
        ];
    }

    private function createModule(User $owner): TeachingModule
    {
        return TeachingModule::create([
            'teacher_id' => $owner->id,
            'program_keahlian' => 'Teknik Komputer dan Jaringan',
            'mata_pelajaran_id' => $this->subject->id,
            'mata_pelajaran' => $this->subject->nama_mapel,
            'fase' => 'F',
            'nama_penyusun' => 'Guru Penguji',
            'instansi' => 'SMK Telkom Lampung',
            'tahun_pelajaran_id' => $this->academicYear->id,
            'tahun_pelajaran' => $this->academicYear->tahun,
            'semester' => $this->academicYear->semester,
            'nama_modul' => 'Konsep Dasar Cloud Computing',
            'alokasi_waktu' => '4 JP',
            'jenjang' => 'SMK',
            'kelas' => 'XI',
            'kode_modul' => 'MA-'.$owner->id.'.1',
            'jumlah_murid' => 'Disesuaikan',
            'lingkup_materi' => 'Pengenalan Cloud Computing',
            'content' => TeachingModuleSchema::defaults([
                'allocation' => '4 JP',
                'teacher_name' => 'Guru Penguji',
            ]),
            'content_version' => TeachingModuleSchema::VERSION,
            'status' => 'draft',
        ]);
    }
}
