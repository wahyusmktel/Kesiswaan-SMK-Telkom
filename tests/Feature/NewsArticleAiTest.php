<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\Berita;
use App\Models\BeritaComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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
                            'seo_title' => 'Pembelajaran Digital Terintegrasi',
                            'seo_description' => 'Pelajari penguatan pembelajaran digital terintegrasi di lingkungan sekolah.',
                            'focus_keyword' => 'pembelajaran digital terintegrasi',
                            'seo_keywords' => ['pembelajaran digital', 'teknologi sekolah'],
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
                'instructions' => 'Bahas manfaat layanan akademik terpadu.',
                'include_code_snippets' => false,
            ]);

        $response->assertOk()
            ->assertJsonPath('article.title', 'Pembelajaran Digital Semakin Terintegrasi')
            ->assertJsonPath('article.paragraph_count', 2)
            ->assertJsonPath('article.sentence_count', 6)
            ->assertJsonPath('article.focus_keyword', 'pembelajaran digital terintegrasi')
            ->assertJsonPath('article.recommended_by_ai', false);

        Http::assertSent(function ($request) {
            return $request['model'] === 'glm-5.2'
                && str_contains($request['messages'][1]['content'], 'Buat tepat 2 paragraf')
                && str_contains($request['messages'][1]['content'], 'sekitar 3 kalimat')
                && str_contains($request['messages'][1]['content'], 'Bahas manfaat layanan akademik terpadu');
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
                'include_code_snippets' => false,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['paragraph_count', 'sentences_per_paragraph']);
    }

    public function test_ai_keeps_fenced_code_and_requests_explicit_language_labels(): void
    {
        Http::fake([
            'https://ai.example/v1/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => json_encode([
                    'title' => 'Validasi Form Laravel',
                    'summary' => 'Panduan praktis validasi formulir.',
                    'content' => "## Controller\n\n```php\n<?php\nreturn ['ok' => true];\n```",
                    'seo_title' => 'Tutorial Validasi Form Laravel',
                    'seo_description' => 'Pelajari validasi formulir Laravel melalui contoh controller yang praktis.',
                    'focus_keyword' => 'validasi form Laravel',
                    'seo_keywords' => ['Laravel', 'validasi form'],
                ])]]],
            ]),
        ]);

        $this->configureAi();

        $response = $this->actingAs($this->superAdmin())
            ->withSession(['active_role' => 'Super Admin'])
            ->postJson(route('super-admin.berita.generate-ai'), [
                'kategori' => 'Akademik',
                'use_ai_recommendation' => true,
                'instructions' => 'Buat tutorial validasi formulir Laravel.',
                'include_code_snippets' => true,
            ]);

        $response->assertOk()
            ->assertJsonPath('article.content', "## Controller\n\n```php\n<?php\nreturn ['ok' => true];\n```")
            ->assertJsonPath('article.seo_title', 'Tutorial Validasi Form Laravel');

        Http::assertSent(fn ($request) => str_contains(
            $request['messages'][1]['content'],
            'fenced code block Markdown dengan nama bahasa'
        ));
    }

    public function test_stella_vue_news_page_contains_seo_related_news_and_comment_form(): void
    {
        AppSetting::create([
            'school_name' => 'SMK Telkom Lampung',
            'theme' => 'stella-vue',
        ]);
        $author = User::factory()->create();
        $article = $this->publishedArticle($author, 'Tutorial Laravel', 'tutorial-laravel');
        $related = $this->publishedArticle($author, 'Berita Laravel Terkait', 'laravel-terkait');

        $response = $this->get(route('berita.show', $article->slug));

        $response->assertOk()
            ->assertSee('<script type="application/ld+json">', false)
            ->assertSee('rel="canonical"', false)
            ->assertSee('data-article-content', false)
            ->assertSee('Berita Laravel Terkait')
            ->assertSee('Kirim untuk Moderasi');
    }

    public function test_public_comment_requires_captcha_and_stays_pending_until_approved(): void
    {
        AppSetting::create(['school_name' => 'SMK Telkom Lampung', 'theme' => 'stella-vue']);
        $article = $this->publishedArticle(User::factory()->create(), 'Artikel Diskusi', 'artikel-diskusi');
        $token = 'known-token';

        $response = $this->withSession([
            'news_comment_captchas' => [
                $token => [
                    'berita_id' => $article->id,
                    'answer_hash' => Hash::make('7'),
                    'expires_at' => now()->addMinutes(10)->timestamp,
                ],
            ],
        ])->post(route('berita.comments.store', $article), [
            'name' => 'Pembaca',
            'email' => 'pembaca@example.com',
            'content' => 'Artikel ini sangat membantu.',
            'captcha_token' => $token,
            'captcha_answer' => 7,
        ]);

        $response->assertRedirect(route('berita.show', $article->slug));
        $comment = BeritaComment::firstOrFail();
        $this->assertSame('pending', $comment->status);

        $this->get(route('berita.show', $article->slug))
            ->assertDontSee('Artikel ini sangat membantu.');

        $comment->update(['status' => 'approved']);
        $this->get(route('berita.show', $article->slug))
            ->assertSee('Artikel ini sangat membantu.');
    }

    public function test_super_admin_can_approve_a_pending_reply(): void
    {
        $admin = $this->superAdmin();
        $article = $this->publishedArticle($admin, 'Artikel Balasan', 'artikel-balasan');
        $parent = BeritaComment::create([
            'berita_id' => $article->id,
            'name' => 'Pembaca Pertama',
            'email' => 'pertama@example.com',
            'content' => 'Komentar utama.',
            'status' => 'approved',
        ]);
        $reply = BeritaComment::create([
            'berita_id' => $article->id,
            'parent_id' => $parent->id,
            'name' => 'Pembaca Kedua',
            'email' => 'kedua@example.com',
            'content' => 'Balasan yang menunggu moderasi.',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->withSession(['active_role' => 'Super Admin'])
            ->patch(route('super-admin.berita-comments.moderate', $reply), [
                'status' => 'approved',
            ])
            ->assertRedirect();

        $reply->refresh();
        $this->assertSame('approved', $reply->status);
        $this->assertSame($admin->id, $reply->moderated_by);
        $this->assertNotNull($reply->moderated_at);
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

    public function test_edit_page_uses_the_berita_resource_parameter(): void
    {
        $user = $this->superAdmin();
        $berita = Berita::create([
            'judul' => 'Berita Pengujian',
            'ringkasan' => 'Ringkasan berita pengujian.',
            'konten' => 'Konten berita pengujian.',
            'kategori' => 'Akademik',
            'status' => 'draft',
            'user_id' => $user->id,
        ]);

        $this->assertSame(
            url('/super-admin/berita/'.$berita->id),
            route('super-admin.berita.update', $berita)
        );

        $this->actingAs($user)
            ->withSession(['active_role' => 'Super Admin'])
            ->get(route('super-admin.berita.edit', $berita))
            ->assertOk()
            ->assertSee('Edit Berita')
            ->assertSee('Berita Pengujian');
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

    private function publishedArticle(User $author, string $title, string $slug): Berita
    {
        return Berita::create([
            'judul' => $title,
            'slug' => $slug,
            'ringkasan' => 'Ringkasan artikel untuk pengujian tampilan publik.',
            'konten' => "## Contoh\n\nArtikel dengan kode berikut.\n\n```php\n<?php echo 'Halo';\n```",
            'kategori' => 'Akademik',
            'status' => 'published',
            'published_at' => now(),
            'user_id' => $author->id,
        ]);
    }
}
