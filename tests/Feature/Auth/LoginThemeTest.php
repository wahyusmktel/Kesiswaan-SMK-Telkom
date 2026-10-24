<?php

namespace Tests\Feature\Auth;

use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LoginThemeTest extends TestCase
{
    use RefreshDatabase;

    public function test_classic_remains_default_and_owl_preserves_all_login_methods(): void
    {
        $setting = AppSetting::create(['school_name' => 'SMK Telkom Lampung']);
        $this->get('/login')->assertOk()->assertSee('data-login-theme="classic"', false);

        $setting->update(['login_theme' => 'owl', 'login_heading' => 'Selamat datang di SISFO']);
        // Public settings are shared once when the application boots.
        view()->share('appSetting', $setting->fresh());
        $this->get('/login')->assertOk()
            ->assertSee('data-login-theme="owl"', false)
            ->assertSee('Selamat datang di SISFO')
            ->assertSee('images/login/sisfo-owl-background.png')
            ->assertSee('images/login/sisfo-logo.png')
            ->assertSee('Masuk dengan Google')
            ->assertSee('Masuk dengan Stella Access Card')
            ->assertSee('Masuk dengan Face ID')
            ->assertSee('Registrasi Siswa Baru')
            ->assertDontSee('particles.min.js');
    }

    public function test_super_admin_can_customize_preview_and_restore_login_theme(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('Super Admin', 'web'));
        $setting = AppSetting::create(['school_name' => 'SMK Telkom Lampung', 'theme' => 'stella-vue']);

        $this->actingAs($admin)->get(route('super-admin.settings'))
            ->assertOk()->assertSee('Tampilan Halaman Login')->assertSee('SISFO Owl');

        $this->actingAs($admin)->post(route('super-admin.settings.update'), [
            'school_name' => 'SMK Telkom Lampung',
            'login_theme' => 'owl',
            'login_heading' => 'Halo, warga sekolah!',
            'login_description' => 'Mari terhubung bersama SISFO.',
            'login_background' => UploadedFile::fake()->image('background.png', 800, 1200),
            'login_logo' => UploadedFile::fake()->image('logo.png'),
        ])->assertSessionHasNoErrors()->assertRedirect();

        $setting->refresh();
        $this->assertSame('owl', $setting->login_theme);
        $this->assertSame('stella-vue', $setting->theme);
        Storage::disk('public')->assertExists([$setting->login_background, $setting->login_logo]);

        $this->get(route('super-admin.settings.login-preview', ['theme' => 'classic']))
            ->assertOk()->assertSee('data-login-theme="classic"', false)->assertSee('Pratinjau tema login');
        $this->assertSame('owl', $setting->fresh()->login_theme);

        $this->get(route('super-admin.settings.login-preview', ['theme' => 'owl']))
            ->assertOk()->assertSee('Halo, warga sekolah!')->assertSee('Mari terhubung bersama SISFO.');

        $this->post(route('super-admin.settings.update'), [
            'school_name' => 'SMK Telkom Lampung',
            'login_theme' => 'classic',
            'reset_login_background' => '1',
            'reset_login_logo' => '1',
        ])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertNull($setting->fresh()->login_background);
        $this->assertNull($setting->fresh()->login_logo);
        $this->assertSame('classic', $setting->fresh()->login_theme);
    }

    public function test_other_roles_cannot_change_or_preview_login_settings(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('Guru Kelas', 'web'));
        $this->actingAs($user)->get(route('super-admin.settings.login-preview', ['theme' => 'owl']))->assertForbidden();
        $this->post(route('super-admin.settings.update'), ['school_name' => 'Sekolah', 'login_theme' => 'owl'])->assertForbidden();
    }

    public function test_invalid_theme_and_executable_uploads_are_rejected(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('Super Admin', 'web'));
        $this->actingAs($admin)->post(route('super-admin.settings.update'), [
            'school_name' => 'Sekolah',
            'login_theme' => 'unknown',
            'login_logo' => UploadedFile::fake()->create('script.svg', 1, 'image/svg+xml'),
        ])->assertSessionHasErrors(['login_theme', 'login_logo']);
    }
}
