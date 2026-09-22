<?php

namespace Tests\Feature;

use App\Models\Survey;
use App\Models\SurveyShare;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SurveyShareTest extends TestCase
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

    public function test_owner_can_view_shares_and_available_users(): void
    {
        $owner = User::factory()->create();
        $collaborator = User::factory()->create(['name' => 'Guru Budi']);
        $otherUser = User::factory()->create(['name' => 'Guru Siti']);
        $studentUser = User::factory()->create(['name' => 'Siswa Joko']);
        $studentUser->assignRole('Siswa');

        $survey = Survey::create([
            'title' => 'Survei Kepuasan Siswa',
            'created_by' => $owner->id,
            'is_active' => true,
        ]);

        SurveyShare::create([
            'survey_id' => $survey->id,
            'user_id' => $collaborator->id,
            'role' => 'editor',
            'shared_by' => $owner->id,
        ]);

        $response = $this->actingAs($owner)->getJson(route('surveys.shares.index', $survey));

        $response->assertOk();
        $response->assertJsonStructure([
            'survey' => ['id', 'title', 'owner', 'share_url'],
            'can_manage',
            'shares',
            'available_users',
        ]);

        $data = $response->json();
        $this->assertTrue($data['can_manage']);
        $this->assertCount(1, $data['shares']);
        $this->assertEquals($collaborator->id, $data['shares'][0]['user_id']);
        $this->assertEquals('editor', $data['shares'][0]['role']);

        // Available users includes non-student and non-collaborator user, excludes student and owner
        $availableUserIds = collect($data['available_users'])->pluck('id')->all();
        $this->assertContains($otherUser->id, $availableUserIds);
        $this->assertNotContains($studentUser->id, $availableUserIds);
        $this->assertNotContains($owner->id, $availableUserIds);
        $this->assertNotContains($collaborator->id, $availableUserIds);
    }

    public function test_owner_can_share_survey_as_editor_and_viewer(): void
    {
        $owner = User::factory()->create();
        $targetEditor = User::factory()->create();
        $targetViewer = User::factory()->create();

        $survey = Survey::create([
            'title' => 'Survei Kerja Sama',
            'created_by' => $owner->id,
            'is_active' => true,
        ]);

        // Share as editor
        $res1 = $this->actingAs($owner)->postJson(route('surveys.shares.store', $survey), [
            'user_id' => $targetEditor->id,
            'role' => 'editor',
        ]);
        $res1->assertOk();
        $res1->assertJson(['success' => true]);
        $this->assertDatabaseHas('survey_shares', [
            'survey_id' => $survey->id,
            'user_id' => $targetEditor->id,
            'role' => 'editor',
        ]);

        // Share as viewer
        $res2 = $this->actingAs($owner)->postJson(route('surveys.shares.store', $survey), [
            'user_id' => $targetViewer->id,
            'role' => 'viewer',
        ]);
        $res2->assertOk();
        $res2->assertJson(['success' => true]);
        $this->assertDatabaseHas('survey_shares', [
            'survey_id' => $survey->id,
            'user_id' => $targetViewer->id,
            'role' => 'viewer',
        ]);
    }

    public function test_unauthorized_user_cannot_share_survey(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $anotherUser = User::factory()->create();

        $survey = Survey::create([
            'title' => 'Survei Rahasia',
            'created_by' => $owner->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($stranger)->postJson(route('surveys.shares.store', $survey), [
            'user_id' => $anotherUser->id,
            'role' => 'editor',
        ]);

        $response->assertForbidden();
    }

    public function test_editor_can_edit_update_and_duplicate_survey(): void
    {
        $owner = User::factory()->create();
        $editor = User::factory()->create();

        $survey = Survey::create([
            'title' => 'Survei Kurikulum',
            'description' => 'Evaluasi',
            'created_by' => $owner->id,
            'is_active' => false,
        ]);

        $survey->questions()->create([
            'question_text' => 'Pertanyaan 1',
            'type' => 'multiple_choice',
            'options' => ['A', 'B'],
            'order' => 0,
        ]);

        SurveyShare::create([
            'survey_id' => $survey->id,
            'user_id' => $editor->id,
            'role' => 'editor',
            'shared_by' => $owner->id,
        ]);

        // Editor can view edit form
        $resEdit = $this->actingAs($editor)->get(route('surveys.edit', $survey));
        $resEdit->assertOk();

        // Editor can update survey
        $resUpdate = $this->actingAs($editor)->put(route('surveys.update', $survey), [
            'title' => 'Survei Kurikulum Diperbarui',
            'description' => 'Deskripsi baru',
            'is_active' => 1,
            'target_users' => [$editor->id],
            'questions' => [
                [
                    'id' => null,
                    'question_text' => 'Pertanyaan Baru',
                    'type' => 'essay',
                    'options' => [],
                ]
            ],
        ]);
        $resUpdate->assertRedirect(route('surveys.index'));
        $this->assertDatabaseHas('surveys', [
            'id' => $survey->id,
            'title' => 'Survei Kurikulum Diperbarui',
        ]);

        // Editor can duplicate survey
        $resDup = $this->actingAs($editor)->post(route('surveys.duplicate', $survey));
        $resDup->assertRedirect(route('surveys.index'));
        $this->assertDatabaseHas('surveys', [
            'title' => 'Survei Kurikulum Diperbarui (Salinan)',
            'created_by' => $editor->id,
        ]);
    }

    public function test_viewer_can_view_results_but_cannot_edit(): void
    {
        $owner = User::factory()->create();
        $viewer = User::factory()->create();

        $survey = Survey::create([
            'title' => 'Survei Kesiswaan',
            'created_by' => $owner->id,
            'is_active' => true,
        ]);

        SurveyShare::create([
            'survey_id' => $survey->id,
            'user_id' => $viewer->id,
            'role' => 'viewer',
            'shared_by' => $owner->id,
        ]);

        // Viewer can view results
        $resResults = $this->actingAs($viewer)->get(route('surveys.results', $survey));
        $resResults->assertOk();

        // Viewer cannot access edit page
        $resEdit = $this->actingAs($viewer)->get(route('surveys.edit', $survey));
        $resEdit->assertForbidden();

        // Viewer cannot update survey
        $resUpdate = $this->actingAs($viewer)->put(route('surveys.update', $survey), [
            'title' => 'Coba Ubah Judul',
            'is_active' => 1,
            'target_users' => [$viewer->id],
        ]);
        $resUpdate->assertForbidden();

        // Viewer cannot duplicate survey
        $resDup = $this->actingAs($viewer)->post(route('surveys.duplicate', $survey));
        $resDup->assertForbidden();
    }

    public function test_collaborator_cannot_delete_survey(): void
    {
        $owner = User::factory()->create();
        $editor = User::factory()->create();

        $survey = Survey::create([
            'title' => 'Survei Penting',
            'created_by' => $owner->id,
            'is_active' => true,
        ]);

        SurveyShare::create([
            'survey_id' => $survey->id,
            'user_id' => $editor->id,
            'role' => 'editor',
            'shared_by' => $owner->id,
        ]);

        // Editor cannot delete
        $resDelete = $this->actingAs($editor)->delete(route('surveys.destroy', $survey));
        $resDelete->assertForbidden();
        $this->assertDatabaseHas('surveys', ['id' => $survey->id]);

        // Owner can delete
        $resOwnerDelete = $this->actingAs($owner)->delete(route('surveys.destroy', $survey));
        $resOwnerDelete->assertRedirect(route('surveys.index'));
        $this->assertDatabaseMissing('surveys', ['id' => $survey->id]);
    }

    public function test_owner_can_update_role_and_remove_collaborator(): void
    {
        $owner = User::factory()->create();
        $collaborator = User::factory()->create();

        $survey = Survey::create([
            'title' => 'Survei Role Update',
            'created_by' => $owner->id,
            'is_active' => true,
        ]);

        $share = SurveyShare::create([
            'survey_id' => $survey->id,
            'user_id' => $collaborator->id,
            'role' => 'viewer',
            'shared_by' => $owner->id,
        ]);

        // Update role to editor
        $resUpdate = $this->actingAs($owner)->patchJson(
            route('surveys.shares.update', [$survey, $collaborator]),
            ['role' => 'editor']
        );
        $resUpdate->assertOk();
        $this->assertEquals('editor', $share->fresh()->role);

        // Remove collaborator
        $resDelete = $this->actingAs($owner)->deleteJson(
            route('surveys.shares.destroy', [$survey, $collaborator])
        );
        $resDelete->assertOk();
        $this->assertDatabaseMissing('survey_shares', [
            'survey_id' => $survey->id,
            'user_id' => $collaborator->id,
        ]);
    }

    public function test_shared_surveys_appear_on_collaborator_index_page(): void
    {
        $owner = User::factory()->create(['name' => 'Pak Budi']);
        $collaborator = User::factory()->create(['name' => 'Bu Ani']);

        $survey = Survey::create([
            'title' => 'Survei Bersama Guru',
            'created_by' => $owner->id,
            'is_active' => true,
        ]);

        SurveyShare::create([
            'survey_id' => $survey->id,
            'user_id' => $collaborator->id,
            'role' => 'editor',
            'shared_by' => $owner->id,
        ]);

        $response = $this->actingAs($collaborator)->get(route('surveys.index'));
        $response->assertOk();
        $response->assertSee('Survei Bersama Guru');
        $response->assertSee('Kolaborator (Editor)');
    }
}
