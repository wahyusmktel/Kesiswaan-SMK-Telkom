<?php

namespace Tests\Feature;

use App\Models\MasterSiswa;
use App\Models\PoinCategory;
use App\Models\PoinPeraturan;
use App\Models\SiswaPelanggaran;
use App\Models\SiswaPrestasi;
use App\Models\SiswaPemutihan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViolationPointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_point_calculation()
    {
        // 1. Setup User & Siswa
        $user = User::factory()->create();
        $siswa = MasterSiswa::create([
            'nis' => '12345',
            'nama_lengkap' => 'Test Student',
            'user_id' => $user->id,
            'jenis_kelamin' => 'L',
        ]);

        // 2. Setup Category & Regulation
        $category = PoinCategory::create(['name' => 'Disiplin', 'description' => 'Test']);
        $peraturan1 = PoinPeraturan::create([
            'poin_category_id' => $category->id,
            'pasal' => 'Pasal 1',
            'deskripsi' => 'Terlambat',
            'bobot_poin' => 10,
        ]);
        $peraturan2 = PoinPeraturan::create([
            'poin_category_id' => $category->id,
            'pasal' => 'Pasal 2',
            'deskripsi' => 'Bolos',
            'bobot_poin' => 25,
        ]);

        // 3. Record Violations (10 + 25 = 35)
        SiswaPelanggaran::create([
            'master_siswa_id' => $siswa->id,
            'poin_peraturan_id' => $peraturan1->id,
            'tanggal' => now(),
            'pelapor_id' => $user->id,
        ]);
        SiswaPelanggaran::create([
            'master_siswa_id' => $siswa->id,
            'poin_peraturan_id' => $peraturan2->id,
            'tanggal' => now(),
            'pelapor_id' => $user->id,
        ]);

        $siswa->refresh();
        $this->assertEquals(35, $siswa->getTotalViolationPoints());

        // 4. Record Achievement (Bonus 15) -> Net: 35 - 15 = 20
        SiswaPrestasi::create([
            'master_siswa_id' => $siswa->id,
            'nama_prestasi' => 'Juara 1',
            'tanggal' => now(),
            'poin_bonus' => 15,
        ]);

        $siswa->refresh();
        $this->assertEquals(15, $siswa->getTotalAchievementPoints());
        $this->assertEquals(20, $siswa->getCurrentPoints());

        // 5. Record Expungement (Reduction 5) -> Net: 20 - 5 = 15
        SiswaPemutihan::create([
            'master_siswa_id' => $siswa->id,
            'tanggal' => now(),
            'poin_dikurangi' => 5,
            'keterangan' => 'Bersih-bersih',
        ]);

        $siswa->refresh();
        $this->assertEquals(5, $siswa->getTotalExpungementPoints());
        $this->assertEquals(15, $siswa->getCurrentPoints());

        // 6. Check Status
        $status = $siswa->getPointStatus();
        $this->assertEquals('Aman', $status['label']); // < 50 is Aman
    }

    public function test_student_critical_status()
    {
        $user = User::factory()->create();
        $siswa = MasterSiswa::create([
            'nis' => '67890',
            'nama_lengkap' => 'Bad Student',
            'user_id' => $user->id,
            'jenis_kelamin' => 'L',
        ]);

        $category = PoinCategory::create(['name' => 'Disiplin', 'description' => 'Test']);
        $peraturan = PoinPeraturan::create([
            'poin_category_id' => $category->id,
            'pasal' => 'Pasal Berat',
            'deskripsi' => 'Narkoba',
            'bobot_poin' => 150,
        ]);

        SiswaPelanggaran::create([
            'master_siswa_id' => $siswa->id,
            'poin_peraturan_id' => $peraturan->id,
            'tanggal' => now(),
            'pelapor_id' => $user->id,
        ]);

        $siswa->refresh();
        $points = $siswa->getCurrentPoints();
        $status = $siswa->getPointStatus();
        $this->assertEquals('Sangat Kritis', $status['label']);
    }

    public function test_parent_summons_creation()
    {
        $user = User::factory()->create();
        $siswa = MasterSiswa::create([
            'nis' => '55555',
            'nama_lengkap' => 'Summons Test',
            'user_id' => $user->id,
            'jenis_kelamin' => 'L',
        ]);

        $category = PoinCategory::create(['name' => 'Disiplin', 'description' => 'Test']);
        $peraturan = PoinPeraturan::create([
            'poin_category_id' => $category->id,
            'pasal' => 'Extra 1',
            'deskripsi' => 'Major Violation',
            'bobot_poin' => 120,
        ]);

        SiswaPelanggaran::create([
            'master_siswa_id' => $siswa->id,
            'poin_peraturan_id' => $peraturan->id,
            'tanggal' => now(),
            'pelapor_id' => $user->id,
        ]);

        $panggilan = \App\Models\SiswaPanggilan::create([
            'master_siswa_id' => $siswa->id,
            'nomor_surat' => '001/TEST/2025',
            'tanggal_panggilan' => now()->addDay(),
            'jam_panggilan' => '09:00',
            'tempat_panggilan' => 'Ruang Guru',
            'perihal' => 'Test Panggilan',
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('siswa_panggilans', [
            'nomor_surat' => '001/TEST/2025',
            'master_siswa_id' => $siswa->id
        ]);
        
        $this->assertEquals(1, $siswa->panggilans()->count());
    }
}
