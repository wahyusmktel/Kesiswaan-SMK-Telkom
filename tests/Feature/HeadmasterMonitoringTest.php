<?php

namespace Tests\Feature;

use App\Http\Controllers\KepalaSekolah\MonitoringController;
use App\Models\FingerprintAttendance;
use App\Models\FingerprintDevice;
use App\Models\Keterlambatan;
use App\Models\MasterGuru;
use App\Models\MasterSiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HeadmasterMonitoringTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        $this->withoutVite();
    }

    private function login(string $roleName = 'Kepala Sekolah'): User
    {
        $user = User::factory()->create();
        $role = Role::findOrCreate($roleName, 'web');
        $role->givePermissionTo(Permission::findOrCreate('view executive dashboard', 'web'));
        $user->assignRole($role);
        $this->actingAs($user)->withSession(['active_role' => $roleName]);

        return $user;
    }

    public function test_navigation_matches_requested_visibility_and_monitoring_order(): void
    {
        $user = $this->login();
        $user->givePermissionTo(Permission::findOrCreate('view erapor', 'web'));
        $user->assignRole(Role::findOrCreate('Super Admin', 'web'));
        $response = $this->get(route('kepala-sekolah.monitoring.index', 'jadwal'))->assertOk();

        $response->assertSeeInOrder(['Manajemen OKR', ...array_values(MonitoringController::SECTIONS)]);
        foreach (['erapor.index', 'cctv-live.index', 'forum-stella.index', 'penilaian.index'] as $route) {
            $response->assertDontSee('href="'.route($route).'"', false);
        }
        foreach (['kepala-sekolah.dashboard.index', 'okr.index', 'tanda-tangan.index', 'stella-ai.index', 'cloud-files.index', 'gallery-photo.index', 'shortener.index', 'tes-iq.start', 'surveys.index', 'notted.landing', 'notted.games', 'shared.nde.index', 'inventaris-aset.index', 'inventaris-aset.borrow-history', 'profile.edit'] as $route) {
            $response->assertSee('href="'.route($route).'"', false);
        }
    }

    public function test_all_monitoring_pages_are_accessible_but_not_writable(): void
    {
        $this->login();
        foreach (MonitoringController::SECTIONS as $section => $title) {
            $url = route('kepala-sekolah.monitoring.index', $section);
            $this->get($url)->assertOk()->assertSee($title)->assertSee('Tidak ada data sesuai filter.');
            $this->post($url, [])->assertStatus(405);
        }
        $this->get(route('kepala-sekolah.monitoring.index', ['section' => 'izin-siswa', 'jenis_data' => 'keluar']))->assertOk();
    }

    public function test_other_roles_cannot_read_monitoring_even_with_the_permission(): void
    {
        $this->login('Guru Kelas');
        foreach (array_keys(MonitoringController::SECTIONS) as $section) {
            $this->get(route('kepala-sekolah.monitoring.index', $section))->assertForbidden();
        }
    }

    public function test_lateness_filters_include_students_without_accounts(): void
    {
        $headmaster = $this->login();
        $student = MasterSiswa::create(['nis' => 'MON-1', 'nama_lengkap' => 'Siswa Monitoring', 'jenis_kelamin' => 'L']);
        foreach (['2026-09-03 07:30:00', '2026-08-20 07:30:00'] as $date) {
            Keterlambatan::create(['master_siswa_id' => $student->id, 'dicatat_oleh_security_id' => $headmaster->id, 'waktu_dicatat_security' => $date, 'alasan_siswa' => $date, 'status' => 'dicatat_security']);
        }
        $this->get(route('kepala-sekolah.monitoring.index', ['section' => 'keterlambatan', 'from' => '2026-09-01', 'to' => '2026-09-04', 'q' => 'Monitoring']))
            ->assertOk()->assertSee('Siswa Monitoring')->assertSee('2026-09-03 07:30:00')->assertDontSee('2026-08-20 07:30:00');
        $this->getJson(route('kepala-sekolah.monitoring.index', ['section' => 'keterlambatan', 'from' => 'invalid']))->assertUnprocessable();
    }

    public function test_fingerprint_only_displays_mapped_teachers(): void
    {
        $this->login();
        $teacher = User::factory()->create(['name' => 'Guru Fingerprint Test']);
        $other = User::factory()->create(['name' => 'Bukan Guru Test']);
        MasterGuru::create(['nama_lengkap' => $teacher->name, 'jenis_kelamin' => 'L', 'user_id' => $teacher->id]);
        $device = FingerprintDevice::create(['name' => 'Mesin Test', 'ip_address' => '127.0.0.1']);
        foreach ([$teacher, $other] as $user) {
            FingerprintAttendance::create(['fingerprint_device_id' => $device->id, 'user_id' => (string) $user->id, 'app_user_id' => $user->id, 'timestamp' => now(), 'punch' => '0']);
        }
        $this->get(route('kepala-sekolah.monitoring.index', 'fingerprint'))->assertOk()->assertSee($teacher->name)->assertDontSee($other->name)->assertSee('Total Kehadiran')->assertDontSee('Kode Punch');
    }

    public function test_daily_teacher_recap_counts_people_and_separates_dates_and_duplicate_scans(): void
    {
        $this->login();
        $teacher = User::factory()->create();
        MasterGuru::create(['nama_lengkap' => 'Guru Hadir', 'jenis_kelamin' => 'L', 'user_id' => $teacher->id]);
        MasterGuru::create(['nama_lengkap' => 'Guru Belum Dipetakan', 'jenis_kelamin' => 'P']);
        $device = FingerprintDevice::create(['name' => 'Mesin A', 'ip_address' => '127.0.0.1']);
        $second = FingerprintDevice::create(['name' => 'Mesin B', 'ip_address' => '127.0.0.2']);
        foreach (['2026-09-03 06:55:00', '2026-09-03 08:00:00', '2026-09-03 16:20:00', '2026-09-04 07:10:00'] as $timestamp) {
            FingerprintAttendance::create(['fingerprint_device_id' => $device->id, 'user_id' => '1', 'app_user_id' => $teacher->id, 'timestamp' => $timestamp]);
        }
        FingerprintAttendance::create(['fingerprint_device_id' => $second->id, 'user_id' => '1', 'app_user_id' => $teacher->id, 'timestamp' => '2026-09-04 07:10:00']);
        $this->get(route('kepala-sekolah.monitoring.index', ['section' => 'fingerprint', 'date' => '2026-09-03']))
            ->assertOk()
            ->assertViewHas('summary', ['total' => 2, 'present' => 1, 'out' => 1, 'missing' => 1])
            ->assertViewHas('rows', fn ($rows) => $rows->firstWhere('name', 'Guru Hadir')['check_in'] === '06:55' && $rows->firstWhere('name', 'Guru Hadir')['check_out'] === '16:20');
        $this->get(route('kepala-sekolah.monitoring.index', ['section' => 'fingerprint', 'date' => '2026-09-04']))
            ->assertOk()->assertViewHas('summary', ['total' => 2, 'present' => 1, 'out' => 0, 'missing' => 1])
            ->assertViewHas('rows', fn ($rows) => $rows->firstWhere('name', 'Guru Hadir')['check_out'] === null);
        $this->get(route('kepala-sekolah.monitoring.index', ['section' => 'fingerprint', 'date' => '2026-09-05']))
            ->assertOk()->assertViewHas('summary', ['total' => 2, 'present' => 0, 'out' => 0, 'missing' => 2]);
        $this->getJson(route('kepala-sekolah.monitoring.index', ['section' => 'fingerprint', 'date' => 'invalid']))->assertUnprocessable();
    }

    public function test_headmaster_can_access_personal_asset_services(): void
    {
        $this->login();
        Http::fake(['*' => Http::response(['data' => [], 'total' => 0, 'last_page' => 1, 'current_page' => 1])]);
        $this->get(route('inventaris-aset.index'))->assertOk();
        $this->get(route('inventaris-aset.borrow-history'))->assertOk();
    }
}
