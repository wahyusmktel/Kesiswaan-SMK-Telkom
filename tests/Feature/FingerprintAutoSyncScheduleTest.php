<?php

namespace Tests\Feature;

use App\Jobs\SendFingerprintDailyRecapsJob;
use App\Jobs\SyncFingerprintAttendancesJob;
use App\Models\FingerprintAutoSyncSetting;
use App\Models\FingerprintDevice;
use App\Models\FingerprintUser;
use App\Models\User;
use App\Services\FingerprintWhatsappNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FingerprintAutoSyncScheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_two_daily_slots_do_not_repeat_and_catch_up_after_downtime(): void
    {
        Queue::fake();
        $setting = FingerprintAutoSyncSetting::getSetting();
        $setting->update(['is_enabled' => true, 'run_time' => '08:00:00', 'second_run_time' => '16:30:00']);
        $device = FingerprintDevice::create(['name' => 'Mesin', 'ip_address' => '127.0.0.1', 'is_active' => true]);
        FingerprintUser::create(['fingerprint_device_id' => $device->id, 'user_id' => '1', 'app_user_id' => User::factory()->create()->id]);
        foreach (['07:59:00' => 0, '08:00:00' => 1, '08:01:00' => 1, '16:29:00' => 1, '16:30:00' => 2, '16:31:00' => 2] as $time => $count) {
            $this->travelTo(\Carbon\Carbon::parse('2026-09-08 '.$time));
            $this->artisan('fingerprint:auto-sync')->assertExitCode(0);
            Queue::assertPushed(SyncFingerprintAttendancesJob::class, $count);
        }
        $this->travelTo(\Carbon\Carbon::parse('2026-09-09 18:00:00'));
        $this->artisan('fingerprint:auto-sync')->assertExitCode(0);
        $this->artisan('fingerprint:auto-sync')->assertExitCode(0);
        Queue::assertPushed(SyncFingerprintAttendancesJob::class, 3);
        $setting->update(['second_run_time' => null]);
        $this->travelTo(\Carbon\Carbon::parse('2026-09-10 08:00:00'));
        $this->artisan('fingerprint:auto-sync')->assertExitCode(0);
        $this->travelTo(\Carbon\Carbon::parse('2026-09-10 17:00:00'));
        $this->artisan('fingerprint:auto-sync')->assertExitCode(0);
        Queue::assertPushed(SyncFingerprintAttendancesJob::class, 4);
        $setting->update(['is_enabled' => false]);
        $this->travelTo(\Carbon\Carbon::parse('2026-09-11 17:00:00'));
        $this->artisan('fingerprint:auto-sync')->assertExitCode(0);
        Queue::assertPushed(SyncFingerprintAttendancesJob::class, 4);
        $this->travelBack();
    }

    public function test_admin_can_save_and_clear_second_time_but_cannot_set_it_before_first(): void
    {
        config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('Super Admin', 'web'));
        $this->actingAs($admin);
        $url = route('fingerprint.auto-sync-settings.update');
        $data = ['is_enabled' => 1, 'run_time' => '08:00', 'range_type' => '1_day'];
        $this->put($url, $data + ['second_run_time' => '16:30'])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('16:30:00', FingerprintAutoSyncSetting::getSetting()->second_run_time);
        foreach (['08:00', '07:00', 'bad'] as $invalid) {
            $this->put($url, $data + ['second_run_time' => $invalid])->assertSessionHasErrors('second_run_time');
        }
        $this->put($url, $data + ['second_run_time' => ''])->assertRedirect();
        $this->assertNull(FingerprintAutoSyncSetting::getSetting()->second_run_time);
    }

    public function test_daily_notifications_are_dispatched_once_at_the_configured_time(): void
    {
        Queue::fake();
        $setting = FingerprintAutoSyncSetting::getSetting();
        $setting->update([
            'notifications_enabled' => true,
            'notification_time' => '18:00:00',
            'last_notification_dispatched_at' => null,
        ]);

        foreach (['17:59:00' => 0, '18:00:00' => 1, '18:01:00' => 1] as $time => $count) {
            $this->travelTo(\Carbon\Carbon::parse('2026-09-08 '.$time));
            $this->artisan('fingerprint:send-daily-notifications')->assertExitCode(0);
            Queue::assertPushed(SendFingerprintDailyRecapsJob::class, $count);
        }

        $this->travelTo(\Carbon\Carbon::parse('2026-09-09 19:00:00'));
        $this->artisan('fingerprint:send-daily-notifications')->assertExitCode(0);
        Queue::assertPushed(SendFingerprintDailyRecapsJob::class, 2);
        $setting->update(['notifications_enabled' => false]);
        $this->travelTo(\Carbon\Carbon::parse('2026-09-10 19:00:00'));
        $this->artisan('fingerprint:send-daily-notifications')->assertExitCode(0);
        Queue::assertPushed(SendFingerprintDailyRecapsJob::class, 2);
        $this->travelBack();
    }

    public function test_superadmin_can_update_the_daily_notification_time(): void
    {
        config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('Super Admin', 'web'));

        $this->actingAs($admin)->withSession(['active_role' => 'Super Admin'])
            ->get(route('super-admin.whatsapp-gateway.index'))
            ->assertOk()
            ->assertSee('Jadwal Notifikasi Fingerprint Pegawai')
            ->assertSee('fingerprint_peringatan_harian');

        $response = $this
            ->putJson(route('super-admin.whatsapp-gateway.fingerprint-notifications.update'), [
                'notifications_enabled' => true,
                'notification_time' => '18:00',
                'notification_channel' => 'whatsapp',
            ]);

        $response->assertOk()->assertJson(['success' => true]);
        $setting = FingerprintAutoSyncSetting::getSetting();
        $this->assertTrue($setting->notifications_enabled);
        $this->assertSame('18:00:00', $setting->notification_time);
        $this->putJson(route('super-admin.whatsapp-gateway.fingerprint-notifications.update'), [
            'notifications_enabled' => true,
            'notification_time' => 'invalid',
            'notification_channel' => 'whatsapp',
        ])->assertUnprocessable()->assertJsonValidationErrors('notification_time');
    }

    public function test_superadmin_can_send_a_manual_notification_immediately_without_replacing_the_schedule(): void
    {
        Queue::fake();
        config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        $this->travelTo(\Carbon\Carbon::parse('2026-09-08 10:15:00'));
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('Super Admin', 'web'));
        $notifications = $this->mock(FingerprintWhatsappNotificationService::class);
        $notifications->shouldReceive('sendToday')->once()->andReturn(['sent' => 1, 'failed' => 0, 'skipped' => 0]);
        $notifications->shouldReceive('sendRemindersToday')->once()->andReturn(['sent' => 1, 'failed' => 0, 'skipped' => 2]);

        $this->actingAs($admin)->withSession(['active_role' => 'Super Admin'])
            ->postJson(route('super-admin.whatsapp-gateway.fingerprint-notifications.send-now'), [
                'notification_channel' => 'whatsapp',
            ])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'summary' => ['sent' => 2, 'failed' => 0, 'skipped' => 2],
            ])
            ->assertJsonPath('message', 'Pengiriman WhatsApp selesai: 2 berhasil, 0 gagal, dan 2 dilewati.');

        Queue::assertNothingPushed();
        $this->assertNull(FingerprintAutoSyncSetting::getSetting()->last_notification_dispatched_at);
        $this->travelBack();
    }
}
