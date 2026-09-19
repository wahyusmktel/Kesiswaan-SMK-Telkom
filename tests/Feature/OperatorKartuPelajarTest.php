<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\MasterSiswa;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Models\User;
use App\Services\KartuPelajarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

class OperatorKartuPelajarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_operator_can_access_kartu_pelajar_index(): void
    {
        $this->withoutMiddleware();
        $operator = User::factory()->create(['name' => 'Operator Siswa']);

        $tahun = TahunPelajaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        $kelas = Kelas::create(['nama_kelas' => 'X RPL 1', 'jurusan' => 'RPL']);
        $rombel = Rombel::create([
            'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $tahun->id,
            'tahun_ajaran' => '2026/2027',
            'wali_kelas_id' => $operator->id,
        ]);

        $student = MasterSiswa::create([
            'nama_lengkap' => 'Muhammad Budi',
            'nis' => '553241101',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '2009-06-16',
            'alamat' => 'Bandar Lampung',
            'status' => 'aktif',
        ]);
        $student->rombels()->attach($rombel->id);

        $response = $this->actingAs($operator)
            ->withSession(['active_role' => 'Operator'])
            ->get(route('operator.kartu-pelajar.index'));

        $response->assertOk();
        $response->assertSee('Kartu Pelajar');
        $response->assertSee('Muhammad Budi');
        $response->assertSee('553241101');
        $response->assertSee('X RPL 1');
    }

    public function test_can_load_preview_modal(): void
    {
        $this->withoutMiddleware();
        $operator = User::factory()->create();

        $student = MasterSiswa::create([
            'nama_lengkap' => 'Siti Nurhaliza',
            'nis' => '553241102',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Metro',
            'tanggal_lahir' => '2009-08-20',
            'alamat' => 'Jl. Mawar Indah',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($operator)
            ->withSession(['active_role' => 'Operator'])
            ->get(route('operator.kartu-pelajar.preview', $student->id));

        $response->assertOk();
        $response->assertSee('Siti Nurhaliza');
        $response->assertSee('553241102');
        $response->assertSee('data:image/png;base64');
    }

    public function test_can_download_single_student_jpg(): void
    {
        $this->withoutMiddleware();
        $operator = User::factory()->create();

        $tahun = TahunPelajaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);
        $kelas = Kelas::create(['nama_kelas' => 'X TKJ 2', 'jurusan' => 'RPL']);
        $rombel = Rombel::create([
            'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $tahun->id,
            'tahun_ajaran' => '2026/2027',
            'wali_kelas_id' => $operator->id,
        ]);

        $student = MasterSiswa::create([
            'nama_lengkap' => 'Andi Wijaya',
            'nis' => '553241103',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '2009-03-10',
            'status' => 'aktif',
        ]);
        $student->rombels()->attach($rombel->id);

        $response = $this->actingAs($operator)
            ->withSession(['active_role' => 'Operator'])
            ->get(route('operator.kartu-pelajar.download-jpg', $student->id));

        $response->assertOk();
        $this->assertStringContainsString('image/jpeg', $response->headers->get('content-type'));
        $this->assertStringContainsString('553241103.jpg', $response->headers->get('content-disposition'));
    }

    public function test_can_export_zip_per_class(): void
    {
        $this->withoutMiddleware();
        $operator = User::factory()->create();

        $tahun = TahunPelajaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);
        $kelas = Kelas::create(['nama_kelas' => 'X TKJ 1', 'jurusan' => 'RPL']);
        $rombel = Rombel::create([
            'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $tahun->id,
            'tahun_ajaran' => '2026/2027',
            'wali_kelas_id' => $operator->id,
        ]);

        $student = MasterSiswa::create([
            'nama_lengkap' => 'Fajar Pratama',
            'nis' => '553241104',
            'jenis_kelamin' => 'L',
            'status' => 'aktif',
        ]);
        $student->rombels()->attach($rombel->id);

        $response = $this->actingAs($operator)
            ->withSession(['active_role' => 'Operator'])
            ->get(route('operator.kartu-pelajar.export-zip', ['rombel_id' => $rombel->id]));

        $response->assertOk();
        $this->assertStringContainsString('application/zip', $response->headers->get('content-type'));
    }

    public function test_can_mass_upload_photos_via_zip(): void
    {
        $this->withoutMiddleware();
        $operator = User::factory()->create();

        $student = MasterSiswa::create([
            'nama_lengkap' => 'Rina Kartika',
            'nis' => '553241105',
            'jenis_kelamin' => 'P',
            'status' => 'aktif',
        ]);

        // Buat file ZIP sementara berisi foto dengan nama NIS
        $tempZipPath = tempnam(sys_get_temp_dir(), 'test_zip_') . '.zip';
        $zip = new ZipArchive();
        $zip->open($tempZipPath, ZipArchive::CREATE);

        // Buat dummy JPG menggunakan GD
        $img = imagecreatetruecolor(100, 100);
        ob_start();
        imagejpeg($img);
        $jpgData = ob_get_clean();
        imagedestroy($img);

        $zip->addFromString('553241105.jpg', $jpgData);
        $zip->addFromString('999999999.jpg', $jpgData); // NIS yang tidak ada
        $zip->close();

        $uploadedFile = new UploadedFile($tempZipPath, 'foto_siswa.zip', 'application/zip', null, true);

        $response = $this->actingAs($operator)
            ->withSession(['active_role' => 'Operator'])
            ->post(route('operator.kartu-pelajar.upload-foto-masal'), [
                'file_zip' => $uploadedFile,
            ]);

        $response->assertRedirect();

        $student->refresh();
        $this->assertNotNull($student->foto);
        $this->assertEquals('siswa_photos/553241105.jpg', $student->foto);
        Storage::disk('public')->assertExists('siswa_photos/553241105.jpg');

        if (file_exists($tempZipPath)) {
            @unlink($tempZipPath);
        }
    }

    public function test_can_access_print_views(): void
    {
        $this->withoutMiddleware();
        $operator = User::factory()->create();

        $tahun = TahunPelajaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);
        $kelas = Kelas::create(['nama_kelas' => 'X RPL 2', 'jurusan' => 'RPL']);
        $rombel = Rombel::create([
            'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $tahun->id,
            'tahun_ajaran' => '2026/2027',
            'wali_kelas_id' => $operator->id,
        ]);

        $student = MasterSiswa::create([
            'nama_lengkap' => 'Dewi Sartika',
            'nis' => '553241106',
            'jenis_kelamin' => 'P',
            'status' => 'aktif',
        ]);
        $student->rombels()->attach($rombel->id);

        // Single print
        $responseSingle = $this->actingAs($operator)
            ->withSession(['active_role' => 'Operator'])
            ->get(route('operator.kartu-pelajar.cetak', $student->id));
        $responseSingle->assertOk();
        $responseSingle->assertSee('Dewi Sartika');

        // Class print
        $responseClass = $this->actingAs($operator)
            ->withSession(['active_role' => 'Operator'])
            ->get(route('operator.kartu-pelajar.cetak-kelas', ['rombel_id' => $rombel->id]));
        $responseClass->assertOk();
        $responseClass->assertSee('X RPL 2');
        $responseClass->assertSee('Dewi Sartika');
    }
}
