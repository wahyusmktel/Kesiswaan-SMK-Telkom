<?php

namespace Tests\Feature;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SurveyDuplicateAndEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_duplicate_an_existing_survey(): void
    {
        Role::firstOrCreate(['name' => 'Guru Kelas']);
        $creator = User::factory()->create();
        $targetUser = User::factory()->create();

        $originalSurvey = Survey::create([
            'title' => 'Survei Evaluasi Pembelajaran',
            'description' => 'Evaluasi KBM semester berjalan',
            'created_by' => $creator->id,
            'is_active' => true,
            'start_at' => now()->subDay(),
            'end_at' => now()->addDays(7),
        ]);

        $originalSurvey->questions()->create([
            'question_text' => 'Bagaimana pemahaman Anda?',
            'type' => 'multiple_choice',
            'options' => ['Sangat Baik', 'Baik', 'Cukup'],
            'order' => 0,
        ]);

        $originalSurvey->questions()->create([
            'question_text' => 'Tuliskan saran Anda',
            'type' => 'essay',
            'options' => null,
            'order' => 1,
        ]);

        $originalSurvey->targets()->attach($targetUser->id);

        $response = $this->actingAs($creator)->post(route('surveys.duplicate', $originalSurvey));

        $response->assertRedirect(route('surveys.index'));
        $response->assertSessionHas('success');

        $duplicate = Survey::where('title', 'Survei Evaluasi Pembelajaran (Salinan)')->first();
        $this->assertNotNull($duplicate);
        $this->assertEquals($creator->id, $duplicate->created_by);
        $this->assertEquals('Evaluasi KBM semester berjalan', $duplicate->description);
        $this->assertFalse((bool) $duplicate->is_active);
        $this->assertNull($duplicate->start_at);
        $this->assertNull($duplicate->end_at);

        // Questions duplicated
        $this->assertCount(2, $duplicate->questions);
        $this->assertEquals('Bagaimana pemahaman Anda?', $duplicate->questions[0]->question_text);
        $this->assertEquals(['Sangat Baik', 'Baik', 'Cukup'], $duplicate->questions[0]->options);
        $this->assertEquals('Tuliskan saran Anda', $duplicate->questions[1]->question_text);

        // Targets duplicated
        $this->assertTrue($duplicate->targets->contains('id', $targetUser->id));

        // Responses are 0
        $this->assertCount(0, $duplicate->responses);
    }

    public function test_can_edit_and_update_active_survey(): void
    {
        Role::firstOrCreate(['name' => 'Guru Kelas']);
        $creator = User::factory()->create();
        $targetUser = User::factory()->create();

        $survey = Survey::create([
            'title' => 'Survei Aktif',
            'description' => 'Deskripsi survei aktif',
            'created_by' => $creator->id,
            'is_active' => true,
            'start_at' => now(),
            'end_at' => now()->addDays(5),
        ]);

        $survey->questions()->create([
            'question_text' => 'Pertanyaan awal?',
            'type' => 'multiple_choice',
            'options' => ['Ya', 'Tidak'],
            'order' => 0,
        ]);

        $survey->targets()->attach($targetUser->id);

        // Can access edit page without being redirected back
        $editResponse = $this->actingAs($creator)->get(route('surveys.edit', $survey));
        $editResponse->assertOk();
        $editResponse->assertViewIs('pages.surveys.edit');

        // Can update active survey
        $updateResponse = $this->actingAs($creator)->put(route('surveys.update', $survey), [
            'title' => 'Survei Aktif Terupdate',
            'description' => 'Deskripsi baru',
            'start_at' => now()->format('Y-m-d\TH:i'),
            'end_at' => now()->addDays(10)->format('Y-m-d\TH:i'),
            'is_active' => 1,
            'questions' => [
                [
                    'question_text' => 'Pertanyaan terupdate?',
                    'type' => 'multiple_choice',
                    'options' => ['Puas', 'Tidak Puas'],
                ],
            ],
            'target_users' => [$targetUser->id],
        ]);

        $updateResponse->assertRedirect(route('surveys.index'));
        $updateResponse->assertSessionHas('success');

        $survey->refresh();
        $this->assertEquals('Survei Aktif Terupdate', $survey->title);
        $this->assertEquals('Deskripsi baru', $survey->description);
        $this->assertEquals('Pertanyaan terupdate?', $survey->questions->first()->question_text);
    }

    public function test_updating_survey_with_existing_responses_preserves_question_answers(): void
    {
        Role::firstOrCreate(['name' => 'Guru Kelas']);
        $creator = User::factory()->create();
        $respondent = User::factory()->create();

        $survey = Survey::create([
            'title' => 'Survei Dengan Respon',
            'description' => 'Deskripsi',
            'created_by' => $creator->id,
            'is_active' => true,
        ]);

        $question = $survey->questions()->create([
            'question_text' => 'Apakah sarana memadai?',
            'type' => 'multiple_choice',
            'options' => ['Ya', 'Tidak'],
            'order' => 0,
        ]);

        $survey->targets()->attach($respondent->id);

        // Submit response
        $response = $survey->responses()->create([
            'user_id' => $respondent->id,
        ]);
        $answer = $response->answers()->create([
            'question_id' => $question->id,
            'answer_value' => 'Ya',
        ]);

        // Creator updates survey, preserving question ID
        $updateResponse = $this->actingAs($creator)->put(route('surveys.update', $survey), [
            'title' => 'Survei Dengan Respon (Revisi)',
            'description' => 'Deskripsi revisi',
            'start_at' => null,
            'end_at' => null,
            'is_active' => 1,
            'questions' => [
                [
                    'id' => $question->id,
                    'question_text' => 'Apakah sarana dan prasarana memadai?',
                    'type' => 'multiple_choice',
                    'options' => ['Ya', 'Tidak'],
                ],
                [
                    'id' => null,
                    'question_text' => 'Pertanyaan baru tambahan',
                    'type' => 'essay',
                    'options' => null,
                ],
            ],
            'target_users' => [$respondent->id],
        ]);

        $updateResponse->assertRedirect(route('surveys.index'));

        // Check that answer and question ID were NOT deleted
        $this->assertDatabaseHas('survey_answers', [
            'id' => $answer->id,
            'question_id' => $question->id,
            'answer_value' => 'Ya',
        ]);

        $survey->refresh();
        $this->assertCount(2, $survey->questions);
        $this->assertEquals('Apakah sarana dan prasarana memadai?', $survey->questions->where('id', $question->id)->first()->question_text);
    }
}
