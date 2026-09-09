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
    }

    public function test_tpa_page_is_separated_from_teacher_dapodik_page(): void
    {
        $operator = User::factory()->create();
        $operator->assignRole(Role::findOrCreate('Operator', 'web'));
        DapodikGuru::create(['employee_category' => 'guru', 'nik' => '1', 'nama' => 'Data Guru']);
        DapodikGuru::create(['employee_category' => 'tpa', 'nik' => '2', 'nama' => 'Data TPA']);

        $this->actingAs($operator)->withSession(['active_role' => 'Operator']);
        $this->get(route('dapodik-tpa.index'))
            ->assertOk()
            ->assertSee('Data Dapodik TPA')
            ->assertSee('Data TPA')
            ->assertDontSee('Data Guru');
        $this->get(route('dapodik-guru.index'))
            ->assertOk()
            ->assertSee('Data Guru')
            ->assertDontSee('Data TPA');
    }
}
