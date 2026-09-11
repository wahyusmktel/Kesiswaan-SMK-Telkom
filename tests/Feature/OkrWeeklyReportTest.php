<?php

namespace Tests\Feature;

use App\Models\OkrPeriod;
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
