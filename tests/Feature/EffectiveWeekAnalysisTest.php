<?php

namespace Tests\Feature;

use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MasterGuru;
use App\Models\MataPelajaran;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Models\User;
use App\Models\WorkCalendarEvent;
use App\Services\EffectiveWeekAnalysisService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EffectiveWeekAnalysisTest extends TestCase
{
    use RefreshDatabase;

    public function test_analysis_matches_reference_calendar_for_tuesday_schedule(): void
    {
        [$user, $academicYear, $rombel, $subject] = $this->prepareSchedule();
        $this->prepareReferenceEvents();

        $analysis = app(EffectiveWeekAnalysisService::class)->analyze(
            $user,
            $academicYear,
            $rombel->id,
            $subject->id,
        );

        $odd = $analysis['semesters'][0];
        $even = $analysis['semesters'][1];

        $this->assertSame(25, $odd['total_weeks']);
        $this->assertSame(10, $odd['ineffective_weeks']);
        $this->assertSame(15, $odd['effective_weeks']);
        $this->assertSame(60, $odd['total_jp']);

        $this->assertSame(22, $even['total_weeks']);
        $this->assertSame(11, $even['ineffective_weeks']);
        $this->assertSame(11, $even['effective_weeks']);
        $this->assertSame(44, $even['total_jp']);
    }

    public function test_teacher_can_open_analysis_page_and_generate_pdf(): void
    {
        [$user, , $rombel, $subject] = $this->prepareSchedule();
        $this->prepareReferenceEvents();

        $parameters = [
            'rombel_id' => $rombel->id,
            'mata_pelajaran_id' => $subject->id,
        ];

        $this->actingAs($user)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->get(route('guru-kelas.teaching-module.effective-week.index', $parameters))
            ->assertOk()
            ->assertSee('Analisis Pekan Efektif')
            ->assertSee('Mata Pelajaran Pilihan Cloud Computing')
            ->assertSee('Semester Ganjil')
            ->assertSee('Semester Genap');

        $pdf = $this->actingAs($user)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->get(route('guru-kelas.teaching-module.effective-week.pdf', $parameters));

        $pdf->assertOk();
        $pdf->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $pdf->getContent());
    }

    private function prepareSchedule(): array
    {
        $role = Role::findOrCreate('Guru Kelas', 'web');
        $user = User::factory()->create(['name' => 'Wahyu Rahmat Hidayat, S.Kom., Gr.']);
        $user->assignRole($role);
        $teacher = MasterGuru::create([
            'nama_lengkap' => $user->name,
            'jenis_kelamin' => 'L',
            'user_id' => $user->id,
            'nik' => '25950022',
        ]);
        $academicYear = TahunPelajaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);
        $class = Kelas::create([
            'nama_kelas' => 'XI TJKT 1',
            'jurusan' => 'Teknik Jaringan Komputer dan Telekomunikasi',
        ]);
        $rombel = Rombel::create([
            'tahun_ajaran' => '2026/2027',
            'tahun_pelajaran_id' => $academicYear->id,
            'kelas_id' => $class->id,
            'wali_kelas_id' => $user->id,
        ]);
        $subject = MataPelajaran::create([
            'kode_mapel' => 'CC',
            'nama_mapel' => 'Mata Pelajaran Pilihan Cloud Computing',
        ]);

        foreach (range(1, 4) as $lessonNumber) {
            JadwalPelajaran::create([
                'rombel_id' => $rombel->id,
                'mata_pelajaran_id' => $subject->id,
                'master_guru_id' => $teacher->id,
                'hari' => 'Selasa',
                'jam_ke' => $lessonNumber,
                'jam_mulai' => sprintf('%02d:00:00', 7 + $lessonNumber),
                'jam_selesai' => sprintf('%02d:45:00', 7 + $lessonNumber),
            ]);
        }

        return [$user, $academicYear, $rombel, $subject];
    }

    private function prepareReferenceEvents(): void
    {
        foreach ([
            ['2026-07-13', '2026-07-13', 'Hari Pertama KBM TP. 2026/2027', 'academic_activity'],
            ['2026-07-13', '2026-07-17', 'MPLS', 'academic_activity'],
            ['2026-08-25', '2026-08-25', 'Maulid Nabi Muhammad SAW', 'national_holiday'],
            ['2026-09-14', '2026-09-19', 'STS Ganjil TP. 2026/2027', 'assessment'],
            ['2026-10-19', '2026-10-23', 'Perkiraan Kokurikuler I', 'academic_activity'],
            ['2026-10-26', '2026-11-08', 'Perkiraan TKA', 'assessment'],
            ['2026-11-23', '2026-11-27', 'UK Level 1 & 3 TP. 2026/2027', 'assessment'],
            ['2026-11-30', '2026-12-05', 'SAS Ganjil TP. 2026/2027', 'assessment'],
            ['2026-12-21', '2027-01-04', 'Libur Akhir Semester Ganjil', 'school_break'],
            ['2027-01-06', '2027-01-06', 'Hari Pertama KMB Semester Genap', 'academic_activity'],
            ['2027-02-01', '2027-02-05', 'Ujian Praktik Sekolah', 'assessment'],
            ['2027-03-01', '2027-03-06', 'STS Genap', 'assessment'],
            ['2027-03-09', '2027-03-19', 'Idul Fitri 1448 Hijriyah', 'religious_holiday'],
            ['2027-03-22', '2027-03-30', 'Perkiraan Ujian Sekolah', 'assessment'],
            ['2027-04-05', '2027-04-09', 'Perkiraan UKK Nasional', 'assessment'],
            ['2027-04-26', '2027-04-30', 'Perkiraan Kokurikuler II', 'academic_activity'],
            ['2027-05-24', '2027-05-28', 'UK Level 2 & 4 TP. 2026/2027', 'assessment'],
            ['2027-05-31', '2027-06-05', 'SAS Genap TP. 2026/2027', 'assessment'],
            ['2027-06-11', '2027-06-11', 'Pembagian Rapor Semester Genap', 'report_distribution'],
            ['2027-06-14', '2027-07-10', 'Libur Akhir Tahun Pelajaran 2026/2027', 'school_break'],
        ] as [$from, $to, $title, $type]) {
            WorkCalendarEvent::create([
                'title' => $title,
                'type' => $type,
                'is_non_working' => WorkCalendarEvent::typeIsNonWorking($type),
                'date_from' => $from,
                'date_to' => $to,
            ]);
        }
    }
}
