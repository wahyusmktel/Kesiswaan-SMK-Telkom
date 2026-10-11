<?php

namespace Tests\Feature;

use App\Models\TeacherPortfolio;
use App\Models\TeacherProfile;
use App\Models\User;
use Database\Seeders\TeacherPortfolioDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherPortfolioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TeacherPortfolioDemoSeeder::class);
    }

    public function test_public_teacher_directory_loads_successfully(): void
    {
        $response = $this->get('/pegawai');

        $response->assertStatus(200);
        $response->assertSee('Kenali Profil', false);
        $response->assertSee('Muhammad Fajar Siddiq', false);
    }

    public function test_employee_aliases_redirect_or_load_directory(): void
    {
        $response1 = $this->get('/employee');
        $response1->assertRedirect('/pegawai');

        $response2 = $this->get('/employees');
        $response2->assertRedirect('/pegawai');
    }

    public function test_public_teacher_show_page_loads_with_seo_metadata_and_json_ld(): void
    {
        $profile = TeacherProfile::where('is_published', true)->first();

        $this->assertNotNull($profile, 'Teacher profile must exist');

        $response = $this->get('/pegawai/' . $profile->slug);

        $response->assertStatus(200);
        $response->assertSee($profile->nama_lengkap);

        // Assert SEO metadata
        $response->assertSee('<meta name="description"', false);
        $response->assertSee('<link rel="canonical"', false);
        $response->assertSee('property="og:type" content="profile"', false);

        // Assert Google schema.org JSON-LD
        $response->assertSee('application/ld+json', false);
        $response->assertSee('schema.org', false);
        $response->assertSee('ProfilePage', false);
        $response->assertSee('Person', false);
    }

    public function test_sitemap_xml_renders_valid_xml_with_teacher_profiles(): void
    {
        $profile = TeacherProfile::where('is_published', true)->first();

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $this->assertStringContainsString('xml', $response->headers->get('Content-Type'));
        $response->assertSee('<urlset', false);

        if ($profile) {
            $response->assertSee(route('pegawai.show', $profile->slug), false);
        }
    }

    public function test_teacher_can_manage_portfolio_crud(): void
    {
        $user = User::where('email', 'fajar.siddiq@smktelkom-mlg.sch.id')->first()
            ?? User::factory()->create();

        $this->actingAs($user);

        // 1. Visit edit page
        $editResponse = $this->get(route('teacher.portfolio.edit'));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Profil Publik', false);

        // 2. Add new project
        $storeResponse = $this->post(route('teacher.portfolio.project.store'), [
            'judul' => 'Inovasi Smart School IoT Vokasi 2026',
            'kategori' => 'Hardware & IoT',
            'deskripsi' => 'Pengembangan smart gateway telemetri berbasis ESP32.',
            'tahun' => '2026',
            'tautan_proyek' => 'https://github.com/smktelkom/smart-gateway-test',
            'teknologi' => 'C++, FreeRTOS, MQTT, Grafana',
            'is_featured' => 1,
        ]);

        $storeResponse->assertRedirect(route('teacher.portfolio.edit'));

        $createdPortfolio = TeacherPortfolio::where('judul', 'Inovasi Smart School IoT Vokasi 2026')->first();
        $this->assertNotNull($createdPortfolio);
        $this->assertEquals('Hardware & IoT', $createdPortfolio->kategori);
        $this->assertContains('FreeRTOS', $createdPortfolio->teknologi);

        // 3. Delete the project
        $deleteResponse = $this->delete(route('teacher.portfolio.project.destroy', $createdPortfolio->id));
        $deleteResponse->assertRedirect(route('teacher.portfolio.edit'));

        $this->assertNull(TeacherPortfolio::find($createdPortfolio->id));
    }
}
