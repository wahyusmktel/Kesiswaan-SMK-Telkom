<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\MasterSiswa;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use ZipArchive;

class RombelDownloadAccountsTest extends TestCase
{
    use RefreshDatabase;

    protected User $operator;
    protected TahunPelajaran $activeYear;
    protected Rombel $rombel1;
    protected Rombel $rombel2;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup roles & permissions
        $operatorRole = Role::firstOrCreate(['name' => 'Operator']);
        Role::firstOrCreate(['name' => 'Siswa']);
        Role::firstOrCreate(['name' => 'Wali Kelas']);

        $permission = Permission::firstOrCreate(['name' => 'manage rombel']);
        $operatorRole->givePermissionTo($permission);

        $this->operator = User::factory()->create();
        $this->operator->assignRole('Operator');

        $this->activeYear = TahunPelajaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        $wali1 = User::factory()->create(['name' => 'Budi Santoso, S.Kom']);
        $wali1->assignRole('Wali Kelas');

        $wali2 = User::factory()->create(['name' => 'Siti Rahma, M.Pd']);
        $wali2->assignRole('Wali Kelas');

        $kelas1 = Kelas::create(['nama_kelas' => 'X RPL 1', 'jurusan' => 'RPL']);
        $kelas2 = Kelas::create(['nama_kelas' => 'X TJKT 1', 'jurusan' => 'TJKT']);

        $this->rombel1 = Rombel::create([
            'tahun_ajaran' => $this->activeYear->tahun,
            'tahun_pelajaran_id' => $this->activeYear->id,
            'kelas_id' => $kelas1->id,
            'wali_kelas_id' => $wali1->id,
        ]);

        $this->rombel2 = Rombel::create([
            'tahun_ajaran' => $this->activeYear->tahun,
            'tahun_pelajaran_id' => $this->activeYear->id,
            'kelas_id' => $kelas2->id,
            'wali_kelas_id' => $wali2->id,
        ]);

        // Create students for rombel 1
        $userStudent1 = User::factory()->create([
            'name' => 'Ahmad Fauzi',
            'email' => '12345@smktelkom-lpg.sch.id',
        ]);
        $userStudent1->assignRole('Siswa');

        $siswa1 = MasterSiswa::create([
            'nis' => '12345',
            'nama_lengkap' => 'Ahmad Fauzi',
            'jenis_kelamin' => 'L',
            'user_id' => $userStudent1->id,
            'status' => 'aktif',
        ]);
        $siswa1->dapodik()->create(['nisn' => '0012345678']);

        $siswa2 = MasterSiswa::create([
            'nis' => '12346',
            'nama_lengkap' => 'Bunga Citra',
            'jenis_kelamin' => 'P',
            'user_id' => null, // student without account yet
            'status' => 'aktif',
        ]);
        $siswa2->dapodik()->create(['nisn' => '0012345679']);

        $this->rombel1->siswa()->attach([$siswa1->id, $siswa2->id]);

        // Create student for rombel 2
        $siswa3 = MasterSiswa::create([
            'nis' => '54321',
            'nama_lengkap' => 'Cahyo Utomo',
            'jenis_kelamin' => 'L',
            'user_id' => null,
            'status' => 'aktif',
        ]);
        $siswa3->dapodik()->create(['nisn' => '0098765432']);

        $this->rombel2->siswa()->attach([$siswa3->id]);
    }

    public function test_rombel_index_displays_unduh_akun_buttons(): void
    {
        $response = $this->actingAs($this->operator)->get(route('master-data.rombel.index'));

        $response->assertOk();
        $response->assertSee('Unduh Akun Siswa (ZIP)');
        $response->assertSee(route('master-data.rombel.download-accounts'));
        $response->assertSee('Akun (PDF)');
        $response->assertSee(route('master-data.rombel.download-account-pdf', $this->rombel1->id));
    }

    public function test_operator_can_download_all_student_accounts_as_zip(): void
    {
        $response = $this->actingAs($this->operator)->get(route('master-data.rombel.download-accounts'));

        $response->assertOk();
        $disposition = $response->headers->get('content-disposition');
        $this->assertNotNull($disposition);
        $this->assertStringContainsString('.zip', $disposition);

        // Verify ZIP content
        $tempZip = tempnam(sys_get_temp_dir(), 'test_zip_');
        file_put_contents($tempZip, $response->streamedContent());

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($tempZip) === true);

        // Should contain PDF for X_RPL_1 and X_TJKT_1
        $fileNames = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $fileNames[] = $zip->getNameIndex($i);
        }

        $zip->close();
        @unlink($tempZip);

        $this->assertTrue(collect($fileNames)->contains(fn($name) => str_contains($name, 'x_rpl_1')));
        $this->assertTrue(collect($fileNames)->contains(fn($name) => str_contains($name, 'x_tjkt_1')));
    }

    public function test_operator_can_download_single_rombel_account_pdf(): void
    {
        $response = $this->actingAs($this->operator)->get(
            route('master-data.rombel.download-account-pdf', $this->rombel1->id)
        );

        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
        $this->assertStringContainsString('Akun_Siswa_x_rpl_1.pdf', $response->headers->get('content-disposition'));
    }

    public function test_unauthorized_user_cannot_download_accounts(): void
    {
        $regularUser = User::factory()->create();
        // Has no role/permission

        $responseZip = $this->actingAs($regularUser)->get(route('master-data.rombel.download-accounts'));
        $responseZip->assertForbidden();

        $responsePdf = $this->actingAs($regularUser)->get(
            route('master-data.rombel.download-account-pdf', $this->rombel1->id)
        );
        $responsePdf->assertForbidden();
    }
}
