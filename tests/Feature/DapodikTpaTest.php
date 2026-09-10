<?php

namespace Tests\Feature;

use App\Imports\DapodikGuruImport;
use App\Models\DapodikGuru;
use App\Models\MasterGuru;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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

    public function test_account_sync_keeps_every_existing_account_role(): void
    {
        $operator = User::factory()->create();
        $operator->assignRole(Role::findOrCreate('Operator', 'web'));
        $security = User::factory()->create(['email' => 'security.tpa@example.test']);
        $security->assignRole(Role::findOrCreate('Security', 'web'));
        $master = MasterGuru::create([
            'employee_category' => MasterGuru::CATEGORY_TPA,
            'nama_lengkap' => 'TPA Security',
            'nik' => '1801000000000077',
            'jenis_kelamin' => 'L',
        ]);
        DapodikGuru::create([
            'employee_category' => DapodikGuru::CATEGORY_TPA,
            'master_guru_id' => $master->id,
            'nama' => 'TPA Security',
            'nik' => '1801000000000077',
            'jenis_kelamin' => 'L',
            'email_dapodik' => $security->email,
        ]);

        $this->actingAs($operator)->withSession(['active_role' => 'Operator'])
            ->post(route('dapodik-tpa.accounts.sync'))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame($security->id, $master->fresh()->user_id);
        $this->assertTrue($security->fresh()->hasRole('Security'));
        $this->assertFalse($security->fresh()->hasRole('TPA'));
    }

    public function test_operator_can_reconcile_existing_headmaster_account_without_changing_its_role(): void
    {
        $operator = User::factory()->create();
        $operator->assignRole(Role::findOrCreate('Operator', 'web'));
        $headmaster = User::factory()->create(['email' => 'kepala.sekolah@example.test']);
        $headmaster->assignRole(Role::findOrCreate('Kepala Sekolah', 'web'));
        $accountMaster = MasterGuru::create([
            'nama_lengkap' => 'Kepala Sekolah',
            'jenis_kelamin' => 'L',
            'user_id' => $headmaster->id,
            'employee_category' => MasterGuru::CATEGORY_TEACHER,
        ]);
        $duplicateMaster = MasterGuru::create([
            'nama_lengkap' => 'Kepala Sekolah',
            'jenis_kelamin' => 'L',
            'nik' => '1801000000000011',
            'nuptk' => '1234567890123456',
            'employee_category' => MasterGuru::CATEGORY_TPA,
        ]);
        $dapodik = DapodikGuru::create([
            'employee_category' => DapodikGuru::CATEGORY_TPA,
            'master_guru_id' => $duplicateMaster->id,
            'nama' => 'Kepala Sekolah',
            'nik' => '1801000000000011',
            'nuptk' => '1234567890123456',
            'email_dapodik' => 'email.dapodik.berbeda@example.test',
        ]);

        $this->actingAs($operator)->withSession(['active_role' => 'Operator'])
            ->patch(route('dapodik-tpa.account.reconcile', $dapodik), ['user_id' => $headmaster->id])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame($accountMaster->id, $dapodik->fresh()->master_guru_id);
        $this->assertDatabaseHas('master_gurus', [
            'id' => $accountMaster->id,
            'user_id' => $headmaster->id,
            'nik' => '1801000000000011',
            'nuptk' => '1234567890123456',
            'employee_category' => MasterGuru::CATEGORY_TPA,
        ]);
        $this->assertDatabaseHas('master_gurus', [
            'id' => $duplicateMaster->id,
            'nik' => null,
            'nuptk' => null,
            'is_active' => false,
        ]);
        $this->assertTrue($headmaster->fresh()->hasRole('Kepala Sekolah'));
        $this->assertFalse($headmaster->fresh()->hasRole('TPA'));

        $this->get(route('dapodik-tpa.index'))
            ->assertOk()
            ->assertSee('kepala.sekolah@example.test')
            ->assertSee('Sinkron:');
    }

    public function test_account_reconciliation_rejects_conflicting_identity(): void
    {
        $operator = User::factory()->create();
        $operator->assignRole(Role::findOrCreate('Operator', 'web'));
        $account = User::factory()->create();
        $account->assignRole(Role::findOrCreate('Kepala Sekolah', 'web'));
        $accountMaster = MasterGuru::create([
            'nama_lengkap' => 'Pegawai Berbeda',
            'jenis_kelamin' => 'P',
            'user_id' => $account->id,
            'nik' => '1801000000000022',
            'employee_category' => MasterGuru::CATEGORY_TPA,
        ]);
        $dapodikMaster = MasterGuru::create([
            'nama_lengkap' => 'Data Dapodik',
            'jenis_kelamin' => 'L',
            'nik' => '1801000000000033',
            'employee_category' => MasterGuru::CATEGORY_TPA,
        ]);
        $dapodik = DapodikGuru::create([
            'employee_category' => DapodikGuru::CATEGORY_TPA,
            'master_guru_id' => $dapodikMaster->id,
            'nama' => 'Data Dapodik',
            'nik' => '1801000000000033',
        ]);

        $this->actingAs($operator)->withSession(['active_role' => 'Operator'])
            ->patch(route('dapodik-tpa.account.reconcile', $dapodik), ['user_id' => $account->id])
            ->assertSessionHasErrors('user_id');

        $this->assertSame($dapodikMaster->id, $dapodik->fresh()->master_guru_id);
        $this->assertSame('1801000000000022', $accountMaster->fresh()->nik);
        $this->assertTrue($dapodikMaster->fresh()->is_active);
    }

    public function test_operator_can_delete_tpa_dapodik_without_deleting_master_account_or_roles(): void
    {
        $operator = User::factory()->create();
        $operator->assignRole(Role::findOrCreate('Operator', 'web'));
        $employee = User::factory()->create();
        $employee->assignRole(Role::findOrCreate('Kepala Sekolah', 'web'));
        $master = MasterGuru::create([
            'nama_lengkap' => 'TPA Tetap Aman',
            'jenis_kelamin' => 'L',
            'nik' => '1801000000000044',
            'user_id' => $employee->id,
            'employee_category' => MasterGuru::CATEGORY_TPA,
        ]);
        $dapodik = DapodikGuru::create([
            'employee_category' => DapodikGuru::CATEGORY_TPA,
            'master_guru_id' => $master->id,
            'nama' => 'TPA Tetap Aman',
            'nik' => '1801000000000044',
        ]);

        $this->actingAs($operator)->withSession(['active_role' => 'Operator'])
            ->delete(route('dapodik-tpa.destroy', $dapodik))
            ->assertRedirect(route('dapodik-tpa.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('dapodik_gurus', ['id' => $dapodik->id]);
        $this->assertDatabaseHas('master_gurus', ['id' => $master->id, 'user_id' => $employee->id]);
        $this->assertDatabaseHas('users', ['id' => $employee->id]);
        $this->assertTrue($employee->fresh()->hasRole('Kepala Sekolah'));
    }

    public function test_tpa_delete_route_cannot_delete_teacher_dapodik(): void
    {
        $operator = User::factory()->create();
        $operator->assignRole(Role::findOrCreate('Operator', 'web'));
        $teacher = DapodikGuru::create([
            'employee_category' => DapodikGuru::CATEGORY_TEACHER,
            'nama' => 'Guru Tidak Boleh Terhapus',
            'nik' => '1801000000000055',
        ]);

        $this->actingAs($operator)->withSession(['active_role' => 'Operator'])
            ->delete(route('dapodik-tpa.destroy', $teacher))
            ->assertNotFound();

        $this->assertDatabaseHas('dapodik_gurus', ['id' => $teacher->id]);
    }

    public function test_operator_can_create_headmaster_account_directly_from_tpa_dapodik(): void
    {
        $operator = User::factory()->create();
        $operator->assignRole(Role::findOrCreate('Operator', 'web'));
        Role::findOrCreate('Kepala Sekolah', 'web');
        $master = MasterGuru::create([
            'nama_lengkap' => 'Kepala Sekolah Baru',
            'jenis_kelamin' => 'L',
            'nik' => '1801000000000066',
            'employee_category' => MasterGuru::CATEGORY_TPA,
        ]);
        $dapodik = DapodikGuru::create([
            'employee_category' => DapodikGuru::CATEGORY_TPA,
            'master_guru_id' => $master->id,
            'nama' => 'Kepala Sekolah Baru',
            'nik' => '1801000000000066',
            'jenis_ptk' => 'Kepala Sekolah',
            'hp' => '081234567890',
        ]);

        $response = $this->actingAs($operator)->withSession(['active_role' => 'Operator'])
            ->post(route('dapodik-tpa.account.create', $dapodik), [
                'dapodik_id' => $dapodik->id,
                'email' => 'kepala.baru@example.test',
                'role' => 'Kepala Sekolah',
            ]);

        $response->assertRedirect()
            ->assertSessionHas('success')
            ->assertSessionHas('tpa_generated_credentials', fn ($rows) => count($rows) === 1
                && $rows[0]['email'] === 'kepala.baru@example.test'
                && $rows[0]['role'] === 'Kepala Sekolah');

        $credential = session('tpa_generated_credentials')[0];
        $account = User::where('email', 'kepala.baru@example.test')->firstOrFail();
        $this->assertTrue($account->hasRole('Kepala Sekolah'));
        $this->assertFalse($account->hasRole('TPA'));
        $this->assertTrue($account->must_change_password);
        $this->assertTrue(Hash::check($credential['password'], $account->password));
        $this->assertSame($account->id, $master->fresh()->user_id);
        $this->assertSame($master->id, $dapodik->fresh()->master_guru_id);
        $this->assertSame('kepala.baru@example.test', $dapodik->fresh()->email_dapodik);
        $this->assertSame('081234567890', $account->phone_number);
    }

    public function test_direct_tpa_account_creation_cannot_create_superadmin(): void
    {
        $operator = User::factory()->create();
        $operator->assignRole(Role::findOrCreate('Operator', 'web'));
        Role::findOrCreate('Super Admin', 'web');
        $master = MasterGuru::create([
            'nama_lengkap' => 'TPA Bukan Superadmin',
            'jenis_kelamin' => 'P',
            'employee_category' => MasterGuru::CATEGORY_TPA,
        ]);
        $dapodik = DapodikGuru::create([
            'employee_category' => DapodikGuru::CATEGORY_TPA,
            'master_guru_id' => $master->id,
            'nama' => 'TPA Bukan Superadmin',
        ]);

        $this->actingAs($operator)->withSession(['active_role' => 'Operator'])
            ->post(route('dapodik-tpa.account.create', $dapodik), [
                'email' => 'forbidden.superadmin@example.test',
                'role' => 'Super Admin',
            ])
            ->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'forbidden.superadmin@example.test']);
        $this->assertNull($master->fresh()->user_id);
    }

    public function test_account_sync_generates_tpa_account_and_forces_password_change(): void
    {
        $operator = User::factory()->create();
        $operator->assignRole(Role::findOrCreate('Operator', 'web'));
        $master = MasterGuru::create([
            'employee_category' => MasterGuru::CATEGORY_TPA,
            'nama_lengkap' => 'TPA Baru',
            'nik' => '1801000000000088',
            'jenis_kelamin' => 'P',
        ]);
        DapodikGuru::create([
            'employee_category' => DapodikGuru::CATEGORY_TPA,
            'master_guru_id' => $master->id,
            'nama' => 'TPA Baru',
            'nik' => '1801000000000088',
            'jenis_kelamin' => 'P',
            'email_dapodik' => 'tpa.baru@example.test',
            'hp' => '081234567890',
        ]);

        $response = $this->actingAs($operator)->withSession(['active_role' => 'Operator'])
            ->post(route('dapodik-tpa.accounts.sync'));

        $response->assertRedirect()
            ->assertSessionHas('tpa_generated_credentials', fn ($rows) => count($rows) === 1 && $rows[0]['email'] === 'tpa.baru@example.test');
        $credential = session('tpa_generated_credentials')[0];
        $this->get(route('dapodik-tpa.index'))
            ->assertOk()
            ->assertSee('Unduh Kredensial CSV');
        $account = User::where('email', 'tpa.baru@example.test')->firstOrFail();
        $this->assertTrue($account->hasRole('TPA'));
        $this->assertTrue($account->must_change_password);
        $this->assertTrue(Hash::check($credential['password'], $account->password));
        $this->assertSame($account->id, $master->fresh()->user_id);

        $this->actingAs($account)->withSession(['active_role' => 'TPA'])
            ->get(route('fingerprint-saya.index'))
            ->assertRedirect(route('profile.edit'));
        $this->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('Ganti password sementara Anda');

        $this->put(route('password.update'), [
            'current_password' => $credential['password'],
            'password' => 'Password-Baru-TPA-2026',
            'password_confirmation' => 'Password-Baru-TPA-2026',
        ])->assertSessionHas('status', 'password-updated');

        $this->assertFalse($account->fresh()->must_change_password);
        $this->get(route('fingerprint-saya.index'))->assertOk();
    }

    public function test_account_sync_does_not_create_account_without_valid_email(): void
    {
        $operator = User::factory()->create();
        $operator->assignRole(Role::findOrCreate('Operator', 'web'));
        $master = MasterGuru::create([
            'employee_category' => MasterGuru::CATEGORY_TPA,
            'nama_lengkap' => 'TPA Tanpa Email',
            'nik' => '1801000000000066',
            'jenis_kelamin' => 'L',
        ]);
        DapodikGuru::create([
            'employee_category' => DapodikGuru::CATEGORY_TPA,
            'master_guru_id' => $master->id,
            'nama' => 'TPA Tanpa Email',
            'nik' => '1801000000000066',
        ]);

        $this->actingAs($operator)->withSession(['active_role' => 'Operator'])
            ->post(route('dapodik-tpa.accounts.sync'))
            ->assertSessionHas('tpa_account_sync_errors', fn ($errors) => count($errors) === 1);

        $this->assertNull($master->fresh()->user_id);
    }
}
