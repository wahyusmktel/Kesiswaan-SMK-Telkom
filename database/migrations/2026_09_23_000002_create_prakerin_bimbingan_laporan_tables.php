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
        // 1. Tabel Utama Bimbingan Laporan Prakerin Siswa
        if (! Schema::hasTable('prakerin_bimbingan_laporans')) {
            Schema::create('prakerin_bimbingan_laporans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('prakerin_penempatan_id')->constrained('prakerin_penempatans')->cascadeOnDelete();
                $table->string('judul_laporan')->nullable();
                $table->text('deskripsi_judul')->nullable();
                $table->enum('status_judul', ['draft', 'diajukan', 'disetujui', 'ditolak'])->default('draft');
                $table->text('catatan_judul')->nullable(); // Alasan jika ditolak / catatan pembimbing
                $table->timestamp('judul_diajukan_at')->nullable();
                $table->timestamp('judul_reviewed_at')->nullable();

                // Status keseluruhan laporan
                $table->enum('status_laporan', ['draft', 'dalam_bimbingan', 'siap_acc', 'selesai_acc'])->default('draft');
                $table->text('catatan_acc')->nullable();
                $table->timestamp('acc_at')->nullable();
                $table->foreignId('acc_by')->nullable()->constrained('users')->nullOnDelete();

                $table->softDeletes();
                $table->timestamps();
            });
        }

        // 2. Tabel Tahapan / Bab Bimbingan yang Dinamis
        if (! Schema::hasTable('prakerin_bimbingan_tahaps')) {
            Schema::create('prakerin_bimbingan_tahaps', function (Blueprint $table) {
                $table->id();
                $table->foreignId('prakerin_bimbingan_laporan_id')->constrained('prakerin_bimbingan_laporans')->cascadeOnDelete();
                $table->string('judul_tahap'); // e.g. "Bagian Depan & Pengesahan", "BAB I: Pendahuluan", dll
                $table->text('deskripsi')->nullable();
                $table->unsignedInteger('urutan')->default(1);
                $table->enum('status', ['belum_mulai', 'draft', 'diajukan', 'perlu_revisi', 'disetujui'])->default('belum_mulai');

                // Berkas Dokumen Laporan (Wajib PDF)
                $table->string('file_pdf_path')->nullable();
                $table->string('file_pdf_nama_asli')->nullable();
                $table->unsignedBigInteger('file_pdf_size')->nullable();
                $table->text('catatan_siswa')->nullable();
                $table->timestamp('diajukan_at')->nullable();

                // Review Pembimbing
                $table->text('catatan_pembimbing')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('disetujui_at')->nullable();

                // Berita Acara Bimbingan
                $table->string('nomor_berita_acara')->nullable();
                $table->timestamp('berita_acara_at')->nullable();

                $table->softDeletes();
                $table->timestamps();
            });
        }

        // 3. Tabel Anotasi Coretan & Komentar Interaktif pada PDF
        if (! Schema::hasTable('prakerin_bimbingan_anotasis')) {
            Schema::create('prakerin_bimbingan_anotasis', function (Blueprint $table) {
                $table->id();
                $table->foreignId('prakerin_bimbingan_tahap_id')->constrained('prakerin_bimbingan_tahaps')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Guru pembimbing yang membuat coretan
                $table->unsignedInteger('halaman')->default(1);
                $table->double('posisi_x', 8, 2); // Persentase X 0 - 100%
                $table->double('posisi_y', 8, 2); // Persentase Y 0 - 100%
                $table->double('lebar', 8, 2)->nullable();
                $table->double('tinggi', 8, 2)->nullable();
                $table->enum('tipe_anotasi', ['sorot_kotak', 'sorot_lingkaran', 'coretan_bebas', 'pin_catatan'])->default('sorot_kotak');
                $table->string('warna', 30)->default('#ef4444'); // Default merah Tailwind
                $table->text('catatan'); // Catatan revisi untuk area ini
                $table->json('drawing_data')->nullable(); // Koordinat stroke coretan bebas

                $table->softDeletes();
                $table->timestamps();
            });
        }

        // 4. Tabel Audit Trail / Log Aktivitas Bimbingan
        if (! Schema::hasTable('prakerin_bimbingan_aktivitas_logs')) {
            Schema::create('prakerin_bimbingan_aktivitas_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('prakerin_bimbingan_laporan_id')->constrained('prakerin_bimbingan_laporans')->cascadeOnDelete();
                $table->foreignId('prakerin_bimbingan_tahap_id')->nullable()->constrained('prakerin_bimbingan_tahaps')->nullOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('aksi'); // e.g. 'ajukan_judul', 'setujui_judul', 'tolak_judul', 'tambah_bab', dll
                $table->text('keterangan');
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();

                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prakerin_bimbingan_aktivitas_logs');
        Schema::dropIfExists('prakerin_bimbingan_anotasis');
        Schema::dropIfExists('prakerin_bimbingan_tahaps');
        Schema::dropIfExists('prakerin_bimbingan_laporans');
    }
};
