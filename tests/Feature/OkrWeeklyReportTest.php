<?php

namespace Tests\Feature;

use App\Models\OkrKeyResult;
use App\Models\OkrObjective;
use App\Models\OkrPeriod;
use App\Models\OkrPlan;
use App\Models\OkrUnit;
use App\Models\OkrWeeklyReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OkrWeeklyReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_unit_can_plan_evaluate_and_submit_a_weekly_report_for_headmaster_review(): void
    {
        $period = $this->period();
        $unit = $this->unit();
        $curriculum = $this->userWithRole('Kurikulum');

        $this->actingAs($curriculum)
            ->withSession(['active_role' => 'Kurikulum'])
            ->post(route('okr.weekly.planning'), [
                'okr_period_id' => $period->id,
                'okr_unit_id' => $unit->id,
                'week_start' => '2026-09-07',
                'weekly_focus' => 'Finalisasi mitra Program Melanjutkan.',
                'support_needed' => 'Arahan pemilihan mitra dari Kepala Sekolah.',
                'items' => [
                    [
                        'commitment' => 'Membandingkan penawaran tiga calon mitra.',
                        'measurable_target' => 'Rekomendasi mitra selesai Jumat.',
                        'cross_unit_dependencies' => 'BK dan Hubin',
                        'approval_needs' => 'Persetujuan Kepala Sekolah',
                    ],
                    [
                        'commitment' => 'Menindaklanjuti proposal Al Faiz.',
                        'measurable_target' => 'Proposal diterima dan dikaji Kamis.',
                    ],
                    [],
                ],
            ])
            ->assertRedirect();

        $report = OkrWeeklyReport::with('items')->firstOrFail();
        $this->assertSame('draft', $report->status);
        $this->assertCount(2, $report->items);
        $this->assertSame('2026-09-11', $report->week_end->format('Y-m-d'));

        $evaluation = [];
        foreach ($report->items as $index => $item) {
            $evaluation[$item->id] = [
                'actual_result' => $index === 0 ? 'Perbandingan tiga mitra telah selesai.' : 'Proposal sudah diterima.',
                'completion_percent' => $index === 0 ? 100 : 75,
                'final_status' => $index === 0 ? 'completed' : 'on_progress',
                'blockers' => $index === 0 ? null : 'Respons mitra lambat.',
                'next_follow_up' => 'Bahas pada Senin depan.',
            ];
        }

        $this->actingAs($curriculum)
            ->withSession(['active_role' => 'Kurikulum'])
            ->post(route('okr.weekly.evaluation', $report), ['items' => $evaluation])
            ->assertRedirect();

        $this->assertSame('submitted', $report->fresh()->status);
        $this->assertDatabaseHas('okr_weekly_report_items', [
            'okr_weekly_report_id' => $report->id,
            'final_status' => 'completed',
            'completion_percent' => 100,
        ]);

        $headmaster = $this->userWithRole('Kepala Sekolah');
        $this->actingAs($headmaster)
            ->withSession(['active_role' => 'Kepala Sekolah'])
            ->get(route('okr.weekly.index', [
                'period_id' => $period->id,
                'unit_id' => $unit->id,
                'week_start' => '2026-09-07',
            ]))
            ->assertOk()
            ->assertSee('Monitoring Pelaporan Seluruh Unit')
            ->assertSee('Perbandingan tiga mitra telah selesai.');

        $this->actingAs($headmaster)
            ->withSession(['active_role' => 'Kepala Sekolah'])
            ->post(route('okr.weekly.review', $report), [
                'review_notes' => 'Lanjutkan negosiasi dengan mitra prioritas.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('okr_weekly_reports', [
            'id' => $report->id,
            'status' => 'reviewed',
            'reviewed_by' => $headmaster->id,
            'review_notes' => 'Lanjutkan negosiasi dengan mitra prioritas.',
        ]);
    }

    public function test_unrelated_role_cannot_submit_a_curriculum_weekly_report(): void
    {
        $period = $this->period();
        $unit = $this->unit();
        $security = $this->userWithRole('Security');

        $this->actingAs($security)
            ->withSession(['active_role' => 'Security'])
            ->post(route('okr.weekly.planning'), [
                'okr_period_id' => $period->id,
                'okr_unit_id' => $unit->id,
                'week_start' => '2026-09-07',
                'weekly_focus' => 'Tidak berhak',
                'items' => [[
                    'commitment' => 'Tidak berhak',
                    'measurable_target' => 'Tidak berhak',
                ]],
            ])
            ->assertForbidden();
    }

    public function test_unit_can_copy_only_unfinished_commitments_from_previous_week(): void
    {
        $period = $this->period();
        $unit = $this->unit();
        $curriculum = $this->userWithRole('Kurikulum');
        $previous = OkrWeeklyReport::create([
            'okr_period_id' => $period->id,
            'okr_unit_id' => $unit->id,
            'week_start' => '2026-09-07',
            'week_end' => '2026-09-11',
            'weekly_focus' => 'Finalisasi calon mitra.',
            'support_needed' => 'Koordinasi dengan Kepala Sekolah.',
            'status' => 'reviewed',
        ]);
        $previous->items()->createMany([
            [
                'priority_order' => 1,
                'commitment' => 'Kaji proposal Ruang Guru.',
                'measurable_target' => 'Kajian selesai Jumat.',
                'actual_result' => 'Selesai.',
                'completion_percent' => 100,
                'final_status' => 'completed',
            ],
            [
                'priority_order' => 2,
                'commitment' => 'Hubungi Genza.',
                'measurable_target' => 'Pertemuan dengan Genza terlaksana.',
                'actual_result' => 'Baru komunikasi WhatsApp.',
                'completion_percent' => 60,
                'final_status' => 'on_progress',
                'next_follow_up' => 'Jadwalkan pembahasan langsung dengan Genza.',
            ],
        ]);

        $this->actingAs($curriculum)
            ->withSession(['active_role' => 'Kurikulum'])
            ->post(route('okr.weekly.copy-previous'), [
                'okr_period_id' => $period->id,
                'okr_unit_id' => $unit->id,
                'week_start' => '2026-09-14',
            ])
            ->assertRedirect();

        $report = OkrWeeklyReport::with('items')
            ->whereDate('week_start', '2026-09-14')
            ->firstOrFail();

        $this->assertSame('draft', $report->status);
        $this->assertCount(1, $report->items);
        $this->assertSame('Jadwalkan pembahasan langsung dengan Genza.', $report->items->first()->commitment);
        $this->assertSame('not_started', $report->items->first()->final_status);
        $this->assertNull($report->items->first()->actual_result);
    }

    public function test_unit_confirms_recommended_progress_after_headmaster_review(): void
    {
        $period = $this->period();
        $unit = $this->unit();
        $curriculum = $this->userWithRole('Kurikulum');
        $objective = OkrObjective::create([
            'okr_period_id' => $period->id,
            'code' => 'O1',
            'title' => 'Lulusan unggul',
            'sort_order' => 1,
        ]);
        $keyResult = OkrKeyResult::create([
            'okr_objective_id' => $objective->id,
            'code' => 'KR 1.1',
            'title' => 'Program melanjutkan',
            'description' => 'Meningkatkan penerimaan perguruan tinggi.',
            'metric_type' => 'percentage',
            'baseline_value' => 0,
            'target_value' => 100,
            'metric_unit' => '%',
            'weight' => 1,
            'sort_order' => 1,
        ]);
        $parent = OkrPlan::create([
            'okr_key_result_id' => $keyResult->id,
            'okr_unit_id' => $unit->id,
            'level' => 'annual',
            'title' => 'Target tahunan Kurikulum',
            'starts_at' => '2026-07-01',
            'ends_at' => '2027-06-30',
            'progress_percent' => 0,
            'status' => 'not_started',
        ]);
        $plan = OkrPlan::create([
            'okr_key_result_id' => $keyResult->id,
            'okr_unit_id' => $unit->id,
            'parent_id' => $parent->id,
            'level' => 'monthly',
            'title' => 'Finalisasi mitra Program Melanjutkan',
            'starts_at' => '2026-09-01',
            'ends_at' => '2026-09-28',
            'target_value' => 100,
            'current_value' => 20,
            'progress_percent' => 20,
            'status' => 'in_progress',
        ]);
        $report = OkrWeeklyReport::create([
            'okr_period_id' => $period->id,
            'okr_unit_id' => $unit->id,
            'week_start' => '2026-09-07',
            'week_end' => '2026-09-11',
            'weekly_focus' => 'Finalisasi calon mitra.',
            'status' => 'reviewed',
            'reviewed_at' => now(),
        ]);
        $item = $report->items()->create([
            'okr_plan_id' => $plan->id,
            'priority_order' => 1,
            'commitment' => 'Bandingkan calon mitra.',
            'measurable_target' => 'Rekomendasi selesai.',
            'actual_result' => 'Perbandingan hampir selesai.',
            'completion_percent' => 80,
            'final_status' => 'on_progress',
        ]);

        $this->actingAs($curriculum)
            ->withSession(['active_role' => 'Kurikulum'])
            ->get(route('okr.weekly.index', [
                'period_id' => $period->id,
                'unit_id' => $unit->id,
                'week_start' => '2026-09-07',
            ]))
            ->assertOk()
            ->assertSee('Perbarui Progres OKR dari Laporan Ini')
            ->assertSee('value="40"', false);

        $this->actingAs($curriculum)
            ->withSession(['active_role' => 'Kurikulum'])
            ->post(route('okr.weekly.apply-progress', $report), [
                'recorded_at' => '2026-09-11',
                'plans' => [
                    $plan->id => [
                        'progress_percent' => 40,
                        'status' => 'in_progress',
                        'note' => 'Progres dikonfirmasi dari evaluasi pekanan.',
                    ],
                ],
            ])
            ->assertRedirect();

        $this->assertSame(40.0, (float) $plan->fresh()->progress_percent);
        $this->assertSame(40.0, (float) $plan->fresh()->current_value);
        $this->assertSame(40.0, (float) $parent->fresh()->progress_percent);
        $this->assertNotNull($item->fresh()->progress_applied_at);
        $this->assertDatabaseHas('okr_progress_updates', [
            'okr_plan_id' => $plan->id,
            'progress_before' => 20,
            'progress_after' => 40,
            'note' => 'Progres dikonfirmasi dari evaluasi pekanan.',
        ]);

        $this->actingAs($curriculum)
            ->withSession(['active_role' => 'Kurikulum'])
            ->post(route('okr.weekly.apply-progress', $report), [
                'recorded_at' => '2026-09-11',
                'plans' => [$plan->id => ['progress_percent' => 60, 'status' => 'in_progress', 'note' => 'Duplikat']],
            ])
            ->assertStatus(422);
    }

    private function period(): OkrPeriod
    {
        return OkrPeriod::create([
            'title' => 'OKR Sekolah 2026/2027',
            'starts_at' => '2026-07-01',
            'ends_at' => '2027-06-30',
            'status' => 'active',
        ]);
    }

    private function unit(): OkrUnit
    {
        return OkrUnit::create([
            'code' => 'KURIKULUM',
            'name' => 'Kurikulum',
            'role_names' => ['Kurikulum', 'Kaprodi'],
            'sort_order' => 1,
            'is_active' => true,
        ]);
    }

    private function userWithRole(string $roleName): User
    {
        $role = Role::findOrCreate($roleName, 'web');
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole($role);

        return $user;
    }
}
