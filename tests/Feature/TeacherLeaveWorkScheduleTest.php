<?php

namespace Tests\Feature;

use App\Models\GuruIzin;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MasterGuru;
use App\Models\MataPelajaran;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Models\User;
use App\Models\WorkCalendarEvent;
use App\Services\TeacherLeaveWorkScheduleService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TeacherLeaveWorkScheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_time_warning_uses_seven_to_sixteen_and_non_working_calendar(): void
    {
        $guru = $this->teacher('Pegawai Full Time');
        WorkCalendarEvent::create([
            'title' => 'Libur Nasional', 'type' => 'national_holiday', 'is_non_working' => true,
            'date_from' => '2026-09-09', 'date_to' => '2026-09-09',
        ]);
        $service = app(TeacherLeaveWorkScheduleService::class);

        $outside = $service->warnings($guru, Carbon::parse('2026-09-08 06:30'), Carbon::parse('2026-09-08 08:00'));
        $holiday = $service->warnings($guru, Carbon::parse('2026-09-09 08:00'), Carbon::parse('2026-09-09 10:00'));

        $this->assertStringContainsString('07:00–16:00', implode(' ', $outside));
        $this->assertStringContainsString('Libur Nasional', implode(' ', $holiday));
    }

    public function test_part_time_warning_follows_active_teaching_schedule(): void
    {
        $guru = $this->teacher('Pegawai Part Time');
        $period = TahunPelajaran::create(['tahun' => '2026/2027', 'semester' => 'Ganjil', 'is_active' => true]);
        $classroom = Kelas::create(['nama_kelas' => 'X TKJ 1', 'jurusan' => 'TKJ']);
        $rombel = Rombel::create(['tahun_ajaran' => $period->tahun, 'tahun_pelajaran_id' => $period->id, 'kelas_id' => $classroom->id, 'wali_kelas_id' => $guru->user_id]);
        $subject = MataPelajaran::create(['kode_mapel' => 'TKJ', 'nama_mapel' => 'Produktif TKJ']);
        JadwalPelajaran::create([
            'master_guru_id' => $guru->id,
            'rombel_id' => $rombel->id,
            'mata_pelajaran_id' => $subject->id,
            'hari' => 'Selasa',
            'jam_ke' => 1,
            'jam_mulai' => '09:00:00',
            'jam_selesai' => '11:00:00',
        ]);

        $warnings = app(TeacherLeaveWorkScheduleService::class)->warnings(
            $guru,
            Carbon::parse('2026-09-08 08:00'),
            Carbon::parse('2026-09-08 10:00'),
        );

        $this->assertStringContainsString('09:00–11:00', implode(' ', $warnings));
    }

    public function test_tpa_always_uses_full_day_hours_and_starts_approval_at_sdm(): void
    {
        $tpa = $this->teacher('Pegawai Part Time', MasterGuru::CATEGORY_TPA);
        $warnings = app(TeacherLeaveWorkScheduleService::class)->warnings(
            $tpa,
            Carbon::parse('2026-09-08 06:30'),
            Carbon::parse('2026-09-08 08:00'),
        );
        $statuses = GuruIzin::initialApprovalStatuses($tpa, GuruIzin::CATEGORY_OUTSIDE);

        $this->assertStringContainsString('07:00–16:00', implode(' ', $warnings));
        $this->assertSame('disetujui', $statuses['status_piket']);
        $this->assertSame('disetujui', $statuses['status_kurikulum']);
        $this->assertSame('menunggu', $statuses['status_sdm']);
    }

    public function test_tpa_and_kaur_sdm_headmaster_rules_follow_the_requested_exceptions(): void
    {
        $permanentTpa = $this->teacher('Pegawai Tetap', MasterGuru::CATEGORY_TPA);
        $outside = GuruIzin::create([
            'master_guru_id' => $permanentTpa->id,
            'tanggal_mulai' => '2026-09-08 08:00', 'tanggal_selesai' => '2026-09-08 10:00',
            'jenis_izin' => 'Sakit', 'kategori_penyetujuan' => 'luar', 'deskripsi' => 'Kontrol kesehatan.',
            ...GuruIzin::initialApprovalStatuses($permanentTpa, 'luar'),
        ]);
        $school = GuruIzin::create([
            'master_guru_id' => $permanentTpa->id,
            'tanggal_mulai' => '2026-09-09 08:00', 'tanggal_selesai' => '2026-09-09 10:00',
            'jenis_izin' => 'Dinas', 'kategori_penyetujuan' => 'sekolah', 'deskripsi' => 'Rapat internal.',
            ...GuruIzin::initialApprovalStatuses($permanentTpa, 'sekolah'),
        ]);
        $this->assertTrue($outside->requiresHeadmasterApproval());
        $this->assertFalse($school->requiresHeadmasterApproval());

        $kaur = $this->teacher('Pegawai Full Time');
        $kaur->user->assignRole(Role::findOrCreate('KAUR SDM', 'web'));
        $statuses = GuruIzin::initialApprovalStatuses($kaur->fresh(), 'sekolah');
        $this->assertSame('disetujui', $statuses['status_sdm']);
        $this->assertSame('menunggu', $statuses['status_kepala_sekolah']);
    }

    private function teacher(string $employment, string $category = MasterGuru::CATEGORY_TEACHER): MasterGuru
    {
        $user = User::factory()->create();
        $guru = MasterGuru::create(['nama_lengkap' => $user->name, 'jenis_kelamin' => 'L', 'user_id' => $user->id, 'employee_category' => $category]);
        $guru->dapodikGuru()->create(['nama' => $user->name, 'status_kepegawaian' => $employment]);

        return $guru;
    }
}
