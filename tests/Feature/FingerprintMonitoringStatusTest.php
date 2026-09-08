<?php

namespace Tests\Feature;

use App\Models\FingerprintAttendance;
use App\Models\FingerprintDevice;
use App\Models\FingerprintUser;
use App\Models\MasterGuru;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FingerprintMonitoringStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_follows_configured_deadline_and_late_supplementary_scan(): void
    {
        config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        $this->withoutVite();
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('Super Admin', 'web'));
        $this->actingAs($admin)->withSession(['active_role' => 'Super Admin']);
        $user = User::factory()->create();
        $teacher = MasterGuru::create(['nama_lengkap' => 'Guru Batas Waktu', 'jenis_kelamin' => 'L', 'user_id' => $user->id]);
        $teacher->dapodikGuru()->create(['nama' => $teacher->nama_lengkap, 'status_kepegawaian' => 'Pegawai Full Time']);
        $device = FingerprintDevice::create(['name' => 'Mesin Test', 'ip_address' => '127.0.0.10']);
        FingerprintUser::create(['fingerprint_device_id' => $device->id, 'user_id' => '12', 'app_user_id' => $user->id, 'name' => $teacher->nama_lengkap]);
        $url = route('fingerprint.monitoring', ['date' => '2026-09-07']);

        Carbon::setTestNow('2026-09-07 07:29:00');
        $this->get($url)->assertOk()->assertSee('Menunggu Absensi')->assertViewHas('stats', fn ($stats) => $stats['pending'] === 1 && $stats['absent'] === 0);
        Carbon::setTestNow('2026-09-07 07:31:00');
        $this->get($url)->assertOk()->assertSee('Tidak Hadir')->assertViewHas('stats', fn ($stats) => $stats['pending'] === 0 && $stats['absent'] === 1);
        FingerprintAttendance::create(['fingerprint_device_id' => $device->id, 'user_id' => '12', 'app_user_id' => $user->id, 'timestamp' => '2026-09-07 07:35:00']);
        $this->get($url)->assertOk()->assertSee('Terlambat')->assertViewHas('stats', fn ($stats) => $stats['late'] === 1 && $stats['absent'] === 0);

        $leave = \App\Models\GuruIzin::create([
            'master_guru_id' => $teacher->id,
            'tanggal_mulai' => '2026-09-07 06:00:00',
            'tanggal_selesai' => '2026-09-07 16:00:00',
            'jenis_izin' => 'Dinas',
            'kategori_penyetujuan' => 'sekolah',
            'deskripsi' => 'Tugas sekolah',
            'status_piket' => 'disetujui',
            'status_kurikulum' => 'disetujui',
            'status_sdm' => 'disetujui',
        ]);
        $this->get($url)->assertOk()
            ->assertViewHas('rows', fn ($rows) => $rows->first()->monitoring_status_text === 'Izin'
                && $rows->first()->monitoring_rule_label === 'Izin disetujui SDM'
                && in_array('Dinas', $rows->first()->monitoring_notes, true))
            ->assertViewHas('stats', fn ($stats) => $stats['leave'] === 1 && $stats['late'] === 0 && $stats['absent'] === 0);
        $leave->update(['status_sdm' => 'ditolak']);
        $this->get($url)->assertOk()->assertSee('Terlambat')
            ->assertViewHas('stats', fn ($stats) => $stats['leave'] === 0 && $stats['late'] === 1);
        Carbon::setTestNow();
    }
}
