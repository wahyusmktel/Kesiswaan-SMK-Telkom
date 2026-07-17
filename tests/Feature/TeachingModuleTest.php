<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\MataPelajaran;
use App\Models\TahunPelajaran;
use App\Models\TeachingModule;
use App\Models\User;
use App\Support\TeachingModuleSchema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
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

        $this->asTeacher()
            ->postJson(route('guru-kelas.teaching-module.content.ai-generate', $module), [
                'topic' => 'Topik milik guru lain',
                'current_content' => $module->content,
            ])
            ->assertNotFound();
    }

    public function test_teacher_can_generate_all_module_sections_with_stella_ai(): void
    {
        $module = $this->createModule($this->teacher);
        $generated = $this->completeGeneratedContent();

        Http::fake([
            'https://ai.example/v1/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => json_encode($generated, JSON_THROW_ON_ERROR)]],
                ],
            ]),
        ]);

        AppSetting::create([
            'stella_ai_base_url' => 'https://ai.example/v1',
            'stella_ai_api_key' => 'secret-api-key',
            'stella_ai_chat_model' => 'chat-model',
            'stella_ai_enabled' => true,
        ]);

        $this->asTeacher()
            ->postJson(route('guru-kelas.teaching-module.content.ai-generate', $module), [
                'topic' => 'Implementasi layanan cloud untuk server sekolah',
                'current_content' => $module->content,
            ])
            ->assertOk()
            ->assertJsonPath('content.identification.students.0', 'Peserta didik memahami jaringan dasar.')
            ->assertJsonPath('content.design.learning_objectives.0', 'Peserta didik mampu merancang layanan cloud sederhana.')
            ->assertJsonPath('content.approval.teacher_name', 'Guru Penguji');

        Http::assertSent(function ($request) {
            return $request->url() === 'https://ai.example/v1/chat/completions'
                && $request['model'] === 'chat-model'
                && str_contains($request['messages'][1]['content'], 'Implementasi layanan cloud untuk server sekolah');
        });
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

    private function completeGeneratedContent(): array
    {
        $content = TeachingModuleSchema::defaults([
            'allocation' => '4 JP',
            'teacher_name' => 'Nama Fiktif dari AI',
        ]);
        $content['identification']['students'] = ['Peserta didik memahami jaringan dasar.'];
        $content['identification']['materials'] = ['Konsep dan implementasi layanan cloud.'];
        $content['identification']['graduate_profile'][1]['selected'] = true;
        $content['identification']['graduate_profile'][1]['note'] = 'Mengembangkan solusi cloud yang kreatif.';
        $content['design'] = [
            'learning_outcomes' => ['Peserta didik mampu memahami arsitektur layanan cloud.'],
            'learning_objectives' => ['Peserta didik mampu merancang layanan cloud sederhana.'],
            'learning_topics' => ['Arsitektur dan implementasi cloud.'],
            'pedagogical_practices' => ['Pembelajaran berbasis proyek.'],
            'learning_partners' => ['Praktisi infrastruktur TI.'],
            'learning_environment' => ['Laboratorium komputer dan lingkungan cloud virtual.'],
            'digital_use' => ['Dashboard penyedia layanan cloud.'],
        ];
        $content['experiences'][0]['title'] = 'Merancang layanan cloud sekolah';
        $content['experiences'][0]['opening'] = ['Guru melakukan apersepsi dan asesmen diagnostik.'];
        $content['experiences'][0]['core_phases'][0]['teacher_activities'] = ['Guru menyajikan masalah kebutuhan server sekolah.'];
        $content['experiences'][0]['core_phases'][0]['student_activities'] = ['Peserta didik menganalisis kebutuhan layanan.'];
        $content['experiences'][0]['core_phases'][0]['outputs'] = ['Dokumen analisis kebutuhan cloud.'];
        $content['experiences'][0]['closing'] = ['Guru memandu refleksi dan menyampaikan tindak lanjut.'];
        $content['assessment'] = [
            'initial' => ['Kuis diagnostik konsep jaringan.'],
            'process' => ['Observasi proses perancangan.'],
            'final' => ['Presentasi rancangan layanan cloud.'],
            'criteria' => ['Ketepatan arsitektur, keamanan, dan presentasi.'],
        ];
        $content['supporting'] = [
            'trigger_questions' => ['Bagaimana sekolah menyediakan server yang mudah dikembangkan?'],
            'differentiation' => ['Dukungan langkah kerja bertingkat sesuai kesiapan peserta didik.'],
            'enrichment' => ['Membandingkan dua penyedia layanan cloud.'],
            'remedial' => ['Mengulang simulasi dengan panduan terstruktur.'],
        ];
        $content['attachments'] = [
            'teaching_materials' => ['Ringkasan arsitektur layanan cloud.'],
            'worksheets' => ['LKPD analisis kebutuhan layanan cloud.'],
            'assessments' => ['Rubrik penilaian rancangan dan presentasi.'],
        ];

        return $content;
    }
}
