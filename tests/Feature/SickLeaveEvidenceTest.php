<?php

namespace Tests\Feature;

use App\Models\GuruIzin;
use App\Models\MasterGuru;
use App\Models\TelegramBot;
use App\Models\TelegramUser;
use App\Models\User;
use App\Services\TelegramTeacherLeaveService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SickLeaveEvidenceTest extends TestCase
{
    use RefreshDatabase;

    private function setupTeacher(): array
    {
        Role::firstOrCreate(['name' => 'Guru Kelas']);
        Role::firstOrCreate(['name' => 'KAUR SDM']);

        $user = User::factory()->create();
        $user->assignRole('Guru Kelas');

        $teacher = MasterGuru::create([
            'nama_lengkap' => 'Budi Santoso',
            'nip' => '198701012015011001',
            'jenis_kelamin' => 'L',
            'user_id' => $user->id,
        ]);
        $teacher->dapodikGuru()->create([
            'nama' => $teacher->nama_lengkap,
            'status_kepegawaian' => 'Pegawai Full Time',
        ]);

        return [$user, $teacher];
    }

    public function test_mild_sick_leave_allows_single_day_without_evidence(): void
    {
        Storage::fake('public');
        [$user, $teacher] = $this->setupTeacher();

        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->post(route('guru.izin.store'), [
                'tanggal_mulai' => '2026-10-10 07:00:00',
                'tanggal_selesai' => '2026-10-10 16:00:00',
                'jenis_izin' => 'Sakit',
                'tipe_sakit' => 'ringan',
                'kategori_penyetujuan' => 'tidak_masuk',
                'deskripsi' => 'Demam ringan dan flu.',
            ]);

        $response->assertRedirect(route('guru.izin.index'));
        $this->assertDatabaseHas('guru_izins', [
            'master_guru_id' => $teacher->id,
            'jenis_izin' => 'Sakit',
            'tipe_sakit' => 'ringan',
            'dokumen_pdf' => null,
        ]);
    }

    public function test_mild_sick_leave_fails_if_more_than_one_day(): void
    {
        [$user, $teacher] = $this->setupTeacher();

        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->from(route('guru.izin.create'))
            ->post(route('guru.izin.store'), [
                'tanggal_mulai' => '2026-10-10 07:00:00',
                'tanggal_selesai' => '2026-10-12 16:00:00',
                'jenis_izin' => 'Sakit',
                'tipe_sakit' => 'ringan',
                'kategori_penyetujuan' => 'tidak_masuk',
                'deskripsi' => 'Demam ringan.',
            ]);

        $response->assertSessionHasErrors(['tanggal_selesai']);
    }

    public function test_doctor_note_sick_leave_requires_evidence_and_locks_to_three_days(): void
    {
        Storage::fake('public');
        [$user, $teacher] = $this->setupTeacher();

        // Fails without evidence
        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->from(route('guru.izin.create'))
            ->post(route('guru.izin.store'), [
                'tanggal_mulai' => '2026-10-10 07:00:00',
                'tanggal_selesai' => '2026-10-12 16:00:00',
                'jenis_izin' => 'Sakit',
                'tipe_sakit' => 'surat_dokter',
                'kategori_penyetujuan' => 'tidak_masuk',
                'deskripsi' => 'Istirahat sesuai surat dokter.',
            ]);

        $response->assertSessionHasErrors(['dokumen_eviden']);

        // Succeeds with PDF evidence and auto calculates 3 days
        $file = UploadedFile::fake()->create('surat_dokter.pdf', 200, 'application/pdf');

        $response2 = $this->actingAs($user)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->post(route('guru.izin.store'), [
                'tanggal_mulai' => '2026-10-10 07:00:00',
                'tanggal_selesai' => '2026-10-10 16:00:00', // Submitted 1 day, backend forces lock 3 days (10 to 12)
                'jenis_izin' => 'Sakit',
                'tipe_sakit' => 'surat_dokter',
                'dokumen_eviden' => $file,
                'kategori_penyetujuan' => 'tidak_masuk',
                'deskripsi' => 'Istirahat sesuai surat dokter.',
            ]);

        $response2->assertRedirect(route('guru.izin.index'));

        $izin = GuruIzin::where('master_guru_id', $teacher->id)->first();
        $this->assertNotNull($izin);
        $this->assertEquals('surat_dokter', $izin->tipe_sakit);
        $this->assertNotNull($izin->dokumen_pdf);
        $this->assertTrue($izin->isEvidenPdf());
        $this->assertEquals('2026-10-12 16:00:00', Carbon::parse($izin->tanggal_selesai)->format('Y-m-d H:i:s'));
    }

    public function test_hospitalization_sick_leave_requires_evidence_and_accepts_image(): void
    {
        Storage::fake('public');
        [$user, $teacher] = $this->setupTeacher();

        $image = UploadedFile::fake()->image('surat_opname.jpg');

        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->post(route('guru.izin.store'), [
                'tanggal_mulai' => '2026-10-10 07:00:00',
                'tanggal_selesai' => '2026-10-15 16:00:00',
                'jenis_izin' => 'Sakit',
                'tipe_sakit' => 'rawat_inap',
                'dokumen_eviden' => $image,
                'kategori_penyetujuan' => 'tidak_masuk',
                'deskripsi' => 'Rawat inap demam berdarah di RS.',
            ]);

        $response->assertRedirect(route('guru.izin.index'));

        $izin = GuruIzin::where('master_guru_id', $teacher->id)->first();
        $this->assertNotNull($izin);
        $this->assertEquals('rawat_inap', $izin->tipe_sakit);
        $this->assertTrue($izin->isEvidenImage());
        $this->assertFalse($izin->isEvidenPdf());
    }
}
