<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\MasterGuru;
use App\Models\MasterSiswa;
use App\Models\PrakerinIndustri;
use App\Models\PrakerinPembimbing;
use App\Models\PrakerinPenempatan;
use App\Models\PrakerinRombel;
use App\Models\Rombel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HubinRombelPklTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Super Admin']);
        Role::firstOrCreate(['name' => 'Hubin Sinergi UP dan Alumni']);
        Role::firstOrCreate(['name' => 'Siswa']);
    }

    public function test_hubin_role_can_access_rombel_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Hubin Sinergi UP dan Alumni');

        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'Hubin Sinergi UP dan Alumni'])
            ->get(route('hubin.rombel-pkl.index'));

        $response->assertOk();
        $response->assertSee('Rombel PKL');
        $response->assertSee('Kelola Rombel PKL');
    }

    public function test_super_admin_can_access_rombel_index(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');

        $response = $this->actingAs($admin)
            ->withSession(['active_role' => 'Super Admin'])
            ->get(route('hubin.rombel-pkl.index'));

        $response->assertOk();
        $response->assertSee('Rombel PKL');
        $response->assertSee('Kelola Rombel PKL');
    }

    public function test_unauthorized_role_cannot_access_hubin_rombel(): void
    {
        $student = User::factory()->create();
        $student->assignRole('Siswa');

        $response = $this->actingAs($student)
            ->withSession(['active_role' => 'Siswa'])
            ->get(route('hubin.rombel-pkl.index'));

        $response->assertForbidden();
    }

    public function test_can_create_and_update_and_delete_rombel_pkl(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Hubin Sinergi UP dan Alumni');

        $industri = PrakerinIndustri::create([
            'nama_industri' => 'PT Telkom Regional I',
            'alamat' => 'Jl. Pahlawan No. 10',
            'kota' => 'Bandar Lampung',
            'nama_pic' => 'Budi Santoso',
            'is_mou_active' => true,
        ]);

        $guru = MasterGuru::create([
            'nama_lengkap' => 'Drs. Ahmad Fauzi, M.Kom',
            'kode_guru' => 'AF',
            'nik' => '1234567890123456',
            'jenis_kelamin' => 'L',
        ]);

        // Create Rombel
        $storeResponse = $this->actingAs($user)
            ->withSession(['active_role' => 'Hubin Sinergi UP dan Alumni'])
            ->post(route('hubin.rombel-pkl.store'), [
                'nama_rombel' => 'PKL PT Telkom - XII RPL 1',
                'prakerin_industri_id' => $industri->id,
                'master_guru_id' => $guru->id,
                'pembimbing_external_nama' => 'Bpk. Hendra Gunawan',
                'pembimbing_external_jabatan' => 'Head of Engineering',
                'pembimbing_external_telepon' => '081299988877',
                'status' => 'aktif',
            ]);

        $storeResponse->assertRedirect(route('hubin.rombel-pkl.index'));

        $this->assertDatabaseHas('prakerin_rombels', [
            'nama_rombel' => 'PKL PT Telkom - XII RPL 1',
            'prakerin_industri_id' => $industri->id,
            'status' => 'aktif',
        ]);

        $rombel = PrakerinRombel::where('nama_rombel', 'PKL PT Telkom - XII RPL 1')->first();
        $this->assertNotNull($rombel);
        $this->assertEquals($guru->id, $rombel->pembimbingInternal->master_guru_id);
        $this->assertEquals('Bpk. Hendra Gunawan', $rombel->pembimbingExternal->nama);

        // Update Rombel
        $updateResponse = $this->actingAs($user)
            ->withSession(['active_role' => 'Hubin Sinergi UP dan Alumni'])
            ->put(route('hubin.rombel-pkl.update', $rombel->id), [
                'nama_rombel' => 'PKL PT Telkom - XII RPL Update',
                'prakerin_industri_id' => $industri->id,
                'master_guru_id' => $guru->id,
                'pembimbing_external_nama' => 'Bpk. Hendra Gunawan Updated',
                'status' => 'aktif',
            ]);

        $updateResponse->assertRedirect(route('hubin.rombel-pkl.index'));
        $this->assertDatabaseHas('prakerin_rombels', [
            'id' => $rombel->id,
            'nama_rombel' => 'PKL PT Telkom - XII RPL Update',
        ]);

        // Delete Rombel
        $deleteResponse = $this->actingAs($user)
            ->withSession(['active_role' => 'Hubin Sinergi UP dan Alumni'])
            ->delete(route('hubin.rombel-pkl.destroy', $rombel->id));

        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('prakerin_rombels', [
            'id' => $rombel->id,
        ]);
    }

    public function test_mapping_page_only_shows_class_xii_students_and_excludes_mapped(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Hubin Sinergi UP dan Alumni');

        $industri = PrakerinIndustri::create([
            'nama_industri' => 'PT Astra Honda',
            'alamat' => 'Jl. Soekarno Hatta',
            'kota' => 'Bandar Lampung',
            'is_mou_active' => true,
        ]);

        $guru = MasterGuru::create([
            'nama_lengkap' => 'Guru Pembimbing 1',
            'kode_guru' => 'GP1',
            'nik' => '9876543210123456',
            'jenis_kelamin' => 'L',
        ]);

        $internal = PrakerinPembimbing::create([
            'tipe' => 'internal',
            'master_guru_id' => $guru->id,
            'nama' => $guru->nama_lengkap,
            'is_active' => true,
        ]);

        $rombel = PrakerinRombel::create([
            'nama_rombel' => 'PKL Astra XII TKJ',
            'prakerin_industri_id' => $industri->id,
            'pembimbing_internal_id' => $internal->id,
            'status' => 'aktif',
        ]);

        // Kelas X, XI, and XII
        $kelasX = Kelas::create(['nama_kelas' => 'X RPL 1', 'jurusan' => 'RPL']);
        $kelasXi = Kelas::create(['nama_kelas' => 'XI RPL 1', 'jurusan' => 'RPL']);
        $kelasXii = Kelas::create(['nama_kelas' => 'XII RPL 1', 'jurusan' => 'RPL']);

        $rombelX = Rombel::create(['kelas_id' => $kelasX->id, 'tahun_ajaran' => '2026/2027', 'wali_kelas_id' => $user->id]);
        $rombelXi = Rombel::create(['kelas_id' => $kelasXi->id, 'tahun_ajaran' => '2026/2027', 'wali_kelas_id' => $user->id]);
        $rombelXii = Rombel::create(['kelas_id' => $kelasXii->id, 'tahun_ajaran' => '2026/2027', 'wali_kelas_id' => $user->id]);

        // Siswa Kelas X
        $siswaX = MasterSiswa::create([
            'nama_lengkap' => 'Siswa Kelas Sepuluh',
            'nis' => '1001',
            'jenis_kelamin' => 'L',
        ]);
        $siswaX->rombels()->attach($rombelX->id);

        // Siswa Kelas XI
        $siswaXi = MasterSiswa::create([
            'nama_lengkap' => 'Siswa Kelas Sebelas',
            'nis' => '1101',
            'jenis_kelamin' => 'L',
        ]);
        $siswaXi->rombels()->attach($rombelXi->id);

        // Siswa Kelas XII (Belum Dimapping)
        $siswaXiiBelum = MasterSiswa::create([
            'nama_lengkap' => 'Siswa Kelas Duabelas Belum Mapping',
            'nis' => '1201',
            'jenis_kelamin' => 'L',
        ]);
        $siswaXiiBelum->rombels()->attach($rombelXii->id);

        // Siswa Kelas XII (Sudah Dimapping ke Rombel Lain)
        $siswaXiiSudah = MasterSiswa::create([
            'nama_lengkap' => 'Siswa Kelas Duabelas Sudah Mapping',
            'nis' => '1202',
            'jenis_kelamin' => 'L',
        ]);
        $siswaXiiSudah->rombels()->attach($rombelXii->id);

        $rombelLain = PrakerinRombel::create([
            'nama_rombel' => 'PKL Lain',
            'prakerin_industri_id' => $industri->id,
            'status' => 'aktif',
        ]);

        PrakerinPenempatan::create([
            'master_siswa_id' => $siswaXiiSudah->id,
            'prakerin_rombel_id' => $rombelLain->id,
            'prakerin_industri_id' => $industri->id,
            'master_guru_id' => $guru->id,
            'nama_pembimbing_industri' => 'Bpk. Pembimbing Industri',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-12-31',
            'status' => 'aktif',
        ]);

        // Akses halaman mapping
        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'Hubin Sinergi UP dan Alumni'])
            ->get(route('hubin.rombel-pkl.mapping', $rombel->id));

        $response->assertOk();
        // Kelas XII yang belum dimapping HARUS muncul
        $response->assertSee('Siswa Kelas Duabelas Belum Mapping');
        // Kelas X, XI, dan XII yang sudah dimapping TIDAK BOLEH muncul di daftar mapping
        $response->assertDontSee('Siswa Kelas Sepuluh');
        $response->assertDontSee('Siswa Kelas Sebelas');
        $response->assertDontSee('Siswa Kelas Duabelas Sudah Mapping');
    }

    public function test_can_add_and_remove_student_mapping(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Hubin Sinergi UP dan Alumni');

        $industri = PrakerinIndustri::create([
            'nama_industri' => 'PT Telkom',
            'alamat' => 'Jl. Pahlawan',
            'kota' => 'Bandar Lampung',
            'is_mou_active' => true,
        ]);

        $guru = MasterGuru::create([
            'nama_lengkap' => 'Guru Pembimbing Telkom',
            'kode_guru' => 'GPT',
            'nik' => '1122334455667788',
            'jenis_kelamin' => 'L',
        ]);

        $internal = PrakerinPembimbing::create([
            'tipe' => 'internal',
            'master_guru_id' => $guru->id,
            'nama' => $guru->nama_lengkap,
            'is_active' => true,
        ]);

        $rombel = PrakerinRombel::create([
            'nama_rombel' => 'PKL Telkom XII RPL',
            'prakerin_industri_id' => $industri->id,
            'pembimbing_internal_id' => $internal->id,
            'status' => 'aktif',
        ]);

        $kelasXii = Kelas::create(['nama_kelas' => 'XII TKJ 2', 'jurusan' => 'TKJ']);
        $rombelXii = Rombel::create(['kelas_id' => $kelasXii->id, 'tahun_ajaran' => '2026/2027', 'wali_kelas_id' => $user->id]);

        $siswa = MasterSiswa::create([
            'nama_lengkap' => 'Muhammad Budi Santoso',
            'nis' => '12099',
            'jenis_kelamin' => 'L',
        ]);
        $siswa->rombels()->attach($rombelXii->id);

        // Store Mapping
        $storeResponse = $this->actingAs($user)
            ->withSession(['active_role' => 'Hubin Sinergi UP dan Alumni'])
            ->post(route('hubin.rombel-pkl.mapping.store', $rombel->id), [
                'master_siswa_ids' => [$siswa->id],
            ]);

        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('prakerin_penempatans', [
            'master_siswa_id' => $siswa->id,
            'prakerin_rombel_id' => $rombel->id,
            'prakerin_industri_id' => $industri->id,
            'status' => 'aktif',
        ]);

        $penempatan = PrakerinPenempatan::where('master_siswa_id', $siswa->id)->first();

        // Remove Mapping
        $removeResponse = $this->actingAs($user)
            ->withSession(['active_role' => 'Hubin Sinergi UP dan Alumni'])
            ->delete(route('hubin.rombel-pkl.mapping.destroy', [$rombel->id, $penempatan->id]));

        $removeResponse->assertRedirect();
        $this->assertDatabaseMissing('prakerin_penempatans', [
            'id' => $penempatan->id,
        ]);
    }

    public function test_navigation_renders_rombel_pkl_menu_for_hubin_and_super_admin(): void
    {
        $hubinUser = User::factory()->create();
        $hubinUser->assignRole('Hubin Sinergi UP dan Alumni');

        $hubinResponse = $this->actingAs($hubinUser)
            ->withSession(['active_role' => 'Hubin Sinergi UP dan Alumni'])
            ->get(route('hubin.rombel-pkl.index'));

        $hubinResponse->assertSee('Rombel PKL');
        $hubinResponse->assertSee(route('hubin.rombel-pkl.index'));

        $adminUser = User::factory()->create();
        $adminUser->assignRole('Super Admin');

        $adminResponse = $this->actingAs($adminUser)
            ->withSession(['active_role' => 'Super Admin'])
            ->get(route('hubin.rombel-pkl.index'));

        $adminResponse->assertSee('Rombel PKL');
        $adminResponse->assertSee(route('hubin.rombel-pkl.index'));
    }
}
