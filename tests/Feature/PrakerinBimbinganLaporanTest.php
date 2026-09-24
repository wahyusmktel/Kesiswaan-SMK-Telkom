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

        // Cek halaman siswa menampilkan status Proses Persetujuan dan tombol disabled
        $indexResponse = $this->actingAs($this->userSiswa)
            ->withSession(['active_role' => 'Siswa'])
            ->get(route('siswa.bimbingan-laporan.index'));

        $indexResponse->assertOk();
        $indexResponse->assertSee('Proses Persetujuan');
        $indexResponse->assertSee('Judul Sedang Ditinjau Pembimbing');
        $indexResponse->assertDontSee('Laporan PKL');

        // Pengajuan ulang saat status masih 'diajukan' tidak diperbolehkan
        $reResponse = $this->actingAs($this->userSiswa)
            ->withSession(['active_role' => 'Siswa'])
            ->post(route('siswa.bimbingan-laporan.ajukan-judul'), [
                'judul' => 'Judul Baru Yang Mau Ditimpa',
            ]);

        $reResponse->assertRedirect();
        $this->assertDatabaseMissing('prakerin_bimbingan_laporans', [
            'judul_laporan' => 'Judul Baru Yang Mau Ditimpa',
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

    public function test_approved_stage_cannot_be_revised_or_reuploaded(): void
    {
        UserDigitalSignature::create([
            'user_id' => $this->userGuru->id,
            'pin_hash' => Hash::make('123456'),
            'is_active' => true,
        ]);

        $laporan = PrakerinBimbinganLaporan::create([
            'prakerin_penempatan_id' => $this->penempatan->id,
            'status_judul' => 'disetujui',
            'status_laporan' => 'dalam_bimbingan',
        ]);

        $tahap = $laporan->tahaps()->create([
            'judul_tahap' => 'Bab I Pendahuluan',
            'urutan' => 1,
            'status' => 'disetujui',
            'file_pdf_path' => 'public/prakerin_laporan/dummy.pdf',
        ]);

        // 1. Pembimbing mencoba merevisi tahap yang sudah disetujui -> Ditolak / Dilarang
        $responseReview = $this->actingAs($this->userGuru)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->post(route('pembimbing-prakerin.bimbingan-laporan.selesaikan-review', $tahap), [
                'status' => 'revisi',
                'catatan_pembimbing' => 'Mencoba revisi setelah ACC',
                'pin' => '123456',
            ]);

        $responseReview->assertRedirect();
        $tahap->refresh();
        $this->assertEquals('disetujui', $tahap->status);

        // 2. Pembimbing mencoba menambah anotasi di tahap yang sudah disetujui -> 422 JSON
        $responseAnotasi = $this->actingAs($this->userGuru)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->postJson(route('pembimbing-prakerin.bimbingan-laporan.simpan-anotasi', $tahap), [
                'halaman' => 1,
                'posisi_x' => 10,
                'posisi_y' => 10,
                'tipe_anotasi' => 'sorot_kotak',
            ]);

        $responseAnotasi->assertStatus(422);

        // 3. Siswa mencoba mengunggah ulang dokumen di tahap yang sudah disetujui -> Ditolak
        $fakePdf = \Illuminate\Http\UploadedFile::fake()->create('revisi.pdf', 500, 'application/pdf');
        $responseUpload = $this->actingAs($this->userSiswa)
            ->withSession(['active_role' => 'Siswa'])
            ->post(route('siswa.bimbingan-laporan.upload-dokumen', $tahap), [
                'file_pdf' => $fakePdf,
            ]);

        $responseUpload->assertRedirect();
        $tahap->refresh();
        $this->assertEquals('disetujui', $tahap->status);
    }

    public function test_prakerin_digital_signature_verification_url(): void
    {
        $laporan = PrakerinBimbinganLaporan::create([
            'prakerin_penempatan_id' => $this->penempatan->id,
            'status_judul' => 'disetujui',
            'status_laporan' => 'dalam_bimbingan',
        ]);

        $tahap = $laporan->tahaps()->create([
            'judul_tahap' => 'Bab I Pendahuluan',
            'urutan' => 1,
            'status' => 'disetujui',
            'nomor_berita_acara' => 'BA-ACC/20260924/0001',
            'file_pdf_path' => 'public/dummy.pdf',
        ]);

        $beritaAcara = $tahap->beritaAcaras()->create([
            'prakerin_bimbingan_laporan_id' => $laporan->id,
            'user_id' => $this->userGuru->id,
            'nomor_berita_acara' => 'BA-ACC/20260924/0001',
            'jenis' => 'disetujui',
            'judul_tahap' => $tahap->judul_tahap,
            'total_anotasi' => 0,
            'diterbitkan_at' => now(),
        ]);

        // Cek akses verifikasi via route bimbingan-prakerin payload
        $payload = [
            'doc' => 'BERITA_ACARA_ACC_BIMBINGAN_PRAKERIN',
            'nomor' => $beritaAcara->nomor_berita_acara,
            'siswa' => $this->masterSiswa->nama_lengkap,
            'bab' => $tahap->judul_tahap,
            'status' => 'disetujui',
            'tanggal' => now()->format('d-m-Y H:i'),
        ];

        $verifyUrl = route('verifikasi.bimbingan-prakerin', base64_encode(json_encode($payload)));
        $response = $this->get($verifyUrl);

        $response->assertOk();
        $response->assertSee('DOKUMEN SAH');
        $response->assertSee('BERITA_ACARA_PRAKERIN');
    }
}
