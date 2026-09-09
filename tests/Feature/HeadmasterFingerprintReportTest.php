<?php

namespace Tests\Feature;

use App\Models\FingerprintAttendance;
use App\Models\FingerprintDevice;
use App\Models\MasterGuru;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HeadmasterFingerprintReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_counts_days_and_averages_without_counting_future_days_or_single_scan_checkout(): void
    {
        config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        $this->withoutVite();
        $this->travelTo(\Carbon\Carbon::parse('2026-09-09 10:00:00'));
        $principal = User::factory()->create();
        $role = Role::findOrCreate('Kepala Sekolah', 'web');
        $role->givePermissionTo(Permission::findOrCreate('view executive dashboard', 'web'));
        $principal->assignRole($role);
        $teacherUser = User::factory()->create();
        $teacher = MasterGuru::create(['nama_lengkap' => 'Guru Rekap', 'jenis_kelamin' => 'L', 'user_id' => $teacherUser->id]);
        $teacher->dapodikGuru()->create(['nama' => 'Guru Rekap', 'status_kepegawaian' => 'Pegawai Tetap']);
        $tpaUser = User::factory()->create();
        $tpa = MasterGuru::create(['nama_lengkap' => 'TPA Rekap', 'jenis_kelamin' => 'P', 'user_id' => $tpaUser->id]);
        $tpa->dapodikGuru()->create(['employee_category' => 'tpa', 'nama' => 'TPA Rekap', 'status_kepegawaian' => 'Pegawai Part Time']);
        $device = FingerprintDevice::create(['name' => 'Mesin', 'ip_address' => '127.0.0.1']);
        foreach (['2026-09-07 07:00', '2026-09-07 16:00', '2026-09-08 08:00'] as $time) {
            FingerprintAttendance::create(['fingerprint_device_id' => $device->id, 'user_id' => '1', 'app_user_id' => $teacherUser->id, 'timestamp' => $time]);
        }
        $this->actingAs($principal)->withSession(['active_role' => 'Kepala Sekolah']);
        $this->get(route('kepala-sekolah.fingerprint-report.index', ['date' => '2026-09-09', 'period' => 'week']))
            ->assertOk()->assertSee('Rata-rata Pulang')->assertViewHas('rows', fn ($rows) => $rows->first()['present'] === 2 && $rows->first()['late'] === 1 && $rows->first()['absent'] === 1 && $rows->first()['average_in'] === '07:30' && $rows->first()['average_out'] === '16:00')
            ->assertViewHas('rows', fn ($rows) => $rows->firstWhere('name', 'TPA Rekap')['absent'] === 3)
            ->assertViewHas('trend', fn ($trend) => count($trend) === 3);
        $this->get(route('kepala-sekolah.fingerprint-report.index', ['date' => '2026-10-01', 'period' => 'month']))->assertOk()->assertViewHas('trend', []);
        $this->actingAs($teacherUser)->get(route('kepala-sekolah.fingerprint-report.index'))->assertForbidden();
        $this->travelBack();
    }
}
