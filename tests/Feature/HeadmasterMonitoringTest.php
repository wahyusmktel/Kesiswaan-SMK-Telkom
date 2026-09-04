<?php

namespace Tests\Feature;

use App\Http\Controllers\KepalaSekolah\MonitoringController;
use App\Jobs\SyncFingerprintAttendancesJob;
use App\Models\FingerprintAttendance;
use App\Models\FingerprintDevice;
use App\Models\FingerprintUser;
use App\Models\Keterlambatan;
use App\Models\MasterGuru;
use App\Models\MasterSiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
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

    public function test_headmaster_can_queue_today_only_for_active_mapped_devices(): void
    {
        Queue::fake();
        config(['queue.default' => 'database']);
        $user = $this->login();
        $device = FingerprintDevice::create(['name' => 'Aktif', 'ip_address' => '127.0.0.1', 'is_active' => true]);
        FingerprintDevice::create(['name' => 'Tanpa Mapping', 'ip_address' => '127.0.0.2', 'is_active' => true]);
        $inactive = FingerprintDevice::create(['name' => 'Nonaktif', 'ip_address' => '127.0.0.3', 'is_active' => false]);
        foreach ([$device, $inactive] as $item) {
            FingerprintUser::create(['fingerprint_device_id' => $item->id, 'user_id' => '1', 'app_user_id' => $user->id]);
        }
        $response = $this->postJson(route('kepala-sekolah.fingerprint-sync.store'), ['date' => '2000-01-01', 'device_id' => $inactive->id])->assertOk();
        Queue::assertPushed(SyncFingerprintAttendancesJob::class, 1);
        Queue::assertPushed(SyncFingerprintAttendancesJob::class, fn ($job) => $job->deviceId === $device->id && $job->dateFrom === today()->toDateString() && $job->dateTo === today()->toDateString() && $job->sendDailyRecaps === false && $job->queue === 'fingerprint');
        $this->getJson($response->json('status_url'))->assertOk()->assertJsonPath('jobs.0.status', 'queued');
        $this->postJson(route('kepala-sekolah.fingerprint-sync.store'))->assertStatus(429);
        $this->login();
        $this->getJson($response->json('status_url'))->assertNotFound();
    }

    public function test_teacher_cannot_trigger_headmaster_sync(): void
    {
        Queue::fake();
        $this->login('Guru Kelas');
        $this->postJson(route('kepala-sekolah.fingerprint-sync.store'))->assertForbidden();
        Queue::assertNothingPushed();
    }

    public function test_sync_rejects_missing_devices_and_sync_queue(): void
    {
        Queue::fake();
        config(['queue.default' => 'database']);
        $this->login();
        $this->postJson(route('kepala-sekolah.fingerprint-sync.store'))->assertUnprocessable();
        $this->login();
        config(['queue.default' => 'sync']);
        $this->postJson(route('kepala-sekolah.fingerprint-sync.store'))->assertUnprocessable();
        Queue::assertNothingPushed();
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
            ->assertViewHas('summary', ['total' => 2, 'present' => 1, 'out' => 1, 'missing' => 1, 'required' => 0, 'required_present' => 0, 'required_missing' => 0, 'unclassified' => 2])
            ->assertViewHas('rows', fn ($rows) => $rows->firstWhere('name', 'Guru Hadir')['check_in'] === '06:55' && $rows->firstWhere('name', 'Guru Hadir')['check_out'] === '16:20');
        $this->get(route('kepala-sekolah.monitoring.index', ['section' => 'fingerprint', 'date' => '2026-09-04']))
            ->assertOk()->assertViewHas('summary', ['total' => 2, 'present' => 1, 'out' => 0, 'missing' => 1, 'required' => 0, 'required_present' => 0, 'required_missing' => 0, 'unclassified' => 2])
            ->assertViewHas('rows', fn ($rows) => $rows->firstWhere('name', 'Guru Hadir')['check_out'] === null);
        $this->get(route('kepala-sekolah.monitoring.index', ['section' => 'fingerprint', 'date' => '2026-09-05']))
            ->assertOk()->assertViewHas('summary', ['total' => 2, 'present' => 0, 'out' => 0, 'missing' => 2, 'required' => 0, 'required_present' => 0, 'required_missing' => 0, 'unclassified' => 2]);
        $this->getJson(route('kepala-sekolah.monitoring.index', ['section' => 'fingerprint', 'date' => 'invalid']))->assertUnprocessable();
    }

    public function test_percentage_uses_employment_schedule_and_work_calendar(): void
    {
        $principal = $this->login();
        $year = \App\Models\TahunPelajaran::create(['tahun' => '2026/2027', 'semester' => 'Ganjil', 'is_active' => true]);
        $class = \App\Models\Kelas::create(['nama_kelas' => 'X Test', 'jurusan' => 'TKJ']);
        $rombel = \App\Models\Rombel::create(['tahun_ajaran' => '2026/2027', 'tahun_pelajaran_id' => $year->id, 'kelas_id' => $class->id, 'wali_kelas_id' => $principal->id]);
        $subject = \App\Models\MataPelajaran::create(['kode_mapel' => 'TEST', 'nama_mapel' => 'Test']);
        $device = FingerprintDevice::create(['name' => 'Test', 'ip_address' => '127.0.0.1']);
        foreach (['Pegawai Tetap', 'Pegawai Full Time', 'Pegawai Part Time', 'Pegawai Part Time'] as $i => $status) {
            $user = User::factory()->create();
            $teacher = MasterGuru::create(['nama_lengkap' => 'Guru '.$i, 'jenis_kelamin' => 'L', 'user_id' => $user->id]);
            \App\Models\DapodikGuru::create(['master_guru_id' => $teacher->id, 'nama' => $teacher->nama_lengkap, 'status_kepegawaian' => $status]);
            if ($i === 2) {
                \App\Models\JadwalPelajaran::create(['master_guru_id' => $teacher->id, 'rombel_id' => $rombel->id, 'mata_pelajaran_id' => $subject->id, 'hari' => 'Jumat', 'jam_ke' => 1, 'jam_mulai' => '10:00:00', 'jam_selesai' => '12:00:00']);
            }
            if ($i !== 1) {
                FingerprintAttendance::create(['fingerprint_device_id' => $device->id, 'user_id' => (string) $user->id, 'app_user_id' => $user->id, 'timestamp' => '2026-09-04 09:50:00']);
            }
        }
        $url = route('kepala-sekolah.monitoring.index', ['section' => 'fingerprint', 'date' => '2026-09-04']);
        $this->get($url)->assertOk()->assertSee('66.7%')
            ->assertViewHas('summary', fn ($s) => $s['required'] === 3 && $s['required_present'] === 2 && $s['present'] === 3)
            ->assertViewHas('rows', fn ($rows) => $rows->firstWhere('name', 'Guru 2')['obligation'] === 'Jadwal 10:00–12:00' && ! $rows->firstWhere('name', 'Guru 3')['required']);
        MasterGuru::where('nama_lengkap', 'Guru 0')->update(['is_active' => false]);
        $this->get($url)->assertOk()
            ->assertViewHas('summary', fn ($s) => $s['total'] === 3 && $s['required'] === 2 && $s['required_present'] === 1 && $s['present'] === 2)
            ->assertViewHas('rows', fn ($rows) => ! $rows->contains('name', 'Guru 0'));
        $this->assertDatabaseCount('fingerprint_attendances', 3);
        MasterGuru::where('nama_lengkap', 'Guru 0')->update(['is_active' => true]);
        $year->update(['is_active' => false]);
        $this->get($url)->assertOk()->assertViewHas('summary', fn ($s) => $s['required'] === 2 && $s['required_present'] === 1);
        \App\Models\WorkCalendarEvent::create(['title' => 'Libur Test', 'type' => 'holiday', 'is_non_working' => true, 'date_from' => '2026-09-04', 'date_to' => '2026-09-04']);
        $this->get($url)->assertOk()->assertViewHas('summary', fn ($s) => $s['required'] === 0 && $s['required_present'] === 0)->assertSee('Tidak ada guru wajib hadir');
        $this->get(route('kepala-sekolah.monitoring.index', ['section' => 'fingerprint', 'date' => '2026-09-05']))->assertOk()->assertViewHas('summary', fn ($s) => $s['required'] === 0);
    }

    public function test_headmaster_can_access_personal_asset_services(): void
    {
        $this->login();
        Http::fake(['*' => Http::response(['data' => [], 'total' => 0, 'last_page' => 1, 'current_page' => 1])]);
        $this->get(route('inventaris-aset.index'))->assertOk();
        $this->get(route('inventaris-aset.borrow-history'))->assertOk();
    }
}
