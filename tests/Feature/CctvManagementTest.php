<?php

namespace Tests\Feature;

use App\Models\CctvCamera;
use App\Models\User;
use App\Services\CctvPlaybackToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CctvManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.cctv.mediamtx_api_url' => 'http://mediamtx.test',
            'services.cctv.hls_base_url' => 'https://media.example.test',
            'services.cctv.gateway_auth_key' => 'gateway-test-secret',
            'services.cctv.playback_token_secret' => 'playback-test-secret',
            'services.cctv.playback_token_ttl' => 900,
        ]);

        Http::fake([
            'http://mediamtx.test/v3/info' => Http::response(['version' => 'test']),
            'http://mediamtx.test/v3/config/paths/get/*' => Http::response([], 404),
            'http://mediamtx.test/v3/config/paths/add/*' => Http::response([], 200),
            'http://mediamtx.test/v3/config/paths/patch/*' => Http::response([], 200),
            'http://mediamtx.test/v3/config/paths/delete/*' => Http::response([], 200),
        ]);
    }

    public function test_super_admin_can_create_camera_and_assign_non_student_user(): void
    {
        $admin = $this->userWithRole('Super Admin');
        $teacher = $this->userWithRole('Guru Kelas');

        $this->actingAs($admin)
            ->withSession(['active_role' => 'Super Admin'])
            ->post(route('super-admin.cctv.store'), [
                'name' => 'Gerbang Utama',
                'location' => 'Pintu masuk sekolah',
                'rtsp_url' => 'rtsp://viewer:secret@192.168.10.20:554/substream',
                'sort_order' => 1,
                'is_active' => '1',
                'user_ids' => [$teacher->id],
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $camera = CctvCamera::firstOrFail();
        $this->assertTrue($camera->users()->whereKey($teacher->id)->exists());
        $this->assertSame('synced', $camera->last_sync_status);
        $this->assertStringNotContainsString(
            'viewer:secret',
            (string) DB::table('cctv_cameras')->where('id', $camera->id)->value('rtsp_url')
        );

        $this->actingAs($admin)
            ->withSession(['active_role' => 'Super Admin'])
            ->get(route('super-admin.cctv.index'))
            ->assertOk()
            ->assertSee('Manajemen CCTV Sekolah')
            ->assertSee('Gerbang Utama');
    }

    public function test_student_cannot_be_assigned_to_camera(): void
    {
        $admin = $this->userWithRole('Super Admin');
        $student = $this->userWithRole('Siswa');

        $this->actingAs($admin)
            ->withSession(['active_role' => 'Super Admin'])
            ->post(route('super-admin.cctv.store'), [
                'name' => 'Lapangan',
                'rtsp_url' => 'rtsp://camera.local/live',
                'is_active' => '1',
                'user_ids' => [$student->id],
            ])
            ->assertSessionHasErrors('user_ids');

        $this->assertDatabaseCount('cctv_cameras', 0);
    }

    public function test_only_assigned_non_student_user_can_request_playback_token(): void
    {
        $teacher = $this->userWithRole('Guru Kelas');
        $otherTeacher = $this->userWithRole('Guru Kelas');
        $camera = $this->camera();
        $camera->users()->attach($teacher);

        $this->actingAs($teacher)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->postJson(route('cctv-live.token', $camera))
            ->assertOk()
            ->assertJsonPath('manifest_url', 'https://media.example.test/'.$camera->stream_path.'/index.m3u8')
            ->assertJsonStructure(['token', 'expires_at', 'manifest_url']);

        $this->actingAs($otherTeacher)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->postJson(route('cctv-live.token', $camera))
            ->assertForbidden();
    }

    public function test_mediamtx_gateway_accepts_valid_token_and_rejects_revoked_access(): void
    {
        $teacher = $this->userWithRole('Guru Kelas');
        $camera = $this->camera();
        $camera->users()->attach($teacher);
        $issued = app(CctvPlaybackToken::class)->issue($teacher, $camera);

        $payload = [
            'token' => $issued['token'],
            'action' => 'read',
            'path' => $camera->stream_path,
            'protocol' => 'hls',
            'ip' => '127.0.0.1',
        ];

        $this->postJson(route('api.cctv.gateway.auth', ['key' => 'gateway-test-secret']), $payload)
            ->assertNoContent();

        $camera->users()->detach($teacher);

        $this->postJson(route('api.cctv.gateway.auth', ['key' => 'gateway-test-secret']), $payload)
            ->assertForbidden();
    }

    public function test_assigned_teacher_can_open_live_page_and_student_role_is_blocked(): void
    {
        $teacher = $this->userWithRole('Guru Kelas');
        $camera = $this->camera();
        $camera->users()->attach($teacher);

        $this->actingAs($teacher)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->get(route('cctv-live.index'))
            ->assertOk()
            ->assertSee('CCTV Live')
            ->assertSee($camera->name);

        $student = $this->userWithRole('Siswa');
        $camera->users()->attach($student);

        $this->actingAs($student)
            ->withSession(['active_role' => 'Siswa'])
            ->get(route('cctv-live.index'))
            ->assertForbidden();
    }

    private function camera(): CctvCamera
    {
        return CctvCamera::create([
            'name' => 'Koridor Lantai 1',
            'slug' => 'koridor-lantai-1',
            'location' => 'Gedung A',
            'rtsp_url' => 'rtsp://viewer:secret@192.168.10.30:554/substream',
            'stream_path' => 'cctv-test-path',
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
