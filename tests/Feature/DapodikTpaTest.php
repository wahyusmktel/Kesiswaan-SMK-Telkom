<?php

namespace Tests\Feature;

use App\Imports\DapodikGuruImport;
use App\Models\DapodikGuru;
use App\Models\MasterGuru;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DapodikTpaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.key' => 'base64:'.base64_encode(str_repeat('d', 32))]);
        $this->withoutVite();
    }

    public function test_tpa_import_uses_dapodik_format_and_links_employee_by_nik(): void
    {
        $employee = MasterGuru::create([
            'nama_lengkap' => 'TPA Laboratorium',
            'jenis_kelamin' => 'L',
            'nik' => '1801000000000001',
        ]);
        $row = array_fill(0, 51, null);
        $row[1] = 'TPA Laboratorium';
        $row[3] = 'L';
        $row[7] = 'Pegawai Part Time';
        $row[8] = 'Tenaga Administrasi Sekolah';
        $row[18] = '081234567890';
        $row[44] = '1801000000000001';

        $import = new DapodikGuruImport(DapodikGuru::CATEGORY_TPA);
        $import->collection(collect([$row]));

        $this->assertSame(1, $import->created);
        $this->assertDatabaseHas('dapodik_gurus', [
            'master_guru_id' => $employee->id,
            'employee_category' => DapodikGuru::CATEGORY_TPA,
            'nama' => 'TPA Laboratorium',
            'status_kepegawaian' => 'Pegawai Part Time',
            'hp' => '081234567890',
        ]);
        $this->assertDatabaseHas('master_gurus', [
            'id' => $employee->id,
            'employee_category' => MasterGuru::CATEGORY_TPA,
        ]);
    }

    public function test_tpa_import_creates_master_employee_when_nik_is_not_registered(): void
    {
        $row = array_fill(0, 51, null);
        $row[1] = 'TPA Baru';
        $row[2] = '1234567890123456';
        $row[3] = 'P';
        $row[44] = '1801000000000099';

        $import = new DapodikGuruImport(DapodikGuru::CATEGORY_TPA);
        $import->collection(collect([$row]));

        $master = MasterGuru::where('nik', '1801000000000099')->firstOrFail();
        $this->assertSame(MasterGuru::CATEGORY_TPA, $master->employee_category);
        $this->assertSame('TPA Baru', $master->nama_lengkap);
        $this->assertSame('P', $master->jenis_kelamin);
        $this->assertDatabaseHas('dapodik_gurus', [
            'master_guru_id' => $master->id,
            'employee_category' => DapodikGuru::CATEGORY_TPA,
        ]);
    }

    public function test_tpa_page_is_separated_from_teacher_dapodik_page(): void
    {
        $operator = User::factory()->create();
        $operator->assignRole(Role::findOrCreate('Operator', 'web'));
        DapodikGuru::create(['employee_category' => 'guru', 'nik' => '1', 'nama' => 'Data Guru']);
        DapodikGuru::create(['employee_category' => 'tpa', 'nik' => '2', 'nama' => 'Data TPA']);
        MasterGuru::create(['nama_lengkap' => 'Pilihan Guru', 'jenis_kelamin' => 'L']);
        MasterGuru::create(['employee_category' => 'tpa', 'nama_lengkap' => 'Pilihan TPA', 'jenis_kelamin' => 'P']);

        $this->actingAs($operator)->withSession(['active_role' => 'Operator']);
        $this->get(route('dapodik-tpa.index'))
            ->assertOk()
            ->assertSee('Data Dapodik TPA')
            ->assertSee('Data TPA')
            ->assertDontSee('Data Guru')
            ->assertSee('Pilihan TPA')
            ->assertDontSee('Pilihan Guru');
        $this->get(route('dapodik-guru.index'))
            ->assertOk()
            ->assertSee('Data Guru')
            ->assertDontSee('Data TPA')
            ->assertSee('Pilihan Guru')
            ->assertDontSee('Pilihan TPA');
    }

    public function test_tpa_mapping_rejects_teacher_master_employee(): void
    {
        $operator = User::factory()->create();
        $operator->assignRole(Role::findOrCreate('Operator', 'web'));
        $tpa = DapodikGuru::create(['employee_category' => 'tpa', 'nik' => '2', 'nama' => 'Data TPA']);
        $teacher = MasterGuru::create(['nama_lengkap' => 'Guru Bukan TPA', 'jenis_kelamin' => 'L']);

        $this->actingAs($operator)->withSession(['active_role' => 'Operator'])
            ->patch(route('dapodik-tpa.mapping.update', $tpa), ['master_guru_id' => $teacher->id])
            ->assertSessionHasErrors('master_guru_id');

        $this->assertNull($tpa->fresh()->master_guru_id);
    }
}
