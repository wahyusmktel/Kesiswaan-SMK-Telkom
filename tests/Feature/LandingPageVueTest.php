<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\LandingHeroSlide;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LandingPageVueTest extends TestCase
{
    use RefreshDatabase;

    public function test_vue_theme_renders_the_integrated_landing_page(): void
    {
        AppSetting::create([
            'school_name' => 'SMK Telkom Lampung',
            'theme' => 'stella-vue',
        ]);

        $this->get(route('welcome'))
            ->assertOk()
            ->assertSee('stella-vue-landing')
            ->assertSee('Satu sekolah. Satu data. Lebih banyak dampak.')
            ->assertSee('window.__STELLA_LANDING__', false);
    }

    public function test_super_admin_can_manage_a_hero_slide_and_ticker(): void
    {
        Storage::fake('public');
        $user = $this->superAdmin();

        $this->actingAs($user)
            ->withSession(['active_role' => 'Super Admin'])
            ->post(route('super-admin.landing-page.slides.store'), [
                'eyebrow' => 'Agenda Sekolah',
                'title' => 'Menyambut Tahun Pelajaran Baru',
                'description' => 'Informasi layanan sekolah untuk seluruh warga sekolah.',
                'image' => UploadedFile::fake()->image('tahun-baru.jpg', 1600, 900),
                'cta_label' => 'Lihat Informasi',
                'cta_url' => '/berita',
                'sort_order' => 1,
                'is_active' => '1',
            ])
            ->assertRedirect();

        $slide = LandingHeroSlide::firstOrFail();
        Storage::disk('public')->assertExists($slide->image_path);
        $this->assertTrue($slide->is_active);

        $this->actingAs($user)
            ->withSession(['active_role' => 'Super Admin'])
            ->post(route('super-admin.landing-page.tickers.store'), [
                'text' => 'Pendaftaran siswa baru telah dibuka.',
                'url' => '/registrasi-siswa-baru',
                'sort_order' => 1,
                'is_active' => '1',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('landing_tickers', [
            'text' => 'Pendaftaran siswa baru telah dibuka.',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->withSession(['active_role' => 'Super Admin'])
            ->get(route('super-admin.landing-page.index'))
            ->assertOk()
            ->assertSee('Kelola Landing Page')
            ->assertSee('Menyambut Tahun Pelajaran Baru')
            ->assertSee('Pendaftaran siswa baru telah dibuka.');
    }

    private function superAdmin(): User
    {
        $role = Role::findOrCreate('Super Admin', 'web');
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole($role);

        return $user;
    }
}
