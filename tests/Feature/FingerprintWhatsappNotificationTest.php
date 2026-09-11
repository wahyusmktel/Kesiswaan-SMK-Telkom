<?php

namespace Tests\Feature;

use App\Models\FingerprintAttendance;
use App\Models\FingerprintAttendanceSetting;
use App\Models\FingerprintDevice;
use App\Models\GuruIzin;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MasterGuru;
use App\Models\MataPelajaran;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
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

    public function test_it_sends_late_and_absent_reminders_once_and_skips_approved_leave(): void
    {
        $late = $this->userWithRole('Guru Kelas', '081211110001');
        $absent = $this->userWithRole('Guru Kelas', '081211110002');
        $onTime = $this->userWithRole('Guru Kelas', '081211110003');
        $leave = $this->userWithRole('Guru Kelas', '081211110004');
        $this->createAttendance($late, '2026-07-23 08:00:00');
        $this->createAttendance($onTime, '2026-07-23 07:00:00');
        GuruIzin::create([
            'master_guru_id' => $leave->masterGuru->id,
            'tanggal_mulai' => '2026-07-23 00:00:00',
            'tanggal_selesai' => '2026-07-23 23:59:59',
            'jenis_izin' => 'Sakit',
            'deskripsi' => 'Istirahat',
            'status_sdm' => 'disetujui',
            'status_kepala_sekolah' => 'disetujui',
        ]);

        $service = app(FingerprintWhatsappNotificationService::class);
        $first = $service->sendRemindersToday();
        $second = $service->sendRemindersToday();

        $this->assertSame(2, $first['sent']);
        $this->assertSame(4, $second['skipped']);
        $this->assertDatabaseCount('whatsapp_logs', 2);
        $this->assertDatabaseHas('whatsapp_logs', [
            'recipient_user_id' => $late->id,
            'event_key' => FingerprintWhatsappNotificationService::REMINDER_EVENT_KEY,
            'type' => 'fingerprint_peringatan',
        ]);
        $this->assertDatabaseHas('whatsapp_logs', [
            'recipient_user_id' => $absent->id,
            'event_key' => FingerprintWhatsappNotificationService::REMINDER_EVENT_KEY,
        ]);
        $this->assertStringContainsString('Terlambat', WhatsappLog::where('recipient_user_id', $late->id)->value('message'));
        $this->assertStringContainsString('Tidak Hadir', WhatsappLog::where('recipient_user_id', $absent->id)->value('message'));
        $this->assertDatabaseMissing('whatsapp_logs', ['recipient_user_id' => $onTime->id]);
        $this->assertDatabaseMissing('whatsapp_logs', ['recipient_user_id' => $leave->id]);
    }

    public function test_it_reminds_full_time_employee_ten_minutes_after_checkin_deadline(): void
    {
        $employee = $this->userWithRole('Guru Kelas', '081211110010');
        FingerprintAttendanceSetting::getSetting()->update(['checkin_end' => '07:30:00']);
        $service = app(FingerprintWhatsappNotificationService::class);

        $beforeDue = $service->sendCheckinRemindersNow(Carbon::parse('2026-07-23 07:39:00'));
        $whenDue = $service->sendCheckinRemindersNow(Carbon::parse('2026-07-23 07:40:00'));
        $duplicate = $service->sendCheckinRemindersNow(Carbon::parse('2026-07-23 07:41:00'));

        $this->assertSame(0, $beforeDue['sent']);
        $this->assertSame(1, $whenDue['sent']);
        $this->assertSame(1, $duplicate['skipped']);
        $this->assertDatabaseHas('whatsapp_logs', [
            'recipient_user_id' => $employee->id,
            'event_key' => FingerprintWhatsappNotificationService::CHECKIN_REMINDER_LOG_EVENT_KEY,
            'type' => 'fingerprint_peringatan',
        ]);
        $this->assertStringContainsString('Belum Check-in', WhatsappLog::firstOrFail()->message);
    }

    public function test_part_time_checkin_reminder_follows_first_teaching_schedule(): void
    {
        $employee = $this->userWithRole('Guru Kelas', '081211110011');
        $employee->masterGuru->dapodikGuru()->update(['status_kepegawaian' => 'Pegawai Part Time']);
        $period = TahunPelajaran::create(['tahun' => '2026/2027', 'semester' => 'Ganjil', 'is_active' => true]);
        $classroom = Kelas::create(['nama_kelas' => 'X TKJ 1', 'jurusan' => 'TKJ']);
        $rombel = Rombel::create([
            'tahun_ajaran' => $period->tahun,
            'tahun_pelajaran_id' => $period->id,
            'kelas_id' => $classroom->id,
            'wali_kelas_id' => $employee->id,
        ]);
        $subject = MataPelajaran::create(['kode_mapel' => 'TKJ', 'nama_mapel' => 'Produktif TKJ']);
        JadwalPelajaran::create([
            'master_guru_id' => $employee->masterGuru->id,
            'rombel_id' => $rombel->id,
            'mata_pelajaran_id' => $subject->id,
            'hari' => 'Kamis',
            'jam_ke' => 1,
            'jam_mulai' => '09:00:00',
            'jam_selesai' => '10:30:00',
        ]);

        $service = app(FingerprintWhatsappNotificationService::class);
        $this->assertSame(0, $service->sendCheckinRemindersNow(Carbon::parse('2026-07-23 09:09:00'))['sent']);
        $this->assertSame(1, $service->sendCheckinRemindersNow(Carbon::parse('2026-07-23 09:10:00'))['sent']);
        $this->assertStringContainsString('09:00', WhatsappLog::firstOrFail()->message);
    }

    public function test_tpa_uses_daily_checkin_deadline_even_when_employment_is_part_time(): void
    {
        $employee = $this->userWithRole('TPA', '081211110012');
        $employee->masterGuru->update(['employee_category' => MasterGuru::CATEGORY_TPA]);
        $employee->masterGuru->dapodikGuru()->update(['status_kepegawaian' => 'Pegawai Part Time']);
        FingerprintAttendanceSetting::getSetting()->update(['checkin_end' => '07:30:00']);

        $result = app(FingerprintWhatsappNotificationService::class)
            ->sendCheckinRemindersNow(Carbon::parse('2026-07-23 07:40:00'));

        $this->assertSame(1, $result['sent']);
        $this->assertStringContainsString('07:30', WhatsappLog::firstOrFail()->message);
    }

    public function test_manual_recap_uses_separate_log_and_does_not_consume_the_scheduled_delivery(): void
    {
        $employee = $this->userWithRole('Guru Kelas', '081211119999');
        $this->createAttendance($employee, '2026-07-23 07:00:00');
        $service = app(FingerprintWhatsappNotificationService::class);

        $this->assertSame(1, $service->sendToday(today(), true)['sent']);
        $this->assertSame(1, $service->sendToday()['sent']);

        $this->assertDatabaseHas('whatsapp_logs', [
            'recipient_user_id' => $employee->id,
            'event_key' => FingerprintWhatsappNotificationService::EVENT_KEY.'_manual',
            'type' => 'fingerprint_rekap_manual',
        ]);
        $this->assertDatabaseHas('whatsapp_logs', [
            'recipient_user_id' => $employee->id,
            'event_key' => FingerprintWhatsappNotificationService::EVENT_KEY,
            'type' => 'fingerprint_rekap',
        ]);
    }

    private function userWithRole(string $roleName, ?string $phoneNumber = null): User
    {
        $role = Role::findOrCreate($roleName, 'web');
        $user = User::factory()->create(['phone_number' => $phoneNumber]);
        $user->assignRole($role);
        if ($roleName !== 'Siswa') {
            $teacher = MasterGuru::create(['nama_lengkap' => $user->name, 'jenis_kelamin' => 'L', 'user_id' => $user->id]);
            $teacher->dapodikGuru()->create(['nama' => $user->name, 'status_kepegawaian' => 'Pegawai Full Time']);
        }

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
