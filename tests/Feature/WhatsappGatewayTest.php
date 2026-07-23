<?php

namespace Tests\Feature;

use App\Models\DapodikSiswa;
use App\Models\MasterSiswa;
use App\Models\Perizinan;
use App\Models\Rombel;
use App\Models\User;
use App\Models\WhatsappDevice;
use App\Models\WhatsappLog;
use App\Models\WhatsappTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WhatsappGatewayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake([
            '127.0.0.1:3001/api/connect' => Http::response([
                'success' => true,
                'status' => 'connecting',
            ]),
            '127.0.0.1:3001/api/status*' => Http::response([
                'success' => true,
                'status' => 'connected',
            ]),
            '127.0.0.1:3001/*' => Http::response([
                'success' => true,
                'message' => 'Faked Response',
            ]),
            'api.fonnte.com/*' => Http::response(['status' => true, 'reason' => 'Faked Response'], 200),
            'api.wablas.com/*' => Http::response(['status' => true, 'message' => 'Faked Response'], 200),
        ]);
    }

    private function userWithRole(string $roleName): User
    {
        $role = Role::findOrCreate($roleName, 'web');
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole($role);

        return $user;
    }

    public function test_super_admin_can_access_whatsapp_gateway_index()
    {
        $user = $this->userWithRole('Super Admin');

        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'Super Admin'])
            ->get(route('super-admin.whatsapp-gateway.index'));

        $response->assertStatus(200);
        $response->assertSee('WhatsApp Gateway');
        $response->assertSee('Gateway Utama SMK Telkom');
    }

    public function test_non_super_admin_cannot_access_whatsapp_gateway_index()
    {
        $user = $this->userWithRole('Guru Kelas');

        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->get(route('super-admin.whatsapp-gateway.index'));

        $response->assertStatus(403);
    }

    public function test_can_get_devices_data()
    {
        $user = $this->userWithRole('Super Admin');

        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'Super Admin'])
            ->get(route('super-admin.whatsapp-gateway.devices-data'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'devices',
            'stats' => [
                'total_devices',
                'connected_devices',
                'total_sent_today',
                'success_rate',
            ],
        ]);
    }

    public function test_can_store_device()
    {
        $user = $this->userWithRole('Super Admin');

        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'Super Admin'])
            ->post(route('super-admin.whatsapp-gateway.device.store'), [
                'name' => 'Device Baru',
                'phone_number' => '+628123456789',
                'provider' => 'fonnte',
                'server_url' => 'https://api.fonnte.com/send',
                'api_key' => 'secret_api_key',
                'is_default' => true,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('whatsapp_devices', [
            'name' => 'Device Baru',
            'phone_number' => '+628123456789',
            'is_default' => 1,
        ]);
    }

    public function test_can_update_device()
    {
        $user = $this->userWithRole('Super Admin');
        $device = WhatsappDevice::create([
            'name' => 'Device Lama',
            'phone_number' => '123456',
            'session_id' => 'wa_sess_old',
            'provider' => 'fonnte',
            'server_url' => 'https://api.fonnte.com/send',
            'api_key' => 'old_key',
            'is_default' => true,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'Super Admin'])
            ->put(route('super-admin.whatsapp-gateway.device.update', $device), [
                'name' => 'Device Terupdate',
                'phone_number' => '654321',
                'provider' => 'fonnte',
                'server_url' => 'https://api.fonnte.com/send',
                'api_key' => 'new_key',
                'is_default' => true,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('whatsapp_devices', [
            'id' => $device->id,
            'name' => 'Device Terupdate',
            'phone_number' => '654321',
        ]);
    }

    public function test_can_generate_qr_code()
    {
        $user = $this->userWithRole('Super Admin');
        $device = WhatsappDevice::create([
            'name' => 'Device QR',
            'session_id' => 'wa_sess_qr',
            'provider' => 'node_baileys',
            'server_url' => 'http://127.0.0.1:3001',
        ]);

        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'Super Admin'])
            ->post(route('super-admin.whatsapp-gateway.device.qr', $device));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
    }

    public function test_can_connect_device()
    {
        $user = $this->userWithRole('Super Admin');
        $device = WhatsappDevice::create([
            'name' => 'Device Conn',
            'session_id' => 'wa_sess_conn',
            'provider' => 'node_baileys',
            'server_url' => 'http://127.0.0.1:3001',
            'status' => 'disconnected',
        ]);

        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'Super Admin'])
            ->post(route('super-admin.whatsapp-gateway.device.connect', $device));

        $response->assertStatus(200);
        $this->assertEquals('connected', WhatsappDevice::find($device->id)->status);
    }

    public function test_can_disconnect_device()
    {
        $user = $this->userWithRole('Super Admin');
        $device = WhatsappDevice::create([
            'name' => 'Device Disc',
            'session_id' => 'wa_sess_disc',
            'provider' => 'fonnte',
            'status' => 'connected',
        ]);

        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'Super Admin'])
            ->post(route('super-admin.whatsapp-gateway.device.disconnect', $device));

        $response->assertStatus(200);
        $this->assertEquals('disconnected', WhatsappDevice::find($device->id)->status);
    }

    public function test_can_send_test_message()
    {
        $user = $this->userWithRole('Super Admin');
        $device = WhatsappDevice::create([
            'name' => 'Device Test',
            'session_id' => 'wa_sess_test',
            'provider' => 'fonnte',
            'status' => 'connected',
            'is_default' => true,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'Super Admin'])
            ->post(route('super-admin.whatsapp-gateway.send-test'), [
                'whatsapp_device_id' => $device->id,
                'recipient' => '081234567890',
                'message' => 'Test message text',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('whatsapp_logs', [
            'whatsapp_device_id' => $device->id,
            'recipient' => '6281234567890',
            'message' => 'Test message text',
        ]);
    }

    public function test_can_get_logs()
    {
        $user = $this->userWithRole('Super Admin');

        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'Super Admin'])
            ->get(route('super-admin.whatsapp-gateway.logs'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'logs' => [
                'data',
            ],
        ]);
    }

    public function test_can_clear_logs()
    {
        $user = $this->userWithRole('Super Admin');
        $device = WhatsappDevice::create([
            'name' => 'Device Logs',
            'session_id' => 'wa_sess_logs',
            'provider' => 'fonnte',
        ]);
        WhatsappLog::create([
            'whatsapp_device_id' => $device->id,
            'recipient' => '628123',
            'message' => 'Log to clear',
            'status' => 'sent',
        ]);

        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'Super Admin'])
            ->delete(route('super-admin.whatsapp-gateway.logs.clear'));

        $response->assertStatus(200);
        $this->assertEquals(0, WhatsappLog::count());
    }

    public function test_can_save_templates()
    {
        $user = $this->userWithRole('Super Admin');

        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'Super Admin'])
            ->post(route('super-admin.whatsapp-gateway.templates.save'), [
                'templates' => [
                    'absensi_alpha' => [
                        'title' => 'Title Alpha',
                        'is_enabled' => true,
                        'template_text' => 'New alpha text',
                    ],
                ],
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('whatsapp_templates', [
            'event_key' => 'absensi_alpha',
            'title' => 'Title Alpha',
            'template_text' => 'New alpha text',
        ]);
    }

    public function test_webhook_can_update_device_status()
    {
        $device = WhatsappDevice::create([
            'name' => 'Device Webhook',
            'session_id' => 'wa_sess_webhook',
            'provider' => 'node_baileys',
            'status' => 'disconnected',
        ]);

        $response = $this->postJson(route('whatsapp.webhook', 'wa_sess_webhook'), [
            'status' => 'connected',
            'phone_number' => '+628999999',
            'qr_code_data' => null,
        ]);

        $response->assertStatus(200);
        $this->assertEquals('connected', WhatsappDevice::find($device->id)->status);
        $this->assertEquals('+628999999', WhatsappDevice::find($device->id)->phone_number);
    }

    public function test_perizinan_approval_sends_whatsapp_notification()
    {
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $role = Role::findOrCreate('Wali Kelas', 'web');
        $permission = Permission::findOrCreate('manage perizinan wali kelas', 'web');
        $permissionDashboard = Permission::findOrCreate('view wali kelas dashboard', 'web');

        $role->givePermissionTo($permission);
        $role->givePermissionTo($permissionDashboard);

        $wali = User::factory()->create(['email_verified_at' => now()]);
        $wali->assignRole($role);
        $wali->givePermissionTo($permission);
        $wali->givePermissionTo($permissionDashboard);

        $siswaUser = User::factory()->create();

        $siswa = MasterSiswa::create([
            'nis' => '12345',
            'nama_lengkap' => 'Ahmad Siswa',
            'jenis_kelamin' => 'L',
            'user_id' => $siswaUser->id,
        ]);

        $tahun = \App\Models\TahunPelajaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        $kelas = \App\Models\Kelas::create([
            'nama_kelas' => 'XI RPL 1',
            'jurusan' => 'Rekayasa Perangkat Lunak',
        ]);

        $rombel = Rombel::create([
            'tahun_ajaran' => '2026/2027',
            'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $tahun->id,
            'wali_kelas_id' => $wali->id,
        ]);

        $siswa->rombels()->attach($rombel);

        DapodikSiswa::create([
            'master_siswa_id' => $siswa->id,
            'nama' => 'Ahmad Siswa',
            'hp' => '081234567890',
        ]);

        $device = WhatsappDevice::create([
            'name' => 'Gateway Utama',
            'session_id' => 'wa_sess_test_perizinan',
            'provider' => 'fonnte',
            'status' => 'connected',
            'is_active' => true,
            'is_default' => true,
        ]);

        WhatsappTemplate::create([
            'event_key' => 'perizinan_disetujui',
            'title' => 'Izin Disetujui',
            'category' => 'perizinan',
            'is_enabled' => true,
            'template_text' => 'Halo {nama_siswa} ({kelas}), izin Anda pada {tanggal} alasan {alasan} disetujui.',
        ]);

        $perizinan = Perizinan::create([
            'user_id' => $siswaUser->id,
            'tanggal_izin' => '2026-07-23',
            'jenis_izin' => 'sakit',
            'keterangan' => 'Sakit Demam',
            'status' => 'diajukan',
        ]);

        $response = $this->actingAs($wali)
            ->withSession(['active_role' => 'Wali Kelas'])
            ->patch(route('wali-kelas.perizinan.approve', $perizinan));

        $response->assertRedirect();

        $this->assertDatabaseHas('whatsapp_logs', [
            'whatsapp_device_id' => $device->id,
            'recipient' => '6281234567890',
            'status' => 'sent',
            'type' => 'perizinan',
        ]);
    }
}
