<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\StellaAiConversation;
use App\Models\StellaAiMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StellaAiTest extends TestCase
{
    use RefreshDatabase;

    public function test_disabled_ai_rejects_user_pages_and_endpoints(): void
    {
        $user = User::factory()->create();
        AppSetting::create(['stella_ai_enabled' => false]);

        $this->actingAs($user)
            ->get(route('stella-ai.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->postJson(route('stella-ai.conversations.create'))
            ->assertForbidden();
    }

    public function test_user_can_only_read_their_own_conversation(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->enabledSetting();

        $conversation = StellaAiConversation::create([
            'user_id' => $owner->id,
            'title' => 'Percakapan pemilik',
        ]);

        StellaAiMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => 'Data privat',
            'type' => 'text',
        ]);

        $this->actingAs($otherUser)
            ->getJson(route('stella-ai.conversations.messages', $conversation->id))
            ->assertNotFound();
    }

    public function test_chat_response_is_saved_to_the_owned_conversation(): void
    {
        Http::fake([
            'https://ai.example/v1/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Jawaban Stella']],
                ],
            ]),
        ]);

        $user = User::factory()->create();
        $this->enabledSetting();
        $conversation = StellaAiConversation::create([
            'user_id' => $user->id,
            'title' => 'Percakapan Baru',
        ]);

        $this->actingAs($user)
            ->postJson(route('stella-ai.send'), [
                'conversation_id' => $conversation->id,
                'message' => 'Apa itu cloud computing?',
                'type' => 'text',
            ])
            ->assertOk()
            ->assertJsonPath('message.content', 'Jawaban Stella');

        $this->assertDatabaseHas('stella_ai_messages', [
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => 'Apa itu cloud computing?',
        ]);
        $this->assertDatabaseHas('stella_ai_messages', [
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => 'Jawaban Stella',
        ]);
        $this->assertSame('Apa itu cloud computing?', $conversation->fresh()->title);
    }

    public function test_generated_image_is_attached_to_the_requested_conversation(): void
    {
        Storage::fake('public');
        Http::fake([
            'https://ai.example/v1/images/generations' => Http::response([
                'data' => [
                    ['b64_json' => base64_encode('fake-image')],
                ],
            ]),
        ]);

        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();
        $this->enabledSetting();

        $firstConversation = StellaAiConversation::create([
            'user_id' => $firstUser->id,
            'title' => 'Percakapan pertama',
        ]);
        $secondConversation = StellaAiConversation::create([
            'user_id' => $secondUser->id,
            'title' => 'Percakapan kedua',
        ]);

        StellaAiMessage::create([
            'conversation_id' => $secondConversation->id,
            'role' => 'user',
            'content' => 'Buat ilustrasi sekolah',
            'type' => 'image_request',
        ]);

        $response = $this->actingAs($firstUser)
            ->postJson(route('stella-ai.send'), [
                'conversation_id' => $firstConversation->id,
                'message' => 'Buat ilustrasi sekolah',
                'type' => 'image_request',
            ])
            ->assertOk();

        $imagePath = $response->json('message.image_path');

        $this->assertDatabaseHas('stella_ai_messages', [
            'conversation_id' => $firstConversation->id,
            'role' => 'assistant',
            'image_path' => $imagePath,
            'type' => 'image_response',
        ]);
        $this->assertDatabaseMissing('stella_ai_messages', [
            'conversation_id' => $secondConversation->id,
            'role' => 'assistant',
            'image_path' => $imagePath,
        ]);
        Storage::disk('public')->assertExists($imagePath);
    }

    public function test_super_admin_can_update_settings_without_erasing_saved_api_key(): void
    {
        $role = Role::findOrCreate('Super Admin', 'web');
        $admin = User::factory()->create(['email_verified_at' => now()]);
        $admin->assignRole($role);

        $setting = $this->enabledSetting();
        $originalKey = $setting->stella_ai_api_key;

        $this->actingAs($admin)
            ->withSession(['active_role' => 'Super Admin'])
            ->post(route('super-admin.stella-ai.settings.update'), [
                'stella_ai_base_url' => 'https://new-ai.example/v1',
                'stella_ai_api_key' => '',
                'stella_ai_chat_model' => 'new-chat-model',
                'stella_ai_image_model' => 'new-image-model',
                'stella_ai_enabled' => '1',
            ])
            ->assertRedirect();

        $setting->refresh();
        $this->assertSame($originalKey, $setting->stella_ai_api_key);
        $this->assertSame('https://new-ai.example/v1', $setting->stella_ai_base_url);
        $this->assertNotSame(
            $originalKey,
            (string) $setting->getRawOriginal('stella_ai_api_key')
        );
    }

    public function test_enabled_chat_and_settings_pages_render_without_exposing_api_key(): void
    {
        $role = Role::findOrCreate('Super Admin', 'web');
        $admin = User::factory()->create(['email_verified_at' => now()]);
        $admin->assignRole($role);
        $this->enabledSetting();

        $this->actingAs($admin)
            ->withSession(['active_role' => 'Super Admin'])
            ->get(route('stella-ai.index'))
            ->assertOk()
            ->assertSee('Halo! Saya Stella AI')
            ->assertSee('Mode Gambar');

        $this->actingAs($admin)
            ->withSession(['active_role' => 'Super Admin'])
            ->get(route('super-admin.stella-ai.settings'))
            ->assertOk()
            ->assertSee('Aktif dan siap')
            ->assertDontSee('secret-api-key')
            ->assertDontSee('\x27');
    }

    private function enabledSetting(): AppSetting
    {
        return AppSetting::create([
            'stella_ai_base_url' => 'https://ai.example/v1',
            'stella_ai_api_key' => 'secret-api-key',
            'stella_ai_chat_model' => 'chat-model',
            'stella_ai_image_model' => 'image-model',
            'stella_ai_enabled' => true,
        ]);
    }
}
