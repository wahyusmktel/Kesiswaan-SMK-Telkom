<?php

namespace Tests\Feature;

use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MasterGuru;
use App\Models\MataPelajaran;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActiveAcademicScheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_academic_period_scope_excludes_schedules_from_other_periods(): void
    {
        $user = User::factory()->create();
        $teacher = MasterGuru::create([
            'nama_lengkap' => 'Guru Pengajar',
            'jenis_kelamin' => 'L',
            'user_id' => $user->id,
        ]);
        $classroom = Kelas::create([
            'nama_kelas' => 'XI TJKT 1',
            'jurusan' => 'Teknik Jaringan Komputer dan Telekomunikasi',
        ]);
        $subject = MataPelajaran::create([
            'kode_mapel' => 'TEST-ACTIVE-PERIOD',
            'nama_mapel' => 'Pengujian Tahun Pelajaran',
        ]);
        $activePeriod = TahunPelajaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);
        $inactivePeriod = TahunPelajaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Genap',
            'is_active' => false,
        ]);

        $activeRombel = Rombel::create([
            'tahun_ajaran' => $activePeriod->tahun,
            'tahun_pelajaran_id' => $activePeriod->id,
            'kelas_id' => $classroom->id,
            'wali_kelas_id' => $user->id,
        ]);
        $inactiveRombel = Rombel::create([
            'tahun_ajaran' => $inactivePeriod->tahun,
            'tahun_pelajaran_id' => $inactivePeriod->id,
            'kelas_id' => $classroom->id,
            'wali_kelas_id' => $user->id,
        ]);

        $activeSchedule = $this->createSchedule($activeRombel, $subject, $teacher, 1);
        $this->createSchedule($inactiveRombel, $subject, $teacher, 2);

        $scheduleIds = JadwalPelajaran::query()
            ->inActiveAcademicPeriod()
            ->pluck('id');

        $this->assertSame([$activeSchedule->id], $scheduleIds->all());
    }

    private function createSchedule(Rombel $rombel, MataPelajaran $subject, MasterGuru $teacher, int $lessonNumber): JadwalPelajaran
    {
        return JadwalPelajaran::create([
            'rombel_id' => $rombel->id,
            'mata_pelajaran_id' => $subject->id,
            'master_guru_id' => $teacher->id,
            'hari' => 'Senin',
            'jam_ke' => $lessonNumber,
            'jam_mulai' => '07:30:00',
            'jam_selesai' => '08:15:00',
        ]);
    }
}
