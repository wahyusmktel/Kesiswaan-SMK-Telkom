<?php

namespace Tests\Feature;

use App\Models\SsoApplication;
use App\Models\SsoUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SsoProviderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'sso.domain' => 'sso.smktelkom-lpg.id',
            'sso.url' => 'https://sso.smktelkom-lpg.id',
            'sso.enforce_domain' => false,
        ]);
    }

    public function test_super_admin_can_register_public_pkce_application(): void
    {
        $admin = $this->userWithRole('Super Admin');

        $response = $this->actingAs($admin)
            ->withSession(['active_role' => 'Super Admin'])
            ->post(route('super-admin.sso-applications.store'), [
                'name' => 'Portal Kurikulum',
                'description' => 'Aplikasi kurikulum sekolah.',
                'homepage_url' => 'https://kurikulum.example.test',
                'client_type' => 'public_pkce',
                'redirect_uris' => ['https://kurikulum.example.test/auth/callback'],
            ]);

        $response->assertRedirect(route('super-admin.sso-applications.index'))
            ->assertSessionHas('sso_credentials');

        $application = SsoApplication::with('client')->firstOrFail();
        $this->assertSame('Portal Kurikulum', $application->client->name);
        $this->assertFalse($application->client->confidential());
        $this->assertSame(['https://kurikulum.example.test/auth/callback'], $application->client->redirect_uris);
    }

    public function test_non_super_admin_cannot_manage_sso_applications(): void
    {
        $teacher = $this->userWithRole('Guru Kelas');

        $this->actingAs($teacher)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->get(route('super-admin.sso-applications.index'))
            ->assertForbidden();
    }

    public function test_sso_login_uses_existing_sisfo_account_and_preserves_intended_url(): void
    {
        $user = User::factory()->create([
            'email' => 'guru@example.test',
            'password' => Hash::make('secret-password'),
        ]);
        $intended = 'https://sso.smktelkom-lpg.id/oauth/authorize?client_id=test-client';

        $this->withSession(['url.intended' => $intended])
            ->post('https://sso.smktelkom-lpg.id/masuk', [
                'email' => $user->email,
                'password' => 'secret-password',
            ])
            ->assertRedirect($intended);

        $this->assertAuthenticatedAs($user);
    }

    public function test_oauth_userinfo_returns_only_the_supported_profile_claims(): void
    {
        $webUser = $this->userWithRole('Guru Kelas');
        $ssoUser = SsoUser::findOrFail($webUser->id);
        Passport::actingAs($ssoUser, ['profile:read'], 'api');

        $this->getJson('/api/sso/user')
            ->assertOk()
            ->assertJsonPath('sub', (string) $webUser->id)
            ->assertJsonPath('email', $webUser->email)
            ->assertJsonPath('roles.0', 'Guru Kelas')
            ->assertJsonMissingPath('password');
    }

    public function test_public_client_can_complete_authorization_code_pkce_flow(): void
    {
        $user = $this->userWithRole('Guru Kelas');
        $client = app(ClientRepository::class)->createAuthorizationCodeGrantClient(
            name: 'Aplikasi Uji',
            redirectUris: ['https://client.example.test/callback'],
            confidential: false,
        );
        SsoApplication::create([
            'passport_client_id' => $client->id,
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        $verifier = str_repeat('a', 64);
        $challenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
        $query = http_build_query([
            'client_id' => $client->id,
            'redirect_uri' => 'https://client.example.test/callback',
            'response_type' => 'code',
            'scope' => 'profile:read',
            'state' => 'state-value',
            'code_challenge' => $challenge,
            'code_challenge_method' => 'S256',
        ]);

        $authorizeResponse = $this->actingAs($user)
            ->get('/oauth/authorize?'.$query)
            ->assertOk()
            ->assertSee('Aplikasi Uji')
            ->assertSee('Izinkan Akses');

        $authToken = session('authToken');
        $approval = $this->post('/oauth/authorize', [
            'state' => 'state-value',
            'client_id' => $client->id,
            'auth_token' => $authToken,
        ])->assertRedirect();

        parse_str((string) parse_url($approval->headers->get('Location'), PHP_URL_QUERY), $callbackQuery);
        $this->assertSame('state-value', $callbackQuery['state'] ?? null);
        $this->assertNotEmpty($callbackQuery['code'] ?? null);

        $tokenResponse = $this->postJson('/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => $client->id,
            'redirect_uri' => 'https://client.example.test/callback',
            'code' => $callbackQuery['code'],
            'code_verifier' => $verifier,
        ])->assertOk()->assertJsonStructure(['token_type', 'expires_in', 'access_token', 'refresh_token']);

        $this->withToken($tokenResponse->json('access_token'))
            ->getJson('/api/sso/user')
            ->assertOk()
            ->assertJsonPath('email', $user->email);
    }

    private function userWithRole(string $roleName): User
    {
        $role = Role::findOrCreate($roleName, 'web');
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole($role);

        return $user;
    }
}
