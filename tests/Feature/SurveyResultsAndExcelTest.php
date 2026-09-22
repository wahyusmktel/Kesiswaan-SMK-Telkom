<?php

namespace Tests\Feature;

use App\Exports\SurveyExport;
use App\Models\Kelas;
use App\Models\MasterSiswa;
use App\Models\Rombel;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SurveyResultsAndExcelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'Super Admin']);
        Role::firstOrCreate(['name' => 'Operator']);
        Role::firstOrCreate(['name' => 'Guru Kelas']);
        Role::firstOrCreate(['name' => 'Siswa']);
    }

    public function test_results_page_displays_widgets_and_paginated_respondents(): void
    {
        $owner = User::factory()->create();

        // Create academic year, class, and rombel
        $tp = TahunPelajaran::create(['tahun' => '2025/2026', 'semester' => 'Ganjil', 'is_active' => true]);
        $kelas = Kelas::create(['nama_kelas' => 'XII RPL 1', 'jurusan' => 'RPL']);
        $rombel = Rombel::create([
            'tahun_ajaran' => '2025/2026',
            'tahun_pelajaran_id' => $tp->id,
            'kelas_id' => $kelas->id,
            'wali_kelas_id' => $owner->id,
        ]);

        // Create 15 students targeted
        $students = [];
        for ($i = 1; $i <= 15; $i++) {
            $u = User::factory()->create(['name' => "Siswa {$i}"]);
            $u->assignRole('Siswa');
            $ms = MasterSiswa::create([
                'user_id' => $u->id,
                'nis' => "1000{$i}",
                'nama_lengkap' => "Siswa {$i}",
                'jenis_kelamin' => 'L',
                'status' => 'aktif',
            ]);
            $ms->rombels()->attach($rombel->id);
            $students[] = $u;
        }

        $survey = Survey::create([
            'title' => 'Survei Evaluasi Pembelajaran Kelas XII',
            'description' => 'Evaluasi KBM',
            'created_by' => $owner->id,
            'is_active' => true,
        ]);

        $q1 = $survey->questions()->create([
            'question_text' => 'Bagaimana materi hari ini?',
            'type' => 'multiple_choice',
            'options' => ['Bagus', 'Cukup', 'Kurang'],
            'order' => 0,
        ]);

        // Attach all 15 students as targets
        $survey->targets()->sync(collect($students)->pluck('id')->all());

        // First 5 students submitted responses
        for ($i = 0; $i < 5; $i++) {
            $resp = $survey->responses()->create([
                'user_id' => $students[$i]->id,
            ]);
            $resp->answers()->create([
                'question_id' => $q1->id,
                'answer_value' => 'Bagus',
            ]);
        }

        // 1. Visit results page
        $response = $this->actingAs($owner)->get(route('surveys.results', $survey));

        $response->assertOk();
        $response->assertSee('Total Target Peserta');
        $response->assertSee('Sudah Mengisi');
        $response->assertSee('Belum Mengisi');
        $response->assertSee('Tingkat Partisipasi');
        $response->assertSee('XII RPL 1');
        $response->assertSee('Export Excel');

        // Check widgets numbers
        $response->assertViewHas('totalTarget', 15);
        $response->assertViewHas('totalSubmitted', 5);
        $response->assertViewHas('totalPending', 10);
        $response->assertViewHas('completionRate', 33.3);

        // Check pagination: 10 per page
        $paginator = $response->viewData('respondentsPaginator');
        $this->assertCount(10, $paginator->items());
        $this->assertEquals(15, $paginator->total());
        $this->assertEquals(2, $paginator->lastPage());
    }

    public function test_results_filtering_by_pending_and_submitted(): void
    {
        $owner = User::factory()->create();

        $student1 = User::factory()->create(['name' => 'Ahmad Sudah']);
        $student2 = User::factory()->create(['name' => 'Budi Belum']);

        $survey = Survey::create([
            'title' => 'Survei Filter Test',
            'created_by' => $owner->id,
            'is_active' => true,
        ]);

        $survey->targets()->sync([$student1->id, $student2->id]);

        // Only student 1 submitted
        $survey->responses()->create(['user_id' => $student1->id]);

        // Filter: pending
        $resPending = $this->actingAs($owner)->get(route('surveys.results', [$survey, 'status' => 'pending']));
        $resPending->assertOk();
        $paginatorPending = $resPending->viewData('respondentsPaginator');
        $this->assertCount(1, $paginatorPending->items());
        $this->assertEquals('Budi Belum', $paginatorPending->items()[0]->name);

        // Filter: submitted
        $resSubmitted = $this->actingAs($owner)->get(route('surveys.results', [$survey, 'status' => 'submitted']));
        $resSubmitted->assertOk();
        $paginatorSubmitted = $resSubmitted->viewData('respondentsPaginator');
        $this->assertCount(1, $paginatorSubmitted->items());
        $this->assertEquals('Ahmad Sudah', $paginatorSubmitted->items()[0]->name);
    }

    public function test_export_excel_returns_download_with_multiple_sheets(): void
    {
        $owner = User::factory()->create();
        $targetUser = User::factory()->create(['name' => 'Siswa Joko']);

        $survey = Survey::create([
            'title' => 'Survei Excel Test',
            'created_by' => $owner->id,
            'is_active' => true,
        ]);

        $q = $survey->questions()->create([
            'question_text' => 'Apakah Anda puas?',
            'type' => 'multiple_choice',
            'options' => ['Ya', 'Tidak'],
            'order' => 0,
        ]);

        $survey->targets()->sync([$targetUser->id]);

        $resp = $survey->responses()->create(['user_id' => $targetUser->id]);
        $resp->answers()->create([
            'question_id' => $q->id,
            'answer_value' => 'Ya',
        ]);

        // Test download response
        $response = $this->actingAs($owner)->get(route('surveys.export.excel', $survey));
        $response->assertOk();
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString('.xlsx', $response->headers->get('content-disposition'));

        // Verify SurveyExport class has 3 sheets
        $export = new SurveyExport($survey);
        $sheets = $export->sheets();
        $this->assertCount(3, $sheets);
        $this->assertEquals('Ringkasan & Analisis', $sheets[0]->title());
        $this->assertEquals('Data Jawaban Responden', $sheets[1]->title());
        $this->assertEquals('Daftar Belum Mengisi', $sheets[2]->title());
    }
}
