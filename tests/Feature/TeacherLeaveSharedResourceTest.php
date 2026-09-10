<?php

namespace Tests\Feature;

use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\LmsAssignment;
use App\Models\MasterGuru;
use App\Models\MataPelajaran;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TeacherLeaveSharedResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.key' => 'base64:'.base64_encode(str_repeat('i', 32))]);
        $this->withoutVite();
    }

    public function test_teacher_selects_one_assignment_for_all_affected_lesson_periods(): void
    {
        [$user, $teacher, $schedules, $assignment] = $this->teacherSchedule();

        $this->actingAs($user)->withSession(['active_role' => 'Guru Kelas'])
            ->get(route('guru.izin.create'))
            ->assertOk()
            ->assertSee('Materi atau Tugas Bersama')
            ->assertSee('name="lms_assignment_id"', false)
            ->assertDontSee('lms_assignment_ids[');

        $this->actingAs($user)->withSession(['active_role' => 'Guru Kelas'])
            ->post(route('guru.izin.store'), [
                'tanggal_mulai' => '2026-09-11 07:00:00',
                'tanggal_selesai' => '2026-09-11 11:00:00',
                'jenis_izin' => 'Sakit',
                'kategori_penyetujuan' => 'luar',
                'deskripsi' => 'Perlu beristirahat sesuai arahan dokter.',
                'jadwal_ids' => $schedules->pluck('id')->all(),
                'lms_assignment_id' => $assignment->id,
            ])
            ->assertRedirect(route('guru.izin.index'))
            ->assertSessionHas('success');

        $izinId = DB::table('guru_izins')->where('master_guru_id', $teacher->id)->value('id');
        $pivots = DB::table('guru_izin_jadwal')->where('guru_izin_id', $izinId)->get();

        $this->assertCount(4, $pivots);
        $this->assertSame(
            $schedules->pluck('id')->sort()->values()->all(),
            $pivots->pluck('jadwal_pelajaran_id')->sort()->values()->all(),
        );
        $this->assertSame([$assignment->id], $pivots->pluck('lms_assignment_id')->unique()->values()->all());
        $this->assertSame([null], $pivots->pluck('lms_material_id')->unique()->values()->all());
    }

    private function teacherSchedule(): array
    {
        $role = Role::findOrCreate('Guru Kelas', 'web');
        Role::findOrCreate('Guru Piket', 'web');
        $user = User::factory()->create();
        $user->assignRole($role);
        $teacher = MasterGuru::create([
            'nama_lengkap' => $user->name,
            'jenis_kelamin' => 'L',
            'user_id' => $user->id,
        ]);
        $period = TahunPelajaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);
        $classroom = Kelas::create([
            'nama_kelas' => 'XI TKJ 1',
            'jurusan' => 'Teknik Komputer dan Jaringan',
        ]);
        $rombel = Rombel::create([
            'tahun_ajaran' => $period->tahun,
            'tahun_pelajaran_id' => $period->id,
            'kelas_id' => $classroom->id,
            'wali_kelas_id' => $user->id,
        ]);
        $subject = MataPelajaran::create([
            'kode_mapel' => 'ADMINJAR',
            'nama_mapel' => 'Administrasi Infrastruktur Jaringan',
        ]);

        $times = [
            [1, '07:00:00', '07:45:00'],
            [2, '07:45:00', '08:30:00'],
            [3, '08:30:00', '09:15:00'],
            [4, '09:15:00', '10:00:00'],
        ];
        $schedules = collect($times)->map(fn (array $time) => JadwalPelajaran::create([
            'rombel_id' => $rombel->id,
            'mata_pelajaran_id' => $subject->id,
            'master_guru_id' => $teacher->id,
            'hari' => 'Jumat',
            'jam_ke' => $time[0],
            'jam_mulai' => $time[1],
            'jam_selesai' => $time[2],
        ]));
        $assignment = LmsAssignment::create([
            'mata_pelajaran_id' => $subject->id,
            'master_guru_id' => $teacher->id,
            'rombel_id' => $rombel->id,
            'title' => 'Praktik konfigurasi routing',
            'description' => 'Selesaikan praktik dan unggah hasilnya.',
            'points' => 100,
        ]);

        return [$user, $teacher, $schedules, $assignment];
    }
}
