<?php

namespace Tests\Feature;

use App\Models\AssetReport;
use App\Models\AssetReportBuilding;
use App\Models\AssetReportLocation;
use App\Models\User;
use App\Support\DashboardRedirector;
use Database\Seeders\KaurSarpraRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AssetReportingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        $this->withoutVite();
    }

    public function test_migration_provides_common_school_buildings_and_locations(): void
    {
        $this->assertDatabaseCount('asset_report_buildings', 4);
        $this->assertGreaterThanOrEqual(20, AssetReportLocation::count());
        $this->assertDatabaseHas('asset_report_locations', ['name' => 'Toilet Pria', 'type' => 'toilet']);
        $this->assertDatabaseHas('asset_report_locations', ['name' => 'Aula Sekolah', 'type' => 'aula']);
    }

    public function test_kaur_sarpra_role_seeder_is_idempotent(): void
    {
        $this->seed(KaurSarpraRoleSeeder::class);
        $this->seed(KaurSarpraRoleSeeder::class);

        $this->assertSame(1, Role::where('name', 'KAUR SARPRA')->where('guard_name', 'web')->count());
        $this->assertSame('super-admin.asset-report-qrs.index', DashboardRedirector::routeNameForRole('KAUR SARPRA'));
    }

    public function test_super_admin_can_provision_kaur_sarpra_role_from_the_web(): void
    {
        $admin = $this->userWithRole('Super Admin');

        $this->assertDatabaseMissing('roles', ['name' => 'KAUR SARPRA', 'guard_name' => 'web']);

        $this->actingAs($admin)->withSession(['active_role' => 'Super Admin'])
            ->post(route('super-admin.roles.kaur-sarpra.provision'))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('roles', ['name' => 'KAUR SARPRA', 'guard_name' => 'web']);
    }

    public function test_non_super_admin_cannot_provision_kaur_sarpra_role_from_the_web(): void
    {
        $teacher = $this->userWithRole('Guru Kelas');

        $this->actingAs($teacher)->withSession(['active_role' => 'Guru Kelas'])
            ->post(route('super-admin.roles.kaur-sarpra.provision'))
            ->assertForbidden();

        $this->assertDatabaseMissing('roles', ['name' => 'KAUR SARPRA', 'guard_name' => 'web']);
    }

    public function test_public_can_open_qr_page_and_submit_asset_report(): void
    {
        Storage::fake('local');
        $location = AssetReportLocation::with('building')->firstOrFail();

        $this->get(route('asset-report.public.create', $location))
            ->assertOk()
            ->assertSee($location->name)
            ->assertSee($location->building->name);

        $response = $this->post(route('asset-report.public.store', $location), [
            'reporter_name' => 'Budi Siswa',
            'reporter_identifier' => '123456',
            'reporter_type' => 'siswa',
            'contact' => '08123456789',
            'asset_name' => 'Keran wastafel',
            'category' => 'air_sanitasi',
            'urgency' => 'tinggi',
            'description' => 'Keran wastafel patah dan air terus mengalir.',
            'photo' => UploadedFile::fake()->image('kerusakan.jpg'),
        ]);

        $report = AssetReport::firstOrFail();
        $response->assertRedirect(route('asset-report.public.success', [$location, $report->ticket_number]));
        $this->assertStringStartsWith('AST-', $report->ticket_number);
        Storage::disk('local')->assertExists($report->photo_path);
    }

    public function test_inactive_location_cannot_receive_public_reports(): void
    {
        $location = AssetReportLocation::firstOrFail();
        $location->update(['is_active' => false]);

        $this->get(route('asset-report.public.create', $location))->assertNotFound();
    }

    public function test_only_super_admin_can_manage_qr_and_reports(): void
    {
        $superAdmin = $this->userWithRole('Super Admin');
        $kaurSarpra = $this->userWithRole('KAUR SARPRA');
        $teacher = $this->userWithRole('Guru Kelas');

        $this->actingAs($teacher)->withSession(['active_role' => 'Guru Kelas'])
            ->get(route('super-admin.asset-report-qrs.index'))->assertForbidden();

        $this->actingAs($superAdmin)->withSession(['active_role' => 'Super Admin'])
            ->get(route('super-admin.asset-report-qrs.index'))
            ->assertOk()->assertSee('QR Laporan Aset')->assertSee('Gedung 1');

        $this->actingAs($kaurSarpra)->withSession(['active_role' => 'KAUR SARPRA'])
            ->get(route('super-admin.asset-report-qrs.index'))
            ->assertOk()
            ->assertSee('QR Laporan Aset')
            ->assertSee('Gedung 1')
            ->assertSee('data-kaur-sarpra-navigation', false);

        $this->actingAs($kaurSarpra)->withSession(['active_role' => 'KAUR SARPRA'])
            ->get(route('super-admin.asset-reports.index'))
            ->assertOk()->assertSee('Pengelolaan Laporan Aset');
    }

    public function test_super_admin_can_create_location_and_update_report_status(): void
    {
        $admin = $this->userWithRole('Super Admin');
        $building = AssetReportBuilding::firstOrFail();

        $this->actingAs($admin)->withSession(['active_role' => 'Super Admin'])
            ->post(route('super-admin.asset-report-locations.store'), [
                'asset_report_building_id' => $building->id,
                'name' => 'Ruang Server',
                'code' => 'GDG1-SRV',
                'type' => 'ruang_kerja',
                'floor' => 'Lantai 2',
                'is_active' => '1',
                'sort_order' => 50,
            ])->assertSessionHas('success');

        $location = AssetReportLocation::where('code', 'GDG1-SRV')->firstOrFail();
        $this->assertTrue(Str::isUuid($location->public_token));

        $report = AssetReport::create([
            'asset_report_location_id' => $location->id,
            'ticket_number' => 'AST-TEST-001',
            'reporter_name' => 'Pelapor',
            'reporter_type' => 'siswa',
            'asset_name' => 'AC Server',
            'category' => 'rusak',
            'urgency' => 'darurat',
            'description' => 'AC tidak menyala dan ruang server sangat panas.',
        ]);

        $this->actingAs($admin)->withSession(['active_role' => 'Super Admin'])
            ->patch(route('super-admin.asset-reports.update', $report), [
                'status' => 'diproses',
                'admin_notes' => 'Teknisi sudah dihubungi.',
            ])->assertSessionHas('success');

        $this->assertDatabaseHas('asset_reports', [
            'id' => $report->id,
            'status' => 'diproses',
            'handled_by' => $admin->id,
        ]);
    }

    public function test_super_admin_can_download_a4_qr_pdf(): void
    {
        $admin = $this->userWithRole('Super Admin');
        $location = AssetReportLocation::firstOrFail();

        $this->assertFileExists(public_path('images/asset-report/smk-telkom-lampung-white.png'));

        $response = $this->actingAs($admin)->withSession(['active_role' => 'Super Admin'])
            ->get(route('super-admin.asset-report-qrs.print', ['location_id' => $location->id]));

        $response->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole(Role::findOrCreate($role, 'web'));

        return $user;
    }
}
