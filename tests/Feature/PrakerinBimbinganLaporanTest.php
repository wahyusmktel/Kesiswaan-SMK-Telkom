<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\MasterGuru;
use App\Models\MasterSiswa;
use App\Models\PrakerinBimbinganAktivitasLog;
use App\Models\PrakerinBimbinganAnotasi;
use App\Models\PrakerinBimbinganLaporan;
use App\Models\PrakerinBimbinganTahap;
use App\Models\PrakerinIndustri;
use App\Models\PrakerinPembimbing;
use App\Models\PrakerinPenempatan;
use App\Models\PrakerinRombel;
use App\Models\Rombel;
use App\Models\User;
use App\Models\UserDigitalSignature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PrakerinBimbinganLaporanTest extends TestCase
{
    use RefreshDatabase;

    protected User $userSiswa;
    protected MasterSiswa $masterSiswa;
    protected User $userGuru;
    protected MasterGuru $masterGuru;
    protected PrakerinPenempatan $penempatan;
    protected PrakerinRombel $rombelPkl;
    protected PrakerinIndustri $industri;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        Role::firstOrCreate(['name' => 'Super Admin']);
        Role::firstOrCreate(['name' => 'Siswa']);
        Role::firstOrCreate(['name' => 'Guru Kelas']);
        Role::firstOrCreate(['name' => 'Wali Kelas']);
        Role::firstOrCreate(['name' => 'Hubin Sinergi UP dan Alumni']);
        Role::firstOrCreate(['name' => 'Kepala Sekolah']);
        Role::firstOrCreate(['name' => 'Kurikulum']);
        Role::firstOrCreate(['name' => 'Operator']);

        // 1. Buat Guru Pembimbing
        $this->userGuru = User::factory()->create(['name' => 'Guru Pembimbing Internal']);
        $this->userGuru->assignRole('Guru Kelas');
        $this->masterGuru = MasterGuru::create([
            'user_id' => $this->userGuru->id,
            'nama_lengkap' => 'Guru Pembimbing Internal',
            'jenis_kelamin' => 'L',
            'is_active' => true,
        ]);

        $pembimbingInternal = PrakerinPembimbing::create([
            'tipe' => 'internal',
            'master_guru_id' => $this->masterGuru->id,
            'nama' => $this->masterGuru->nama_lengkap,
            'is_active' => true,
        ]);

        // 2. Buat Industri Mitra & Rombel PKL
        $this->industri = PrakerinIndustri::create([
            'nama_industri' => 'PT Telkom Indonesia Tbk',
            'alamat' => 'Bandar Lampung',
            'kota' => 'Bandar Lampung',
            'nama_pic' => 'Budi Santoso',
            'is_active' => true,
        ]);

        $this->rombelPkl = PrakerinRombel::create([
            'nama_rombel' => 'Rombel PKL Telkom Group',
            'prakerin_industri_id' => $this->industri->id,
            'pembimbing_internal_id' => $pembimbingInternal->id,
            'status' => 'aktif',
        ]);

        // 3. Buat Siswa & Penempatan
        $this->userSiswa = User::factory()->create(['name' => 'Siswa Prakerin Test']);
        $this->userSiswa->assignRole('Siswa');
        $this->masterSiswa = MasterSiswa::create([
            'user_id' => $this->userSiswa->id,
            'nama_lengkap' => 'Siswa Prakerin Test',
            'nis' => '553241091',
            'jenis_kelamin' => 'L',
        ]);

        $this->penempatan = PrakerinPenempatan::create([
            'master_siswa_id' => $this->masterSiswa->id,
            'prakerin_rombel_id' => $this->rombelPkl->id,
            'prakerin_industri_id' => $this->industri->id,
            'master_guru_id' => $this->masterGuru->id,
            'nama_pembimbing_industri' => 'Bapak Joko (Industri)',
            'status' => 'aktif',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonths(3)->toDateString(),
        ]);
    }

    public function test_siswa_can_access_bimbingan_index_and_creates_default_tahaps(): void
    {
        $response = $this->actingAs($this->userSiswa)
            ->withSession(['active_role' => 'Siswa'])
            ->get(route('siswa.bimbingan-laporan.index'));

        $response->assertOk();
        $response->assertSee('Bimbingan Laporan Prakerin');
        $response->assertSee('Tahap 1: Pengajuan Judul Laporan Prakerin');

        // Pastikan record bimbingan laporan dan template tahap default terbuat
        $this->assertDatabaseHas('prakerin_bimbingan_laporans', [
            'prakerin_penempatan_id' => $this->penempatan->id,
        ]);

        $laporan = PrakerinBimbinganLaporan::where('prakerin_penempatan_id', $this->penempatan->id)->first();
        $this->assertNotNull($laporan);
        $this->assertGreaterThanOrEqual(5, $laporan->tahaps()->count());
    }

    public function test_siswa_can_ajukan_judul_and_records_activity_log(): void
    {
        $this->actingAs($this->userSiswa)->get(route('siswa.bimbingan-laporan.index'));

        $response = $this->actingAs($this->userSiswa)
            ->withSession(['active_role' => 'Siswa'])
            ->post(route('siswa.bimbingan-laporan.ajukan-judul'), [
                'judul' => 'Rancang Bangun Jaringan FTTH pada Kawasan Industri',
                'abstrak_rencana' => 'Membahas instalasi Optical Distribution Point (ODP) dan pengujian OTDR.',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('prakerin_bimbingan_laporans', [
            'prakerin_penempatan_id' => $this->penempatan->id,
            'judul_laporan' => 'Rancang Bangun Jaringan FTTH pada Kawasan Industri',
            'status_judul' => 'diajukan',
        ]);

        $this->assertDatabaseHas('prakerin_bimbingan_aktivitas_logs', [
            'aksi' => 'ajukan_judul',
            'user_id' => $this->userSiswa->id,
        ]);
    }

    public function test_pembimbing_can_approve_and_reject_judul_with_mandatory_notes(): void
    {
        // Siswa ajukan judul
        $laporan = PrakerinBimbinganLaporan::create([
            'prakerin_penempatan_id' => $this->penempatan->id,
            'judul_laporan' => 'Sistem Pemantauan Server',
            'status_judul' => 'diajukan',
        ]);

        // 1. Uji penolakan tanpa catatan alasan (harus error validasi)
        $responseTolakGagal = $this->actingAs($this->userGuru)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->post(route('pembimbing-prakerin.bimbingan-laporan.review-judul', $laporan), [
                'action' => 'tolak',
                'catatan' => '', // kosong
            ]);
        $responseTolakGagal->assertSessionHasErrors(['catatan_judul']);

        // 2. Uji penolakan dengan catatan (harus sukses)
        $responseTolakSukses = $this->actingAs($this->userGuru)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->post(route('pembimbing-prakerin.bimbingan-laporan.review-judul', $laporan), [
                'action' => 'tolak',
                'catatan' => 'Judul terlalu umum, jelaskan lokasi implementasinya.',
            ]);
        $responseTolakSukses->assertRedirect();
        $this->assertDatabaseHas('prakerin_bimbingan_laporans', [
            'id' => $laporan->id,
            'status_judul' => 'ditolak',
            'catatan_judul' => 'Judul terlalu umum, jelaskan lokasi implementasinya.',
        ]);

        // 3. Uji persetujuan judul
        $responseSetuju = $this->actingAs($this->userGuru)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->post(route('pembimbing-prakerin.bimbingan-laporan.review-judul', $laporan), [
                'action' => 'setuju',
                'catatan' => 'Judul disetujui, silakan lanjutkan penyusunan Bab 1.',
            ]);
        $responseSetuju->assertRedirect();
        $this->assertDatabaseHas('prakerin_bimbingan_laporans', [
            'id' => $laporan->id,
            'status_judul' => 'disetujui',
        ]);
    }

    public function test_siswa_cannot_upload_document_before_title_is_approved(): void
    {
        $laporan = PrakerinBimbinganLaporan::create([
            'prakerin_penempatan_id' => $this->penempatan->id,
            'status_judul' => 'diajukan', // Belum disetujui
        ]);
        $tahap = $laporan->tahaps()->create([
            'judul_tahap' => 'BAB I: Pendahuluan',
            'urutan' => 1,
            'status' => 'belum_mulai',
        ]);

        $file = UploadedFile::fake()->create('laporan.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($this->userSiswa)
            ->withSession(['active_role' => 'Siswa'])
            ->post(route('siswa.bimbingan-laporan.upload-dokumen', $tahap), [
                'file_dokumen' => $file,
            ]);

        $response->assertRedirect();
        // File tidak boleh tersimpan
        $tahap->refresh();
        $this->assertNull($tahap->file_pdf_path);
    }

    public function test_siswa_can_upload_pdf_after_title_approved(): void
    {
        $laporan = PrakerinBimbinganLaporan::create([
            'prakerin_penempatan_id' => $this->penempatan->id,
            'status_judul' => 'disetujui',
        ]);
        $tahap = $laporan->tahaps()->create([
            'judul_tahap' => 'BAB I: Pendahuluan',
            'urutan' => 1,
            'status' => 'belum_mulai',
        ]);

        $file = UploadedFile::fake()->create('bab1_pendahuluan.pdf', 2048, 'application/pdf');

        $response = $this->actingAs($this->userSiswa)
            ->withSession(['active_role' => 'Siswa'])
            ->post(route('siswa.bimbingan-laporan.upload-dokumen', $tahap), [
                'file_dokumen' => $file,
                'catatan_siswa' => 'Mohon koreksi latar belakang dan batasan masalah.',
            ]);

        $response->assertRedirect();

        $tahap->refresh();
        $this->assertNotNull($tahap->file_pdf_path);
        $this->assertEquals('diajukan', $tahap->status);
        $this->assertEquals('bab1_pendahuluan.pdf', $tahap->file_pdf_nama_asli);
    }

    public function test_pembimbing_requires_digital_signature_to_access_annotator(): void
    {
        $laporan = PrakerinBimbinganLaporan::create([
            'prakerin_penempatan_id' => $this->penempatan->id,
            'status_judul' => 'disetujui',
        ]);
        $tahap = $laporan->tahaps()->create([
            'judul_tahap' => 'BAB I: Pendahuluan',
            'urutan' => 1,
            'status' => 'diajukan',
            'file_pdf_path' => 'public/dummy.pdf',
        ]);
        Storage::disk('public')->put('dummy.pdf', '%PDF-1.4 dummy content');

        // Belum setup TTD digital
        $response = $this->actingAs($this->userGuru)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->get(route('pembimbing-prakerin.bimbingan-laporan.annotator', $tahap));

        // Harus diarahkan ke halaman setup tanda tangan
        $response->assertRedirect(route('tanda-tangan.index'));

        // Sekarang setup TTD digital
        UserDigitalSignature::create([
            'user_id' => $this->userGuru->id,
            'pin_hash' => Hash::make('123456'),
            'is_active' => true,
        ]);

        $responseReady = $this->actingAs($this->userGuru)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->get(route('pembimbing-prakerin.bimbingan-laporan.annotator', $tahap));

        $responseReady->assertOk();
        $responseReady->assertSee('Studio Koreksi & Coretan');
    }

    public function test_pembimbing_can_save_and_delete_annotations(): void
    {
        $laporan = PrakerinBimbinganLaporan::create([
            'prakerin_penempatan_id' => $this->penempatan->id,
            'status_judul' => 'disetujui',
        ]);
        $tahap = $laporan->tahaps()->create([
            'judul_tahap' => 'BAB I: Pendahuluan',
            'urutan' => 1,
            'status' => 'diajukan',
            'file_pdf_path' => 'public/dummy.pdf',
        ]);

        // Simpan Anotasi Box
        $response = $this->actingAs($this->userGuru)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->postJson(route('pembimbing-prakerin.bimbingan-laporan.simpan-anotasi', $tahap), [
                'halaman' => 1,
                'koordinat_x' => 150,
                'koordinat_y' => 200,
                'lebar' => 120,
                'tinggi' => 60,
                'tipe' => 'box',
                'warna' => '#ef4444',
                'catatan' => 'Perbaiki penulisan kalimat efektif pada paragraf 2.',
            ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('prakerin_bimbingan_anotasis', [
            'prakerin_bimbingan_tahap_id' => $tahap->id,
            'halaman' => 1,
            'tipe_anotasi' => 'sorot_kotak',
            'catatan' => 'Perbaiki penulisan kalimat efektif pada paragraf 2.',
        ]);

        $anotasi = PrakerinBimbinganAnotasi::first();

        // Hapus anotasi (Soft Delete)
        $delResponse = $this->actingAs($this->userGuru)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->deleteJson(route('pembimbing-prakerin.bimbingan-laporan.hapus-anotasi', $anotasi));

        $delResponse->assertOk();
        $this->assertSoftDeleted('prakerin_bimbingan_anotasis', ['id' => $anotasi->id]);
    }

    public function test_pembimbing_can_complete_review_and_generate_berita_acara(): void
    {
        UserDigitalSignature::create([
            'user_id' => $this->userGuru->id,
            'pin_hash' => Hash::make('123456'),
            'is_active' => true,
        ]);

        $laporan = PrakerinBimbinganLaporan::create([
            'prakerin_penempatan_id' => $this->penempatan->id,
            'status_judul' => 'disetujui',
        ]);
        $tahap = $laporan->tahaps()->create([
            'judul_tahap' => 'BAB I: Pendahuluan',
            'urutan' => 1,
            'status' => 'diajukan',
            'file_pdf_path' => 'public/dummy.pdf',
        ]);

        $response = $this->actingAs($this->userGuru)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->post(route('pembimbing-prakerin.bimbingan-laporan.selesaikan-review', $tahap), [
                'status' => 'disetujui',
                'catatan_pembimbing' => 'Penulisan Bab 1 sudah lengkap dan sesuai panduan.',
                'pin' => '123456',
            ]);

        $response->assertRedirect(route('pembimbing-prakerin.bimbingan-laporan.detail', $laporan));

        $tahap->refresh();
        $this->assertEquals('disetujui', $tahap->status);
        $this->assertNotNull($tahap->nomor_berita_acara);
        $this->assertNotNull($tahap->berita_acara_at);
        $this->assertNotNull($tahap->disetujui_at);
    }

    public function test_acc_final_process_and_completion(): void
    {
        UserDigitalSignature::create([
            'user_id' => $this->userGuru->id,
            'pin_hash' => Hash::make('123456'),
            'is_active' => true,
        ]);

        $laporan = PrakerinBimbinganLaporan::create([
            'prakerin_penempatan_id' => $this->penempatan->id,
            'status_judul' => 'disetujui',
            'status_laporan' => 'siap_acc',
        ]);

        // Buat 1 tahap dan disetujui semua
        $laporan->tahaps()->create([
            'judul_tahap' => 'Laporan Lengkap',
            'urutan' => 1,
            'status' => 'disetujui',
            'file_pdf_path' => 'public/dummy.pdf',
        ]);

        $response = $this->actingAs($this->userGuru)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->post(route('pembimbing-prakerin.bimbingan-laporan.acc-final', $laporan), [
                'catatan_acc' => 'Laporan Prakerin resmi di-ACC tuntas.',
                'pin' => '123456',
            ]);

        $response->assertRedirect();

        $laporan->refresh();
        $this->assertEquals('selesai_acc', $laporan->status_laporan);
        $this->assertNotNull($laporan->acc_at);
        $this->assertEquals($this->userGuru->id, $laporan->acc_by);
    }

    public function test_monitoring_access_and_wali_kelas_restriction(): void
    {
        // Buat 2 kelas dan 2 siswa di kelas berbeda
        $kelasA = Kelas::create(['nama_kelas' => 'XII RPL 1', 'jurusan' => 'Rekayasa Perangkat Lunak']);
        $kelasB = Kelas::create(['nama_kelas' => 'XII TKJ 1', 'jurusan' => 'Teknik Komputer dan Jaringan']);

        $rombelA = Rombel::create([
            'tahun_ajaran' => '2025/2026',
            'kelas_id' => $kelasA->id,
            'wali_kelas_id' => $this->userGuru->id, // Guru ini adalah wali kelas A
        ]);

        $guruLain = User::factory()->create();
        $rombelB = Rombel::create([
            'tahun_ajaran' => '2025/2026',
            'kelas_id' => $kelasB->id,
            'wali_kelas_id' => $guruLain->id,
        ]);

        // Siswa A di Rombel A
        $this->masterSiswa->rombels()->attach($rombelA->id);

        // Siswa B di Rombel B
        $userSiswaB = User::factory()->create();
        $siswaB = MasterSiswa::create([
            'user_id' => $userSiswaB->id,
            'nama_lengkap' => 'Siswa Kelas Lain TKJ',
            'nis' => '999888111',
            'jenis_kelamin' => 'P',
        ]);
        $siswaB->rombels()->attach($rombelB->id);

        PrakerinPenempatan::create([
            'master_siswa_id' => $siswaB->id,
            'prakerin_rombel_id' => $this->rombelPkl->id,
            'prakerin_industri_id' => $this->industri->id,
            'master_guru_id' => $this->masterGuru->id,
            'nama_pembimbing_industri' => 'Bapak Joko (Industri)',
            'status' => 'aktif',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonths(3)->toDateString(),
        ]);

        // 1. Wali Kelas mengakses monitoring: Hanya boleh melihat Siswa A (kelas binaannya)
        $this->userGuru->assignRole('Wali Kelas');
        $responseWali = $this->actingAs($this->userGuru)
            ->withSession(['active_role' => 'Wali Kelas'])
            ->get(route('monitoring.bimbingan-laporan.index'));

        $responseWali->assertOk();
        $responseWali->assertSee($this->masterSiswa->nama_lengkap);
        $responseWali->assertDontSee('Siswa Kelas Lain TKJ');

        // 2. Hubin Sinergi UP dan Alumni mengakses monitoring: Boleh melihat seluruh siswa
        $userHubin = User::factory()->create();
        $userHubin->assignRole('Hubin Sinergi UP dan Alumni');

        $responseHubin = $this->actingAs($userHubin)
            ->withSession(['active_role' => 'Hubin Sinergi UP dan Alumni'])
            ->get(route('monitoring.bimbingan-laporan.index'));

        $responseHubin->assertOk();
        $responseHubin->assertSee($this->masterSiswa->nama_lengkap);
        $responseHubin->assertSee('Siswa Kelas Lain TKJ');
    }
}
