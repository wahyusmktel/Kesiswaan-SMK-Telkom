<?php

namespace Tests\Feature;

use App\Models\MasterGuru;
use App\Models\TelegramBot;
use App\Models\TelegramUserLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TelegramExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.key' => 'base64:'.base64_encode(str_repeat('t', 32))]);
    }

    public function test_superadmin_can_export_telegram_pegawai_status_excel(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('Super Admin', 'web'));

        $bot = TelegramBot::create([
            'name' => 'HC SMK Telkom Lampung',
            'slug' => 'hc-kepegawaian',
            'purpose' => 'employment',
            'bot_token' => '123456789:ABCdefGHIjklMNOpqrsTUVwxyz',
            'webhook_secret' => 'secret123',
            'is_active' => true,
            'status' => 'connected',
        ]);

        // Pegawai 1: Terhubung
        $user1 = User::factory()->create(['name' => 'Budi Santoso', 'email' => 'budi@smktelkom-lpg.sch.id']);
        $guru1 = MasterGuru::create([
            'nama_lengkap' => 'Budi Santoso, S.Kom',
            'jenis_kelamin' => 'L',
            'kode_guru' => 'G01',
            'user_id' => $user1->id,
            'employee_category' => 'guru',
        ]);
        $guru1->dapodikGuru()->create([
            'nama' => 'Budi Santoso',
            'hp' => '081234567890',
            'jenis_ptk' => 'Guru Mapel',
        ]);
        TelegramUserLink::create([
            'telegram_bot_id' => $bot->id,
            'user_id' => $user1->id,
            'chat_id' => '99887766',
            'telegram_user_id' => '99887766',
            'telegram_username' => 'budisantoso',
            'telegram_name' => 'Budi S',
            'linked_at' => now(),
            'last_interaction_at' => now(),
        ]);

        // Pegawai 2: Belum Terhubung (Punya HP dan Akun)
        $user2 = User::factory()->create(['name' => 'Siti Aminah', 'email' => 'siti@smktelkom-lpg.sch.id']);
        $guru2 = MasterGuru::create([
            'nama_lengkap' => 'Siti Aminah, M.Pd',
            'jenis_kelamin' => 'P',
            'kode_guru' => 'G02',
            'user_id' => $user2->id,
            'employee_category' => 'guru',
        ]);
        $guru2->dapodikGuru()->create([
            'nama' => 'Siti Aminah',
            'hp' => '089876543210',
            'jenis_ptk' => 'Guru BK',
        ]);

        // Pegawai 3: Belum Terhubung (TPA, belum punya akun SISFO)
        $guru3 = MasterGuru::create([
            'nama_lengkap' => 'Ahmad Fauzi',
            'jenis_kelamin' => 'L',
            'kode_guru' => 'T01',
            'user_id' => null,
            'employee_category' => 'tpa',
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['active_role' => 'Super Admin'])
            ->get(route('super-admin.telegram-links.export'));

        $response->assertOk();
        $this->assertTrue(
            str_contains((string) $response->headers->get('content-disposition'), 'Daftar-Pegawai-Telegram-SISFO')
        );
    }

    public function test_non_admin_cannot_export_telegram_pegawai_status_excel(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole(Role::findOrCreate('Siswa', 'web'));

        $response = $this->actingAs($siswa)
            ->withSession(['active_role' => 'Siswa'])
            ->get(route('super-admin.telegram-links.export'));

        $response->assertForbidden();
    }
}
