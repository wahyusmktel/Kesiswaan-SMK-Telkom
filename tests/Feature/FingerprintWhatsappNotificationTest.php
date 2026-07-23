<?php

namespace Tests\Feature;

use App\Models\FingerprintAttendance;
use App\Models\FingerprintDevice;
use App\Models\User;
use App\Models\WhatsappDevice;
use App\Models\WhatsappLog;
use App\Models\WhatsappTemplate;
use App\Services\FingerprintWhatsappNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FingerprintWhatsappNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-07-23 16:30:00');

        WhatsappDevice::create([
            'name' => 'Gateway Test',
            'session_id' => 'fingerprint-test',
            'provider' => 'fonnte',
            'api_key' => 'DEMO_API_KEY_SMK_TELKOM_2026',
            'status' => 'connected',
            'is_active' => true,
            'is_default' => true,
        ]);

        WhatsappTemplate::where('event_key', FingerprintWhatsappNotificationService::EVENT_KEY)->update([
            'title' => 'Rekap Harian',
            'category' => 'presensi',
            'is_enabled' => true,
            'template_text' => '{nama_pegawai}|{tanggal}|{jam_masuk}|{jam_pulang}|{total_scan}|{status_kehadiran}|{catatan}|{durasi_terlambat}',
            'variables' => [],
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_it_sends_one_daily_recap_per_employee(): void
    {
        $employee = $this->userWithRole('Guru Kelas', '0812-3456-7890');
        $this->createAttendance($employee, '2026-07-23 07:15:00');
        $this->createAttendance($employee, '2026-07-23 16:00:00');

        $service = app(FingerprintWhatsappNotificationService::class);
        $firstResult = $service->sendToday();
        $secondResult = $service->sendToday();

        $this->assertSame(1, $firstResult['sent']);
        $this->assertSame(1, $secondResult['skipped']);
        $this->assertDatabaseCount('whatsapp_logs', 1);
        $this->assertDatabaseHas('whatsapp_logs', [
            'recipient_user_id' => $employee->id,
            'recipient' => '6281234567890',
            'event_key' => FingerprintWhatsappNotificationService::EVENT_KEY,
            'notification_date' => '2026-07-23 00:00:00',
            'type' => 'fingerprint_rekap',
            'status' => 'sent',
        ]);

        $message = WhatsappLog::firstOrFail()->message;
        $this->assertStringContainsString('07:15', $message);
        $this->assertStringContainsString('16:00', $message);
        $this->assertStringContainsString('Hadir Lengkap', $message);
    }

    public function test_it_skips_students_and_users_without_phone_numbers(): void
    {
        $student = $this->userWithRole('Siswa', '081211112222');
        $employeeWithoutPhone = $this->userWithRole('Guru Kelas');
        $this->createAttendance($student, '2026-07-23 07:00:00');
        $this->createAttendance($employeeWithoutPhone, '2026-07-23 07:00:00');

        $result = app(FingerprintWhatsappNotificationService::class)->sendToday();

        $this->assertSame(0, $result['sent']);
        $this->assertDatabaseCount('whatsapp_logs', 0);
    }

    public function test_disabled_template_stops_daily_recap_notifications(): void
    {
        WhatsappTemplate::where('event_key', FingerprintWhatsappNotificationService::EVENT_KEY)
            ->update(['is_enabled' => false]);

        $employee = $this->userWithRole('Guru Kelas', '081211112222');
        $this->createAttendance($employee, '2026-07-23 07:00:00');

        $result = app(FingerprintWhatsappNotificationService::class)->sendToday();

        $this->assertTrue($result['disabled']);
        $this->assertDatabaseCount('whatsapp_logs', 0);
    }

    private function userWithRole(string $roleName, ?string $phoneNumber = null): User
    {
        $role = Role::findOrCreate($roleName, 'web');
        $user = User::factory()->create(['phone_number' => $phoneNumber]);
        $user->assignRole($role);

        return $user;
    }

    private function createAttendance(User $user, string $timestamp): void
    {
        $device = FingerprintDevice::firstOrCreate(
            ['ip_address' => '127.0.0.2'],
            ['name' => 'Mesin Test', 'port' => 4370, 'is_active' => true],
        );

        FingerprintAttendance::create([
            'fingerprint_device_id' => $device->id,
            'uid' => $user->id,
            'user_id' => (string) $user->id,
            'app_user_id' => $user->id,
            'timestamp' => $timestamp,
            'status' => '0',
            'punch' => '0',
        ]);
    }
}
