<?php

namespace Tests\Feature;

use App\Models\DapodikSiswa;
use App\Models\MasterSiswa;
use App\Models\StudentRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentRegistrationBulkDapodikMappingTest extends TestCase
{
    use RefreshDatabase;

    public function test_bulk_match_preview_matches_by_nisn_and_name_and_reports_unmatched(): void
    {
        $this->withoutMiddleware();
        $user = User::factory()->create();

        // Student A: Matches by NISN
        $studentA = MasterSiswa::create([
            'nama_lengkap' => 'Ahmad Fauzi',
            'nis' => 'SEM-001',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandar Lampung',
            'tanggal_lahir' => '2010-05-10',
            'alamat' => 'Jl. Mawar No. 1',
        ]);
        $regA = StudentRegistration::create([
            'status' => 'approved',
            'nama_lengkap' => 'Ahmad Fauzi',
            'nisn' => '1111111111',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandar Lampung',
            'tanggal_lahir' => '2010-05-10',
            'nomor_hp' => '081234567890',
            'alamat' => 'Jl. Mawar No. 1',
            'master_siswa_id' => $studentA->id,
        ]);

        // Student B: Matches by exact Name and Birthdate (NISN in registration is empty or different)
        $studentB = MasterSiswa::create([
            'nama_lengkap' => 'Budi Santoso',
            'nis' => 'SEM-002',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Metro',
            'tanggal_lahir' => '2010-08-15',
            'alamat' => 'Jl. Melati No. 2',
        ]);
        $regB = StudentRegistration::create([
            'status' => 'approved',
            'nama_lengkap' => 'Budi Santoso',
            'nisn' => null,
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Metro',
            'tanggal_lahir' => '2010-08-15',
            'nomor_hp' => '081234567891',
            'alamat' => 'Jl. Melati No. 2',
            'master_siswa_id' => $studentB->id,
        ]);

        // Student C: No match in Dapodik
        $studentC = MasterSiswa::create([
            'nama_lengkap' => 'Citra Lestari',
            'nis' => 'SEM-003',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Pringsewu',
            'tanggal_lahir' => '2010-12-20',
            'alamat' => 'Jl. Anggrek No. 3',
        ]);
        $regC = StudentRegistration::create([
            'status' => 'approved',
            'nama_lengkap' => 'Citra Lestari',
            'nisn' => '3333333333',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Pringsewu',
            'tanggal_lahir' => '2010-12-20',
            'nomor_hp' => '081234567892',
            'alamat' => 'Jl. Anggrek No. 3',
            'master_siswa_id' => $studentC->id,
        ]);

        // Dapodik records
        $dapodik1 = DapodikSiswa::create([
            'nama' => 'Ahmad Fauzi',
            'nipd' => '2026001',
            'nisn' => '1111111111',
            'tanggal_lahir' => '2010-05-10',
            'jenis_kelamin' => 'L',
            'master_siswa_id' => null,
        ]);

        $dapodik2 = DapodikSiswa::create([
            'nama' => 'Budi Santoso',
            'nipd' => '2026002',
            'nisn' => '2222222222',
            'tanggal_lahir' => '2010-08-15',
            'jenis_kelamin' => 'L',
            'master_siswa_id' => null,
        ]);

        $response = $this->actingAs($user)->getJson(route('master-data.student-registration.dapodik.bulk-match-preview'));

        $response->assertOk();
        $response->assertJson([
            'total_candidates' => 3,
            'total_matched' => 2,
            'total_unmatched' => 1,
        ]);

        $data = $response->json();
        $this->assertEquals($regA->id, $data['matched'][0]['registration_id']);
        $this->assertEquals($dapodik1->id, $data['matched'][0]['dapodik_id']);
        $this->assertEquals('nisn', $data['matched'][0]['match_type']);

        $this->assertEquals($regB->id, $data['matched'][1]['registration_id']);
        $this->assertEquals($dapodik2->id, $data['matched'][1]['dapodik_id']);
        $this->assertEquals('name_and_birth', $data['matched'][1]['match_type']);

        $this->assertEquals($regC->id, $data['unmatched'][0]['registration_id']);
    }

    public function test_bulk_map_updates_master_siswa_and_links_dapodik(): void
    {
        $this->withoutMiddleware();
        $user = User::factory()->create();

        $studentA = MasterSiswa::create([
            'nama_lengkap' => 'Ahmad Fauzi',
            'nis' => 'SEM-001',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandar Lampung',
            'tanggal_lahir' => '2010-05-10',
            'alamat' => 'Jl. Mawar No. 1',
        ]);
        $regA = StudentRegistration::create([
            'status' => 'approved',
            'nama_lengkap' => 'Ahmad Fauzi',
            'nisn' => '1111111111',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandar Lampung',
            'tanggal_lahir' => '2010-05-10',
            'nomor_hp' => '081234567890',
            'alamat' => 'Jl. Mawar No. 1',
            'master_siswa_id' => $studentA->id,
        ]);

        $studentB = MasterSiswa::create([
            'nama_lengkap' => 'Budi Santoso',
            'nis' => 'SEM-002',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Metro',
            'tanggal_lahir' => '2010-08-15',
            'alamat' => 'Jl. Melati No. 2',
        ]);
        $regB = StudentRegistration::create([
            'status' => 'approved',
            'nama_lengkap' => 'Budi Santoso',
            'nisn' => '2222222222',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Metro',
            'tanggal_lahir' => '2010-08-15',
            'nomor_hp' => '081234567891',
            'alamat' => 'Jl. Melati No. 2',
            'master_siswa_id' => $studentB->id,
        ]);

        $dapodik1 = DapodikSiswa::create([
            'nama' => 'Ahmad Fauzi Resmi',
            'nipd' => '2026001',
            'nisn' => '1111111111',
            'tanggal_lahir' => '2010-05-10',
            'jenis_kelamin' => 'L',
            'master_siswa_id' => null,
        ]);

        $dapodik2 = DapodikSiswa::create([
            'nama' => 'Budi Santoso Resmi',
            'nipd' => '2026002',
            'nisn' => '2222222222',
            'tanggal_lahir' => '2010-08-15',
            'jenis_kelamin' => 'L',
            'master_siswa_id' => null,
        ]);

        $response = $this->actingAs($user)->postJson(route('master-data.student-registration.dapodik.bulk-map'), [
            'mappings' => [
                [
                    'registration_id' => $regA->id,
                    'dapodik_siswa_id' => $dapodik1->id,
                ],
                [
                    'registration_id' => $regB->id,
                    'dapodik_siswa_id' => $dapodik2->id,
                ],
            ],
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'count' => 2,
        ]);

        // Student A verified
        $regA->refresh();
        $studentA->refresh();
        $dapodik1->refresh();

        $this->assertEquals('mapped', $regA->status);
        $this->assertEquals($dapodik1->id, $regA->dapodik_siswa_id);
        $this->assertEquals('2026001', $studentA->nis);
        $this->assertEquals('Ahmad Fauzi Resmi', $studentA->nama_lengkap);
        $this->assertEquals('dapodik', $studentA->data_source);
        $this->assertTrue((bool) $studentA->is_data_verified);
        $this->assertEquals($studentA->id, $dapodik1->master_siswa_id);

        // Student B verified
        $regB->refresh();
        $studentB->refresh();
        $dapodik2->refresh();

        $this->assertEquals('mapped', $regB->status);
        $this->assertEquals($dapodik2->id, $regB->dapodik_siswa_id);
        $this->assertEquals('2026002', $studentB->nis);
        $this->assertEquals('Budi Santoso Resmi', $studentB->nama_lengkap);
        $this->assertEquals($studentB->id, $dapodik2->master_siswa_id);
    }
}
