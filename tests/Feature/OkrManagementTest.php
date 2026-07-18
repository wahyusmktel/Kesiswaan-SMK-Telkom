<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\OkrKeyResult;
use App\Models\OkrPeriod;
use App\Models\OkrPlan;
use App\Models\OkrUnit;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OkrManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_student_can_open_okr_and_template_is_initialized_from_reference(): void
    {
        $user = $this->userWithRole('Guru Kelas');
        $this->activeAcademicYear();

        $this->actingAs($user)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->get(route('okr.index'))
            ->assertOk()
            ->assertSee('Manajemen OKR Sekolah')
            ->assertSee('Menghasilkan Lulusan dengan Kompetensi IT Terdepan')
            ->assertSee('Stella AI dapat membantu');

        $this->assertDatabaseCount('okr_objectives', 5);
        $this->assertDatabaseCount('okr_key_results', 16);
        $this->assertDatabaseCount('okr_units', 9);
        $this->assertDatabaseHas('okr_key_results', ['code' => 'KR 5.3']);
    }

    public function test_student_role_cannot_access_okr(): void
    {
        $student = $this->userWithRole('Siswa');

        $this->actingAs($student)
            ->withSession(['active_role' => 'Siswa'])
            ->get(route('okr.index'))
            ->assertForbidden();
    }

    public function test_unit_role_can_create_hierarchical_plan_and_progress_rolls_up(): void
    {
        $user = $this->userWithRole('Guru Kelas');
        $this->activeAcademicYear();
        $this->actingAs($user)->withSession(['active_role' => 'Guru Kelas'])->get(route('okr.index'));

        $unit = OkrUnit::where('code', 'KURIKULUM')->firstOrFail();
        $keyResult = OkrKeyResult::where('code', 'KR 1.1')->firstOrFail();

        $annual = $this->createPlan($user, $unit, $keyResult, 'annual', null, 'Target sertifikasi tahunan');
        $monthly = $this->createPlan($user, $unit, $keyResult, 'monthly', $annual, 'Target sertifikasi bulanan');
        $weekly = $this->createPlan($user, $unit, $keyResult, 'weekly', $monthly, 'Verifikasi sertifikasi minggu ini');

        $this->actingAs($user)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->post(route('okr.plans.progress', $weekly), [
                'progress_percent' => 100,
                'status' => 'completed',
                'note' => 'Seluruh data sertifikasi minggu ini telah diverifikasi.',
                'recorded_at' => now()->format('Y-m-d'),
            ])
            ->assertRedirect();

        $this->assertSame(100.0, (float) $weekly->fresh()->progress_percent);
        $this->assertSame(100.0, (float) $monthly->fresh()->progress_percent);
        $this->assertSame(100.0, (float) $annual->fresh()->progress_percent);
        $this->assertDatabaseHas('okr_progress_updates', [
            'okr_plan_id' => $weekly->id,
            'status' => 'completed',
        ]);
    }

    public function test_stella_ai_can_suggest_measurable_unit_plans(): void
    {
        Http::fake([
            'https://ai.example/v1/chat/completions' => Http::response([
                'choices' => [[
                    'message' => ['content' => json_encode([
                        'suggestions' => [[
                            'title' => 'Pemetaan siswa peserta sertifikasi',
                            'description' => 'Memvalidasi daftar siswa dan kebutuhan sertifikasi.',
                            'success_indicator' => 'Daftar 100% tervalidasi.',
                            'target_value' => 100,
                            'metric_unit' => '% data valid',
                        ]],
                    ])],
                ]],
            ]),
        ]);

        $user = $this->userWithRole('Guru Kelas');
        $this->activeAcademicYear();
        AppSetting::create([
            'school_name' => 'SMK Telkom Lampung',
            'stella_ai_enabled' => true,
            'stella_ai_base_url' => 'https://ai.example/v1',
            'stella_ai_api_key' => 'secret',
            'stella_ai_chat_model' => 'glm-5.2',
        ]);
        $this->actingAs($user)->withSession(['active_role' => 'Guru Kelas'])->get(route('okr.index'));

        $this->actingAs($user)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->postJson(route('okr.ai.suggest'), [
                'okr_key_result_id' => OkrKeyResult::where('code', 'KR 1.1')->value('id'),
                'okr_unit_id' => OkrUnit::where('code', 'KURIKULUM')->value('id'),
                'level' => 'annual',
                'context' => 'Prioritaskan sertifikasi internasional.',
            ])
            ->assertOk()
            ->assertJsonPath('suggestions.0.title', 'Pemetaan siswa peserta sertifikasi')
            ->assertJsonPath('suggestions.0.target_value', 100);
    }

    public function test_non_student_can_download_global_okr_pdf_report(): void
    {
        $user = $this->userWithRole('Kepala Sekolah');
        $this->activeAcademicYear();
        $this->actingAs($user)
            ->withSession(['active_role' => 'Kepala Sekolah'])
            ->get(route('okr.index'))
            ->assertOk();

        $this->actingAs($user)
            ->withSession(['active_role' => 'Kepala Sekolah'])
            ->get(route('okr.report', OkrPeriod::firstOrFail()))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    private function createPlan(
        User $user,
        OkrUnit $unit,
        OkrKeyResult $keyResult,
        string $level,
        ?OkrPlan $parent,
        string $title
    ): OkrPlan {
        $this->actingAs($user)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->post(route('okr.plans.store'), [
                'okr_key_result_id' => $keyResult->id,
                'okr_unit_id' => $unit->id,
                'parent_id' => $parent?->id,
                'owner_id' => $user->id,
                'level' => $level,
                'title' => $title,
                'target_value' => 100,
                'metric_unit' => '%',
                'weight' => 1,
            ])
            ->assertRedirect();

        return OkrPlan::where('title', $title)->firstOrFail();
    }

    private function userWithRole(string $roleName): User
    {
        $role = Role::findOrCreate($roleName, 'web');
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole($role);

        return $user;
    }

    private function activeAcademicYear(): TahunPelajaran
    {
        return TahunPelajaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);
    }
}
