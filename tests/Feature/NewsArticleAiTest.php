<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NewsArticleAiTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_generate_a_news_draft_with_custom_length(): void
    {
        Http::fake([
            'https://ai.example/v1/chat/completions' => Http::response([
                'choices' => [[
                    'message' => [
                        'content' => json_encode([
                            'title' => 'Pembelajaran Digital Semakin Terintegrasi',
                            'summary' => 'Sekolah terus memperkuat layanan akademik melalui pemanfaatan teknologi.',
                            'content' => "SMK Telkom Lampung terus mengembangkan layanan pembelajaran digital. Langkah ini mendukung kegiatan belajar yang lebih terarah. Guru dan siswa dapat mengakses informasi akademik secara terpadu.\n\nPenguatan layanan dilakukan secara bertahap dan berkelanjutan. Seluruh warga sekolah diajak memanfaatkan teknologi secara bertanggung jawab. Kolaborasi menjadi bagian penting dalam peningkatan kualitas pembelajaran.",
                            'paragraph_count' => 2,
                            'sentences_per_paragraph' => 3,
                        ]),
                    ],
                ]],
            ]),
        ]);

        $this->configureAi();
        $user = $this->superAdmin();

        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'Super Admin'])
            ->postJson(route('super-admin.berita.generate-ai'), [
                'kategori' => 'Akademik',
                'use_ai_recommendation' => false,
                'paragraph_count' => 2,
                'sentences_per_paragraph' => 3,
            ]);

        $response->assertOk()
            ->assertJsonPath('article.title', 'Pembelajaran Digital Semakin Terintegrasi')
            ->assertJsonPath('article.paragraph_count', 2)
            ->assertJsonPath('article.sentence_count', 6)
            ->assertJsonPath('article.recommended_by_ai', false);

        Http::assertSent(function ($request) {
            return $request['model'] === 'glm-5.2'
                && str_contains($request['messages'][1]['content'], 'Buat tepat 2 paragraf')
                && str_contains($request['messages'][1]['content'], 'sekitar 3 kalimat');
        });
    }

    public function test_custom_length_fields_are_required_when_ai_recommendation_is_disabled(): void
    {
        $this->configureAi();
        $user = $this->superAdmin();

        $this->actingAs($user)
            ->withSession(['active_role' => 'Super Admin'])
            ->postJson(route('super-admin.berita.generate-ai'), [
                'kategori' => 'Kegiatan',
                'use_ai_recommendation' => false,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['paragraph_count', 'sentences_per_paragraph']);
    }

    public function test_create_page_disables_generation_when_stella_ai_is_not_ready(): void
    {
        $user = $this->superAdmin();

        $this->actingAs($user)
            ->withSession(['active_role' => 'Super Admin'])
            ->get(route('super-admin.berita.create'))
            ->assertOk()
            ->assertSee('Hasilkan Artikel dengan Stella AI')
            ->assertSee('Belum dikonfigurasi');
    }

    private function configureAi(): void
    {
        AppSetting::create([
            'school_name' => 'SMK Telkom Lampung',
            'stella_ai_enabled' => true,
            'stella_ai_base_url' => 'https://ai.example/v1',
            'stella_ai_api_key' => 'secret-key',
            'stella_ai_chat_model' => 'glm-5.2',
        ]);
    }

    private function superAdmin(): User
    {
        $role = Role::findOrCreate('Super Admin', 'web');
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole($role);

        return $user;
    }
}
