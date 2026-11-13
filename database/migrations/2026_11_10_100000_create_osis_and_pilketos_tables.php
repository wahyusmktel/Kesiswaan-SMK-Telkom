<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Pengaturan & Profil Umum OSIS
        Schema::create('osis_settings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_organisasi')->default('OSIS SMK Telkom Lampung');
            $table->string('semboyan')->default('Kreatif, Inovatif, Berkarakter & Berintegritas');
            $table->text('deskripsi')->nullable();
            $table->text('visi')->nullable();
            $table->text('misi')->nullable();
            $table->string('logo')->nullable();
            $table->string('foto_struktur')->nullable();
            
            // Profil Ketua & Wakil Existing
            $table->string('nama_ketua_existing')->nullable();
            $table->string('foto_ketua_existing')->nullable();
            $table->string('nama_wakil_existing')->nullable();
            $table->string('foto_wakil_existing')->nullable();
            $table->text('visi_misi_ketua_existing')->nullable();
            $table->text('pesan_ketua_existing')->nullable();
            $table->string('periode_aktif')->default('2025/2026');
            
            // Sosial Media
            $table->string('instagram')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('youtube')->nullable();
            $table->string('email')->nullable();
            
            $table->timestamps();
        });

        // 2. Daftar Pengurus OSIS Existing
        Schema::create('osis_penguruses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_siswa_id')->nullable()->constrained('master_siswa')->nullOnDelete();
            $table->string('nama');
            $table->string('jabatan');
            $table->string('sekbid')->nullable();
            $table->string('kelas')->nullable();
            $table->string('foto')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Agenda & Kegiatan OSIS
        Schema::create('osis_agendas', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('foto')->nullable();
            $table->string('kategori')->default('Program Kerja');
            $table->string('status')->default('akan_datang'); // akan_datang, berlangsung, selesai
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // 4. Periode Pemilihan Ketua OSIS (Pilketos)
        Schema::create('pilketos_periodes', function (Blueprint $table) {
            $table->id();
            $table->string('nama_periode');
            $table->string('tahun_pelajaran')->default('2026/2027');
            $table->text('deskripsi')->nullable();
            $table->string('status')->default('draft'); // draft, aktif, voting_berlangsung, selesai
            $table->dateTime('tgl_mulai_voting')->nullable();
            $table->dateTime('tgl_selesai_voting')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Timeline / Jadwal Tahapan Pemilihan
        Schema::create('pilketos_timelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pilketos_periode_id')->constrained('pilketos_periodes')->cascadeOnDelete();
            $table->string('judul_tahap');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('status_tahap')->default('akan_datang'); // selesai, berlangsung, akan_datang
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        // 6. Kandidat / Calon Ketua & Wakil Ketua OSIS
        Schema::create('pilketos_kandidats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pilketos_periode_id')->constrained('pilketos_periodes')->cascadeOnDelete();
            $table->integer('nomor_urut');
            
            // Calon Ketua (data siswa)
            $table->foreignId('ketua_siswa_id')->nullable()->constrained('master_siswa')->nullOnDelete();
            $table->string('nama_ketua');
            $table->string('nis_ketua')->nullable();
            $table->string('kelas_ketua')->nullable();
            $table->string('foto_ketua')->nullable();
            
            // Calon Wakil (data siswa)
            $table->foreignId('wakil_siswa_id')->nullable()->constrained('master_siswa')->nullOnDelete();
            $table->string('nama_wakil')->nullable();
            $table->string('nis_wakil')->nullable();
            $table->string('kelas_wakil')->nullable();
            $table->string('foto_wakil')->nullable();
            
            // Informasi Paslon
            $table->string('foto_paslon')->nullable();
            $table->string('tagline')->nullable();
            $table->text('visi');
            $table->text('misi');
            $table->text('program_unggulan')->nullable();
            $table->string('video_url')->nullable();
            $table->timestamps();
        });

        // 7. Daftar Pemilih Tetap (DPT) & Barcode Akses Bilik
        Schema::create('pilketos_dpts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pilketos_periode_id')->constrained('pilketos_periodes')->cascadeOnDelete();
            $table->foreignId('master_siswa_id')->nullable()->constrained('master_siswa')->nullOnDelete();
            $table->string('nama_pemilih');
            $table->string('nis')->index();
            $table->string('nisn')->nullable();
            $table->string('kelas')->index();
            $table->string('jenis_kelamin')->nullable();
            
            // Barcode unik per pemilih untuk scanner fisik (Code 128)
            $table->string('barcode_token')->unique();
            $table->string('status_memilih')->default('belum_memilih'); // belum_memilih, sedang_memilih, sudah_memilih
            $table->dateTime('waktu_memilih')->nullable();
            $table->string('bilik_id')->nullable();
            $table->timestamps();
        });

        // 8. Suara Masuk (Anonymous Ballot)
        Schema::create('pilketos_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pilketos_periode_id')->constrained('pilketos_periodes')->cascadeOnDelete();
            $table->foreignId('pilketos_kandidat_id')->constrained('pilketos_kandidats')->cascadeOnDelete();
            $table->string('bilik_id')->nullable();
            $table->dateTime('voted_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pilketos_votes');
        Schema::dropIfExists('pilketos_dpts');
        Schema::dropIfExists('pilketos_kandidats');
        Schema::dropIfExists('pilketos_timelines');
        Schema::dropIfExists('pilketos_periodes');
        Schema::dropIfExists('osis_agendas');
        Schema::dropIfExists('osis_penguruses');
        Schema::dropIfExists('osis_settings');
    }
};
