<?php

namespace Tests\Feature;

use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SurveyDashboardNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::findOrCreate('Siswa');
        Role::findOrCreate('Operator');
        Role::findOrCreate('Guru Kelas');
    }

    public function test_targeted_user_sees_survey_notification_on_dashboard(): void
    {
        $this->withoutMiddleware();

        $operator = User::factory()->create();
        $operator->assignRole('Operator');

        $creator = User::factory()->create();

        $survey = Survey::create([
            'title' => 'Survei Kepuasan Sarana dan Prasarana 2026',
            'description' => 'Evaluasi fasilitas laboratorium dan kelas.',
            'created_by' => $creator->id,
            'is_active' => true,
            'start_at' => now()->subDay(),
            'end_at' => now()->addDays(5),
        ]);

        // Target operator user
        $survey->targets()->attach($operator->id);

        $response = $this->actingAs($operator)
            ->withSession(['active_role' => 'Operator'])
            ->get(route('operator.dashboard.index'));

        $response->assertOk();
        $response->assertSee('Anda memiliki survei yang harus di isi');
        $response->assertSee('Survei Kepuasan Sarana dan Prasarana 2026');
        $response->assertSee(route('surveys.show', $survey->id));
        $response->assertSee('Tutup Notifikasi');
    }

    public function test_user_not_targeted_does_not_see_survey_notification_on_dashboard(): void
    {
        $this->withoutMiddleware();

        $operator = User::factory()->create();
        $operator->assignRole('Operator');

        $otherUser = User::factory()->create();
        $creator = User::factory()->create();

        $survey = Survey::create([
            'title' => 'Survei Guru BK',
            'created_by' => $creator->id,
            'is_active' => true,
        ]);

        // Target someone else, not $operator
        $survey->targets()->attach($otherUser->id);

        $response = $this->actingAs($operator)
            ->withSession(['active_role' => 'Operator'])
            ->get(route('operator.dashboard.index'));

        $response->assertOk();
        $response->assertDontSee('Anda memiliki survei yang harus di isi');
        $response->assertDontSee('Survei Guru BK');
    }

    public function test_user_who_already_responded_does_not_see_survey_notification(): void
    {
        $this->withoutMiddleware();

        $operator = User::factory()->create();
        $operator->assignRole('Operator');

        $creator = User::factory()->create();

        $survey = Survey::create([
            'title' => 'Survei Evaluasi Pembelajaran',
            'created_by' => $creator->id,
            'is_active' => true,
        ]);

        $survey->targets()->attach($operator->id);

        // Operator submits response
        SurveyResponse::create([
            'survey_id' => $survey->id,
            'user_id' => $operator->id,
        ]);

        $response = $this->actingAs($operator)
            ->withSession(['active_role' => 'Operator'])
            ->get(route('operator.dashboard.index'));

        $response->assertOk();
        $response->assertDontSee('Anda memiliki survei yang harus di isi');
    }

    public function test_inactive_or_expired_or_upcoming_survey_does_not_show_notification(): void
    {
        $this->withoutMiddleware();

        $operator = User::factory()->create();
        $operator->assignRole('Operator');
        $creator = User::factory()->create();

        // 1. Inactive survey
        $inactive = Survey::create([
            'title' => 'Survei Tidak Aktif',
            'created_by' => $creator->id,
            'is_active' => false,
        ]);
        $inactive->targets()->attach($operator->id);

        // 2. Expired survey
        $expired = Survey::create([
            'title' => 'Survei Sudah Berakhir',
            'created_by' => $creator->id,
            'is_active' => true,
            'start_at' => now()->subDays(10),
            'end_at' => now()->subDays(2),
        ]);
        $expired->targets()->attach($operator->id);

        // 3. Upcoming survey
        $upcoming = Survey::create([
            'title' => 'Survei Masa Depan',
            'created_by' => $creator->id,
            'is_active' => true,
            'start_at' => now()->addDays(2),
            'end_at' => now()->addDays(10),
        ]);
        $upcoming->targets()->attach($operator->id);

        $response = $this->actingAs($operator)
            ->withSession(['active_role' => 'Operator'])
            ->get(route('operator.dashboard.index'));

        $response->assertOk();
        $response->assertDontSee('Anda memiliki survei yang harus di isi');
    }

    public function test_multiple_pending_surveys_shows_correct_count_and_links(): void
    {
        $this->withoutMiddleware();

        $operator = User::factory()->create();
        $operator->assignRole('Operator');
        $creator = User::factory()->create();

        $survey1 = Survey::create([
            'title' => 'Survei Kepuasan Siswa 1',
            'created_by' => $creator->id,
            'is_active' => true,
        ]);
        $survey1->targets()->attach($operator->id);

        $survey2 = Survey::create([
            'title' => 'Survei Kepuasan Siswa 2',
            'created_by' => $creator->id,
            'is_active' => true,
        ]);
        $survey2->targets()->attach($operator->id);

        $response = $this->actingAs($operator)
            ->withSession(['active_role' => 'Operator'])
            ->get(route('operator.dashboard.index'));

        $response->assertOk();
        $response->assertSee('Anda memiliki survei yang harus di isi');
        $response->assertSee('2 Survei Tersedia');
        $response->assertSee(route('surveys.index'));
    }

    public function test_survey_notification_not_shown_on_non_dashboard_pages(): void
    {
        $this->withoutMiddleware();

        $operator = User::factory()->create();
        $operator->assignRole('Operator');
        $creator = User::factory()->create();

        $survey = Survey::create([
            'title' => 'Survei Pelayanan Administrasi',
            'created_by' => $creator->id,
            'is_active' => true,
        ]);
        $survey->targets()->attach($operator->id);

        // Access non-dashboard page (surveys index itself)
        $response = $this->actingAs($operator)
            ->withSession(['active_role' => 'Operator'])
            ->get(route('surveys.index'));

        $response->assertOk();
        // The banner should NOT be rendered in the main layout on non-dashboard pages
        $response->assertDontSee('Anda memiliki survei yang harus di isi');
    }
}
