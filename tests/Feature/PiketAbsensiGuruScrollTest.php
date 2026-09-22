<?php

namespace Tests\Feature;

use App\Models\MasterGuru;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PiketAbsensiGuruScrollTest extends TestCase
{
    use RefreshDatabase;

    protected User $guruPiket;
    protected JadwalPelajaran $jadwal;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup role & permission
        $role = Role::firstOrCreate(['name' => 'Guru Piket']);
        $perm1 = Permission::firstOrCreate(['name' => 'view piket dashboard']);
        $perm2 = Permission::firstOrCreate(['name' => 'manage absensi guru']);
        $role->givePermissionTo([$perm1, $perm2]);

        $this->guruPiket = User::factory()->create();
        $this->guruPiket->assignRole('Guru Piket');

        // Setup master data
        $tahun = TahunPelajaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => 'X PPLG 1',
            'jurusan' => 'PPLG',
        ]);
        $guruUser = User::factory()->create(['name' => 'Guru Pengajar']);
        $guru = MasterGuru::create([
            'user_id' => $guruUser->id,
            'nama_lengkap' => 'Guru Pengajar, S.Kom',
            'kode_guru' => 'GP01',
            'jenis_kelamin' => 'L',
        ]);

        $rombel = Rombel::create([
            'tahun_ajaran' => $tahun->tahun,
            'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $tahun->id,
            'wali_kelas_id' => $guruUser->id,
        ]);

        $mapel = MataPelajaran::create([
            'nama_mapel' => 'Pemrograman Web',
            'kode_mapel' => 'PW01',
        ]);

        $today = Carbon::today();
        $hariList = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];
        $namaHari = $hariList[$today->dayOfWeek];

        $this->jadwal = JadwalPelajaran::create([
            'rombel_id' => $rombel->id,
            'master_guru_id' => $guru->id,
            'mata_pelajaran_id' => $mapel->id,
            'hari' => $namaHari,
            'jam_ke' => 1,
            'jam_mulai' => '07:30:00',
            'jam_selesai' => '08:15:00',
        ]);
    }

    public function test_piket_page_renders_jadwal_card_with_id_and_restore_script(): void
    {
        $response = $this->actingAs($this->guruPiket)
            ->withSession(['active_role' => 'Guru Piket'])
            ->get(route('piket.absensi-guru.index'));

        $response->assertOk();
        $response->assertSee('id="jadwal-card-' . $this->jadwal->id . '"', false);
        $response->assertSee('restoreScrollPosition', false);
        $response->assertSee('absensi_last_jadwal_id', false);
    }

    public function test_submitting_attendance_redirects_with_last_jadwal_id_in_session(): void
    {
        $response = $this->actingAs($this->guruPiket)
            ->withSession(['active_role' => 'Guru Piket'])
            ->post(route('piket.absensi-guru.store'), [
                'jadwal_pelajaran_id' => $this->jadwal->id,
                'status' => 'hadir',
                'tanggal' => Carbon::today()->format('Y-m-d'),
                'keterangan' => null,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('last_jadwal_id', $this->jadwal->id);

        $this->assertDatabaseHas('absensi_guru', [
            'jadwal_pelajaran_id' => $this->jadwal->id,
            'status' => 'hadir',
        ]);
    }
}
