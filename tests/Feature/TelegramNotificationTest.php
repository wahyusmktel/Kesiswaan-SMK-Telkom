<?php

namespace Tests\Feature;

use App\Models\FingerprintAttendance;
use App\Models\FingerprintAttendanceSetting;
use App\Models\FingerprintAutoSyncSetting;
use App\Models\FingerprintDevice;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\LmsAssignment;
use App\Models\MasterGuru;
use App\Models\MataPelajaran;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Models\TelegramBot;
use App\Models\TelegramLog;
use App\Models\TelegramUserLink;
use App\Models\User;
use App\Models\WhatsappTemplate;
use App\Services\FingerprintWhatsappNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TelegramNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.key' => 'base64:'.base64_encode(str_repeat('t', 32))]);
        config(['services.telegram.webhook_reply' => false]);
        Carbon::setTestNow('2026-09-08 18:00:00');
        Http::fake(fn ($request) => match (true) {
            str_ends_with($request->url(), '/getMe') => Http::response(['ok' => true, 'result' => ['username' => 'hc_smktel_bot']], 200),
            str_ends_with($request->url(), '/setWebhook') => Http::response(['ok' => true, 'result' => true], 200),
            str_ends_with($request->url(), '/setMyCommands') => Http::response(['ok' => true, 'result' => true], 200),
            str_ends_with($request->url(), '/setMyShortDescription') => Http::response(['ok' => true, 'result' => true], 200),
            str_ends_with($request->url(), '/setMyDescription') => Http::response(['ok' => true, 'result' => true], 200),
            str_ends_with($request->url(), '/sendMessage') => Http::response(['ok' => true, 'result' => ['message_id' => 10]], 200),
            default => Http::response(['ok' => false], 404),
        });
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_superadmin_can_create_a_secure_multi_purpose_bot(): void
    {
        $admin = $this->loginSuperadmin();
        $token = '123456789:AA_secure_test_token';

        $this->actingAs($admin)->withSession(['active_role' => 'Super Admin'])
            ->post(route('super-admin.telegram-bots.store'), [
                'name' => 'HC SMK Telkom Lampung',
                'slug' => 'hc-kepegawaian',
                'purpose' => 'employment',
                'bot_token' => $token,
                'is_active' => 1,
            ])->assertRedirect()->assertSessionHas('success');

        $bot = TelegramBot::firstOrFail();
        $this->assertSame('connected', $bot->status);
        $this->assertSame('hc_smktel_bot', $bot->bot_username);
        $this->assertSame($token, $bot->bot_token);
        $this->assertStringNotContainsString($token, (string) DB::table('telegram_bots')->value('bot_token'));
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/setWebhook')
            && $request['url'] === route('telegram.webhook', $bot->slug)
            && $request['secret_token'] === $bot->webhook_secret);
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/setMyCommands')
            && $request['commands'][0]['command'] === 'start');

        $this->actingAs($admin)->withSession(['active_role' => 'Super Admin'])
            ->get(route('super-admin.telegram-bots.index'))
            ->assertOk()
            ->assertSee('HC SMK Telkom Lampung');
    }

    public function test_superadmin_can_select_connected_employment_bot_as_notification_channel(): void
    {
        $admin = $this->loginSuperadmin();
        $bot = $this->createBot();

        $this->actingAs($admin)->withSession(['active_role' => 'Super Admin'])
            ->putJson(route('super-admin.whatsapp-gateway.fingerprint-notifications.update'), [
                'notifications_enabled' => true,
                'notification_time' => '18:00',
                'notification_channel' => 'telegram',
                'telegram_bot_id' => $bot->id,
            ])->assertOk();

        $this->assertDatabaseHas('fingerprint_auto_sync_settings', [
            'id' => 1,
            'notification_channel' => 'telegram',
            'telegram_bot_id' => $bot->id,
        ]);
    }

    public function test_employee_links_by_own_contact_and_receives_fingerprint_recap_via_telegram(): void
    {
        $user = User::factory()->create(['name' => 'Guru Telegram', 'phone_number' => '0812-3456-7890']);
        $teacher = MasterGuru::create(['nama_lengkap' => 'Guru Telegram', 'jenis_kelamin' => 'L', 'user_id' => $user->id]);
        $teacher->dapodikGuru()->create(['nama' => 'Guru Telegram', 'status_kepegawaian' => 'Pegawai Full Time', 'hp' => '0812-3456-7890']);
        $bot = $this->createBot();

        $this->withHeader('X-Telegram-Bot-Api-Secret-Token', $bot->webhook_secret)
            ->postJson(route('telegram.webhook', $bot->slug), [
                'message' => [
                    'chat' => ['id' => 998877, 'type' => 'private'],
                    'from' => ['id' => 998877, 'username' => 'guru_tel', 'first_name' => 'Guru'],
                    'contact' => ['phone_number' => '+6281234567890', 'user_id' => 998877],
                ],
            ])->assertOk()->assertJson(['ok' => true]);
        $this->assertDatabaseHas('telegram_user_links', ['telegram_bot_id' => $bot->id, 'user_id' => $user->id, 'chat_id' => '998877']);

        $device = FingerprintDevice::create(['name' => 'Mesin', 'ip_address' => '127.0.0.9', 'is_active' => true]);
        foreach (['07:00:00', '16:00:00'] as $time) {
            FingerprintAttendance::create(['fingerprint_device_id' => $device->id, 'user_id' => '1', 'app_user_id' => $user->id, 'timestamp' => '2026-09-08 '.$time]);
        }

        $admin = $this->loginSuperadmin();
        $this->actingAs($admin)->withSession(['active_role' => 'Super Admin'])
            ->postJson(route('super-admin.whatsapp-gateway.fingerprint-notifications.send-now'), [
                'notification_channel' => 'telegram',
                'telegram_bot_id' => $bot->id,
            ])
            ->assertOk()
            ->assertJsonPath('summary.sent', 1)
            ->assertJsonPath('summary.failed', 0);
        $this->assertDatabaseHas('fingerprint_auto_sync_settings', [
            'notification_channel' => 'telegram',
            'telegram_bot_id' => $bot->id,
        ]);
        $this->assertDatabaseHas('telegram_logs', ['telegram_bot_id' => $bot->id, 'recipient_user_id' => $user->id, 'status' => 'sent', 'event_key' => FingerprintWhatsappNotificationService::EVENT_KEY.'_manual']);
        $this->assertSame('998877', TelegramLog::firstOrFail()->chat_id);
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/sendMessage') && (string) $request['chat_id'] === '998877');
    }

    public function test_telegram_channel_sends_late_and_absent_reminders_only_once(): void
    {
        WhatsappTemplate::where('event_key', FingerprintWhatsappNotificationService::REMINDER_EVENT_KEY)->update([
            'is_enabled' => true,
            'template_text' => '{nama_pegawai}|{status_kehadiran}|{jam_masuk}|{batas_datang}|{durasi_terlambat}',
        ]);
        FingerprintAttendanceSetting::getSetting()->update(['checkin_end' => '07:30:00']);
        $bot = $this->createBot();
        FingerprintAutoSyncSetting::getSetting()->update(['notification_channel' => 'telegram', 'telegram_bot_id' => $bot->id]);

        $late = $this->createLinkedEmployee($bot, 'Guru Terlambat', '998801');
        $absent = $this->createLinkedEmployee($bot, 'Guru Tidak Hadir', '998802');
        $device = FingerprintDevice::create(['name' => 'Mesin', 'ip_address' => '127.0.0.10', 'is_active' => true]);
        FingerprintAttendance::create([
            'fingerprint_device_id' => $device->id,
            'user_id' => (string) $late->id,
            'app_user_id' => $late->id,
            'timestamp' => '2026-09-08 08:00:00',
        ]);

        $service = app(FingerprintWhatsappNotificationService::class);
        $first = $service->sendRemindersToday();
        $second = $service->sendRemindersToday();

        $this->assertSame(2, $first['sent']);
        $this->assertSame(2, $second['skipped']);
        $this->assertDatabaseCount('telegram_logs', 2);
        $this->assertStringContainsString('Terlambat', TelegramLog::where('recipient_user_id', $late->id)->value('message'));
        $this->assertStringContainsString('Tidak Hadir', TelegramLog::where('recipient_user_id', $absent->id)->value('message'));
    }

    public function test_superadmin_can_send_a_connection_test_without_attendance_data(): void
    {
        $bot = $this->createBot();
        $employee = $this->createLinkedEmployee($bot, 'Guru Uji Telegram', '998803');
        $link = TelegramUserLink::where('user_id', $employee->id)->firstOrFail();
        $admin = $this->loginSuperadmin();

        $this->actingAs($admin)->withSession(['active_role' => 'Super Admin'])
            ->post(route('super-admin.telegram-links.test', $link))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('telegram_logs', [
            'telegram_bot_id' => $bot->id,
            'recipient_user_id' => $employee->id,
            'chat_id' => '998803',
            'type' => 'connection_test',
            'event_key' => 'telegram_connection_test',
            'status' => 'sent',
        ]);
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/sendMessage')
            && (string) $request['chat_id'] === '998803'
            && str_contains($request['text'], 'TES NOTIFIKASI BERHASIL'));
    }

    public function test_linked_account_hides_onboarding_and_unlink_restores_contact_button(): void
    {
        $bot = $this->createBot();
        $employee = $this->createLinkedEmployee($bot, 'Guru Sudah Terhubung', '998804');
        $link = TelegramUserLink::where('user_id', $employee->id)->firstOrFail();

        $this->withHeader('X-Telegram-Bot-Api-Secret-Token', $bot->webhook_secret)
            ->postJson(route('telegram.webhook', $bot->slug), [
                'message' => [
                    'chat' => ['id' => 998804, 'type' => 'private'],
                    'from' => ['id' => 998804, 'first_name' => 'Guru'],
                    'text' => '/start sisfo',
                ],
            ])->assertOk();

        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/setMyCommands')
            && $request['scope']['type'] === 'chat'
            && (string) $request['scope']['chat_id'] === '998804'
            && $request['commands'][0]['command'] === 'status');
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/sendMessage')
            && str_contains($request['text'], 'sudah terhubung')
            && $request['reply_markup']['remove_keyboard'] === true);

        $this->withHeader('X-Telegram-Bot-Api-Secret-Token', $bot->webhook_secret)
            ->postJson(route('telegram.webhook', $bot->slug), [
                'message' => [
                    'chat' => ['id' => 998804, 'type' => 'private'],
                    'from' => ['id' => 998804, 'first_name' => 'Guru'],
                    'text' => '/start sisfo',
                ],
            ])->assertOk();
        $commandRequests = collect(Http::recorded())
            ->filter(fn ($record) => str_ends_with($record[0]->url(), '/setMyCommands'));
        $this->assertCount(1, $commandRequests, 'Perintah Telegram tidak boleh dikirim ulang jika menu akun tidak berubah.');

        $admin = $this->loginSuperadmin();
        $this->actingAs($admin)->withSession(['active_role' => 'Super Admin'])
            ->delete(route('super-admin.telegram-links.destroy', $link))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('telegram_user_links', ['id' => $link->id]);
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/setMyCommands')
            && $request['scope']['type'] === 'chat'
            && (string) $request['scope']['chat_id'] === '998804'
            && $request['commands'][0]['command'] === 'start');
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/sendMessage')
            && str_contains($request['text'], 'Selamat datang')
            && $request['reply_markup']['keyboard'][0][0]['request_contact'] === true);
    }

    public function test_guru_kelas_gets_leave_menu_and_can_submit_a_teacher_leave(): void
    {
        $bot = $this->createBot();
        $teacher = $this->createLinkedEmployee($bot, 'Guru Kelas Telegram', '998805');
        $teacher->assignRole(Role::findOrCreate('Guru Kelas', 'web'));
        Role::findOrCreate('Guru Piket', 'web');

        $this->sendBotMessage($bot, '998805', '/start');

        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/setMyCommands')
            && (string) $request['scope']['chat_id'] === '998805'
            && collect($request['commands'])->contains('command', 'izin'));
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/sendMessage')
            && str_contains(json_encode($request['reply_markup']), 'Ajukan Izin Guru'));

        foreach (['/izin', '🚗 Luar Sekolah / Tidak Masuk', 'Sakit', '10-09-2026 07:00', '10-09-2026 16:00', 'Perlu beristirahat sesuai arahan dokter.', '✅ Kirim Pengajuan'] as $message) {
            $this->sendBotMessage($bot, '998805', $message);
        }

        $this->assertDatabaseHas('guru_izins', [
            'master_guru_id' => $teacher->masterGuru->id,
            'jenis_izin' => 'Sakit',
            'kategori_penyetujuan' => 'luar',
            'deskripsi' => 'Perlu beristirahat sesuai arahan dokter.',
            'status_piket' => 'menunggu',
            'status_kurikulum' => 'menunggu',
            'status_sdm' => 'menunggu',
        ]);
        $this->assertDatabaseMissing('telegram_conversations', [
            'telegram_user_link_id' => TelegramUserLink::where('user_id', $teacher->id)->value('id'),
        ]);
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/sendMessage')
            && str_contains($request['text'], 'berhasil dikirim'));
    }

    public function test_guru_kelas_can_create_a_missing_lms_assignment_in_the_leave_bot_flow(): void
    {
        $bot = $this->createBot();
        $teacher = $this->createLinkedEmployee($bot, 'Guru Pembuat Tugas Telegram', '998807');
        $teacher->assignRole(Role::findOrCreate('Guru Kelas', 'web'));
        Role::findOrCreate('Guru Piket', 'web');

        $period = TahunPelajaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);
        $classroom = Kelas::create([
            'nama_kelas' => 'XII TKJ 1',
            'jurusan' => 'Teknik Komputer dan Jaringan',
        ]);
        $rombel = Rombel::create([
            'tahun_ajaran' => $period->tahun,
            'tahun_pelajaran_id' => $period->id,
            'kelas_id' => $classroom->id,
            'wali_kelas_id' => $teacher->id,
        ]);
        $subject = MataPelajaran::create([
            'kode_mapel' => 'MPTKJ',
            'nama_mapel' => 'Materi Peminatan TKJ',
        ]);
        $schedule = JadwalPelajaran::create([
            'rombel_id' => $rombel->id,
            'mata_pelajaran_id' => $subject->id,
            'master_guru_id' => $teacher->masterGuru->id,
            'hari' => 'Kamis',
            'jam_ke' => 7,
            'jam_mulai' => '12:45:00',
            'jam_selesai' => '13:30:00',
        ]);

        foreach (['/izin', '🚗 Luar Sekolah / Tidak Masuk', 'Sakit', '10-09-2026 12:00', '10-09-2026 14:00'] as $message) {
            $this->sendBotMessage($bot, '998807', $message);
        }

        $linkId = TelegramUserLink::where('user_id', $teacher->id)->value('id');
        $this->assertDatabaseHas('telegram_conversations', [
            'telegram_user_link_id' => $linkId,
            'step' => 'assignment_offer',
        ]);
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/sendMessage')
            && str_contains($request['text'], 'belum memiliki materi/tugas LMS')
            && str_contains(json_encode($request['reply_markup']), 'Buat Penugasan Sekarang'));

        foreach ([
            '📝 Buat Penugasan Sekarang',
            'Praktik konfigurasi routing',
            'Kerjakan konfigurasi routing sesuai topologi pada modul.',
            '11-09-2026 16:00',
            '💯 Gunakan 100 Poin',
            '✅ Buat & Gunakan Tugas',
        ] as $message) {
            $this->sendBotMessage($bot, '998807', $message);
        }

        $assignment = LmsAssignment::where('master_guru_id', $teacher->masterGuru->id)->firstOrFail();
        $this->assertSame('Praktik konfigurasi routing', $assignment->title);
        $this->assertSame(100, $assignment->points);
        $this->assertSame($schedule->rombel_id, $assignment->rombel_id);
        $this->assertSame($schedule->mata_pelajaran_id, $assignment->mata_pelajaran_id);
        $this->assertDatabaseHas('telegram_conversations', [
            'telegram_user_link_id' => $linkId,
            'step' => 'description',
        ]);

        foreach (['Perlu beristirahat sesuai arahan dokter.', '✅ Kirim Pengajuan'] as $message) {
            $this->sendBotMessage($bot, '998807', $message);
        }

        $izinId = DB::table('guru_izins')->where('master_guru_id', $teacher->masterGuru->id)->value('id');
        $this->assertDatabaseHas('guru_izin_jadwal', [
            'guru_izin_id' => $izinId,
            'jadwal_pelajaran_id' => $schedule->id,
            'lms_assignment_id' => $assignment->id,
        ]);
        $this->assertDatabaseMissing('telegram_conversations', [
            'telegram_user_link_id' => $linkId,
        ]);
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/sendMessage')
            && str_contains($request['text'], 'berhasil dibuat di LMS'));
    }

    public function test_non_guru_kelas_cannot_open_teacher_leave_flow_even_by_typing_command(): void
    {
        $bot = $this->createBot();
        $employee = $this->createLinkedEmployee($bot, 'Pegawai Biasa', '998806');

        $this->sendBotMessage($bot, '998806', '/izin');

        $this->assertDatabaseMissing('telegram_conversations', [
            'telegram_user_link_id' => TelegramUserLink::where('user_id', $employee->id)->value('id'),
        ]);
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/sendMessage')
            && str_contains($request['text'], 'Notifikasi dari')
            && ($request['reply_markup']['remove_keyboard'] ?? false) === true);
    }

    public function test_webhook_rejects_a_contact_owned_by_another_telegram_user(): void
    {
        $user = User::factory()->create(['phone_number' => '081234567890']);
        $bot = TelegramBot::create(['name' => 'HC', 'slug' => 'hc', 'purpose' => 'employment', 'bot_token' => '1:test', 'webhook_secret' => 'secret', 'status' => 'connected', 'is_active' => true]);

        $this->withHeader('X-Telegram-Bot-Api-Secret-Token', 'secret')->postJson(route('telegram.webhook', $bot->slug), [
            'message' => ['chat' => ['id' => 1, 'type' => 'private'], 'from' => ['id' => 1], 'contact' => ['phone_number' => '081234567890', 'user_id' => 2]],
        ])->assertOk();
        $this->assertDatabaseCount('telegram_user_links', 0);
    }

    public function test_start_command_shows_welcome_and_own_contact_button(): void
    {
        $bot = $this->createBot();

        $this->withHeader('X-Telegram-Bot-Api-Secret-Token', $bot->webhook_secret)
            ->postJson(route('telegram.webhook', $bot->slug), [
                'message' => [
                    'chat' => ['id' => 998899, 'type' => 'private'],
                    'from' => ['id' => 998899, 'first_name' => 'Guru'],
                    'text' => '/start sisfo',
                ],
            ])->assertOk();

        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/sendMessage')
            && str_contains($request['text'], 'Selamat datang di HC SMK Telkom Lampung')
            && str_contains($request['text'], 'nomor WhatsApp yang bergabung di Group Sekolah')
            && $request['reply_markup']['keyboard'][0][0]['request_contact'] === true
            && str_contains($request['reply_markup']['keyboard'][0][0]['text'], 'Bagikan Nomor HP Saya'));
    }

    public function test_webhook_can_return_the_telegram_reply_directly_without_a_second_http_request(): void
    {
        config(['services.telegram.webhook_reply' => true]);
        $bot = $this->createBot();
        $employee = $this->createLinkedEmployee($bot, 'Pegawai Respon Cepat', '998808');

        $response = $this->withHeader('X-Telegram-Bot-Api-Secret-Token', $bot->webhook_secret)
            ->postJson(route('telegram.webhook', $bot->slug), [
                'message' => [
                    'chat' => ['id' => 998808, 'type' => 'private'],
                    'from' => ['id' => 998808, 'first_name' => 'Pegawai'],
                    'text' => '/start',
                ],
            ]);

        $response->assertOk()
            ->assertJsonPath('method', 'sendMessage')
            ->assertJsonPath('chat_id', '998808');
        $this->assertStringContainsString($employee->name, $response->json('text'));
        Http::assertNotSent(fn ($request) => str_ends_with($request->url(), '/sendMessage'));
    }

    public function test_employee_can_link_using_a_phone_number_stored_in_dapodik(): void
    {
        $user = User::factory()->create(['name' => 'Guru Dapodik', 'phone_number' => null]);
        $teacher = MasterGuru::create(['nama_lengkap' => 'Guru Dapodik', 'jenis_kelamin' => 'L', 'user_id' => $user->id]);
        $teacher->dapodikGuru()->create([
            'nama' => 'Guru Dapodik',
            'status_kepegawaian' => 'Pegawai Tetap',
            'hp' => '0821-8590-3635',
        ]);
        $bot = $this->createBot();

        $this->withHeader('X-Telegram-Bot-Api-Secret-Token', $bot->webhook_secret)
            ->postJson(route('telegram.webhook', $bot->slug), [
                'message' => [
                    'chat' => ['id' => 82185903635, 'type' => 'private'],
                    'from' => ['id' => 82185903635, 'first_name' => 'Guru'],
                    'contact' => ['phone_number' => '+62 821 85903635', 'user_id' => 82185903635],
                ],
            ])->assertOk();

        $this->assertDatabaseHas('telegram_user_links', [
            'telegram_bot_id' => $bot->id,
            'user_id' => $user->id,
            'chat_id' => '82185903635',
        ]);
    }

    public function test_phone_on_user_account_alone_cannot_link_without_dapodik_hp(): void
    {
        $user = User::factory()->create(['name' => 'Guru Tanpa HP Dapodik', 'phone_number' => '082185903635']);
        $teacher = MasterGuru::create(['nama_lengkap' => $user->name, 'jenis_kelamin' => 'L', 'user_id' => $user->id]);
        $teacher->dapodikGuru()->create(['nama' => $user->name, 'status_kepegawaian' => 'Pegawai Tetap', 'hp' => null]);
        $bot = $this->createBot();

        $this->withHeader('X-Telegram-Bot-Api-Secret-Token', $bot->webhook_secret)
            ->postJson(route('telegram.webhook', $bot->slug), [
                'message' => [
                    'chat' => ['id' => 82185903635, 'type' => 'private'],
                    'from' => ['id' => 82185903635, 'first_name' => 'Guru'],
                    'contact' => ['phone_number' => '+6282185903635', 'user_id' => 82185903635],
                ],
            ])->assertOk();

        $this->assertDatabaseCount('telegram_user_links', 0);
    }

    public function test_webhook_rejects_an_invalid_secret(): void
    {
        $bot = $this->createBot();

        $this->withHeader('X-Telegram-Bot-Api-Secret-Token', 'wrong-secret')
            ->postJson(route('telegram.webhook', $bot->slug), ['message' => []])
            ->assertForbidden();
    }

    private function createBot(): TelegramBot
    {
        return TelegramBot::create([
            'name' => 'HC SMK Telkom Lampung',
            'slug' => 'hc-kepegawaian',
            'purpose' => 'employment',
            'bot_username' => 'hc_smktel_bot',
            'bot_token' => '123456789:AA_secure_test_token',
            'webhook_secret' => 'secret_token_telegram',
            'status' => 'connected',
            'is_active' => true,
        ]);
    }

    private function sendBotMessage(TelegramBot $bot, string $chatId, string $text): void
    {
        $this->withHeader('X-Telegram-Bot-Api-Secret-Token', $bot->webhook_secret)
            ->postJson(route('telegram.webhook', $bot->slug), [
                'message' => [
                    'chat' => ['id' => $chatId, 'type' => 'private'],
                    'from' => ['id' => $chatId, 'first_name' => 'Guru'],
                    'text' => $text,
                ],
            ])->assertOk()->assertJson(['ok' => true]);
    }

    private function createLinkedEmployee(TelegramBot $bot, string $name, string $chatId): User
    {
        $user = User::factory()->create(['name' => $name, 'phone_number' => '08'.$chatId]);
        $teacher = MasterGuru::create(['nama_lengkap' => $name, 'jenis_kelamin' => 'L', 'user_id' => $user->id]);
        $teacher->dapodikGuru()->create(['nama' => $name, 'status_kepegawaian' => 'Pegawai Full Time', 'hp' => '08'.$chatId]);
        TelegramUserLink::create([
            'telegram_bot_id' => $bot->id,
            'user_id' => $user->id,
            'chat_id' => $chatId,
            'telegram_user_id' => $chatId,
            'linked_at' => now(),
        ]);

        return $user;
    }

    private function loginSuperadmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('Super Admin', 'web'));

        return $user;
    }
}
