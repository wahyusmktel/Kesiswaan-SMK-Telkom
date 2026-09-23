<?php

namespace Tests\Feature;

use App\Models\PrakerinIndustri;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HubinIndustriKerjasamaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Super Admin']);
        Role::firstOrCreate(['name' => 'Hubin Sinergi UP dan Alumni']);
        Role::firstOrCreate(['name' => 'Siswa']);
    }

    public function test_hubin_role_can_access_industri_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Hubin Sinergi UP dan Alumni');

        $this->assertEquals('hubin.industri.index', \App\Support\DashboardRedirector::routeNameForRole('Hubin Sinergi UP dan Alumni'));

        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'Hubin Sinergi UP dan Alumni'])
            ->get(route('hubin.industri.index'));

        $response->assertOk();
        $response->assertSee('Data Industri Kerjasama');
        $response->assertSee('HUBIN SINERGI UP & ALUMNI', false);
    }

    public function test_super_admin_can_access_industri_index(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');

        $response = $this->actingAs($admin)
            ->withSession(['active_role' => 'Super Admin'])
            ->get(route('hubin.industri.index'));

        $response->assertOk();
        $response->assertSee('Data Industri Kerjasama');
    }

    public function test_unauthorized_role_cannot_access_hubin_industri(): void
    {
        $student = User::factory()->create();
        $student->assignRole('Siswa');

        $response = $this->actingAs($student)
            ->withSession(['active_role' => 'Siswa'])
            ->get(route('hubin.industri.index'));

        $response->assertForbidden();
    }

    public function test_can_create_industri_kerjasama_with_mou_and_collaboration_types(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $user->assignRole('Hubin Sinergi UP dan Alumni');

        $file = UploadedFile::fake()->create('mou_telkom.pdf', 500, 'application/pdf');

        $postData = [
            'nama_industri' => 'PT Telkom Indonesia (Persero) Tbk',
            'bidang_usaha' => 'Teknologi Informasi & Rekayasa Perangkat Lunak',
            'website' => 'https://www.telkom.co.id',
            'alamat' => 'Jl. Pahlawan No. 10',
            'kota' => 'Bandar Lampung',
            'telepon' => '0721-123456',
            'nama_pic' => 'Ahmad PIC',
            'jabatan_pic' => 'HR Manager',
            'no_hp_pic' => '081234567890',
            'email_pic' => 'ahmad@telkom.co.id',
            'nomor_mou' => '001/MOU/TELKOM/2026',
            'tanggal_mou' => '2026-01-01',
            'tanggal_akhir_mou' => '2028-12-31',
            'is_mou_active' => '1',
            'bentuk_kerjasama' => [
                'Prakerin / PKL Siswa',
                'Penyaluran Lulusan & Alumni (BKK)',
                'Guru Tamu & Praktisi Mengajar',
            ],
            'catatan_mou' => 'Kerjasama PKL jurusan PPLG dan TJKT',
            'file_mou' => $file,
        ];

        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'Hubin Sinergi UP dan Alumni'])
            ->post(route('hubin.industri.store'), $postData);

        $response->assertRedirect(route('hubin.industri.index'));

        $this->assertDatabaseHas('prakerin_industris', [
            'nama_industri' => 'PT Telkom Indonesia (Persero) Tbk',
            'bidang_usaha' => 'Teknologi Informasi & Rekayasa Perangkat Lunak',
            'nama_pic' => 'Ahmad PIC',
            'nomor_mou' => '001/MOU/TELKOM/2026',
            'is_mou_active' => true,
        ]);

        $created = PrakerinIndustri::where('nama_industri', 'PT Telkom Indonesia (Persero) Tbk')->first();
        $this->assertNotNull($created->file_mou);
        Storage::disk('public')->assertExists($created->file_mou);
        $this->assertContains('Prakerin / PKL Siswa', $created->bentuk_kerjasama);
    }

    public function test_can_update_and_delete_industri_kerjasama(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $user->assignRole('Hubin Sinergi UP dan Alumni');

        $industri = PrakerinIndustri::create([
            'nama_industri' => 'PT Mitra Lama',
            'alamat' => 'Jl. Lama',
            'kota' => 'Metro',
            'is_mou_active' => true,
        ]);

        // Update
        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'Hubin Sinergi UP dan Alumni'])
            ->put(route('hubin.industri.update', $industri), [
                'nama_industri' => 'PT Mitra Baru Diperbarui',
                'alamat' => 'Jl. Baru No. 99',
                'kota' => 'Bandar Lampung',
                'bidang_usaha' => 'Jaringan Komputer & Telekomunikasi',
                'is_mou_active' => '1',
            ]);

        $response->assertRedirect(route('hubin.industri.index'));

        $this->assertDatabaseHas('prakerin_industris', [
            'id' => $industri->id,
            'nama_industri' => 'PT Mitra Baru Diperbarui',
            'kota' => 'Bandar Lampung',
        ]);

        // Delete
        $deleteResponse = $this->actingAs($user)
            ->withSession(['active_role' => 'Hubin Sinergi UP dan Alumni'])
            ->delete(route('hubin.industri.destroy', $industri));

        $deleteResponse->assertRedirect(route('hubin.industri.index'));
        $this->assertDatabaseMissing('prakerin_industris', ['id' => $industri->id]);
    }

    public function test_mou_download_works(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $user->assignRole('Hubin Sinergi UP dan Alumni');

        $file = UploadedFile::fake()->create('dokumen_mou.pdf', 300, 'application/pdf');
        $storedPath = $file->store('hubin/mou', 'public');

        $industri = PrakerinIndustri::create([
            'nama_industri' => 'PT Solusi Digital',
            'alamat' => 'Jl. Digital',
            'kota' => 'Bandar Lampung',
            'file_mou' => $storedPath,
            'is_mou_active' => true,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'Hubin Sinergi UP dan Alumni'])
            ->get(route('hubin.industri.download-mou', $industri));

        $response->assertOk();
    }

    public function test_navigation_renders_menu_for_hubin_and_super_admin(): void
    {
        $hubinUser = User::factory()->create();
        $hubinUser->assignRole('Hubin Sinergi UP dan Alumni');

        $hubinNav = $this->actingAs($hubinUser)
            ->withSession(['active_role' => 'Hubin Sinergi UP dan Alumni'])
            ->get(route('hubin.industri.index'));

        $hubinNav->assertSee('Industri Kerjasama');

        $adminUser = User::factory()->create();
        $adminUser->assignRole('Super Admin');

        $adminNav = $this->actingAs($adminUser)
            ->withSession(['active_role' => 'Super Admin'])
            ->get(route('hubin.industri.index'));

        $adminNav->assertSee('Industri Kerjasama');
    }
}
