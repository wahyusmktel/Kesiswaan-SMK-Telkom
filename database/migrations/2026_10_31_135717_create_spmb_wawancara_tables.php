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
        // 1. Master Pertanyaan Instrumen Wawancara (Siswa & Ortu)
        Schema::create('spmb_wawancara_pertanyaans', function (Blueprint $table) {
            $table->id();
            $table->enum('kategori', ['siswa', 'orang_tua'])->index();
            $table->integer('nomor_urut')->default(1);
            $table->string('field_key', 50)->nullable()->index(); // e.g. no_pendaftaran, nama_lengkap, asal_sekolah
            $table->text('pertanyaan');
            $table->text('petunjuk')->nullable();
            $table->string('tipe_input', 30); // text, textarea, radio, checkbox, scale_1_5, file_upload, date, info
            $table->json('pilihan_opsi')->nullable(); // array of options
            $table->boolean('has_other')->default(false); // allows free text for "Other / Lainnya"
            $table->boolean('is_required')->default(true);
            $table->boolean('is_readonly')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('bobot')->default(1);
            $table->json('skor_opsi')->nullable(); // map option to score points
            $table->timestamps();
        });

        // 2. Sesi Wawancara Calon Peserta
        Schema::create('spmb_wawancara_sesis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spmb_peserta_seleksi_id')->constrained('spmb_peserta_seleksis')->cascadeOnDelete();
            $table->foreignId('spmb_piket_tanggal_id')->nullable()->constrained('spmb_piket_tanggals')->nullOnDelete();
            $table->string('tahun_ajaran', 20)->default('2027/2028')->index();

            // Sesi Siswa
            $table->foreignId('pewawancara_siswa_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('pewawancara_siswa_nama', 150)->nullable();
            $table->dateTime('waktu_wawancara_siswa')->nullable();
            $table->enum('status_wawancara_siswa', ['Belum', 'Sedang Wawancara', 'Selesai'])->default('Belum');
            $table->decimal('skor_siswa', 5, 2)->nullable();
            $table->enum('hasil_wawancara_siswa', ['Sangat Baik', 'Baik', 'Cukup', 'Kurang Baik'])->nullable();
            $table->text('catatan_pewawancara_siswa')->nullable();
            $table->string('foto_siswa')->nullable();

            // Sesi Orang Tua
            $table->foreignId('pewawancara_ortu_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('pewawancara_ortu_nama', 150)->nullable();
            $table->dateTime('waktu_wawancara_ortu')->nullable();
            $table->enum('status_wawancara_ortu', ['Belum', 'Sedang Wawancara', 'Selesai'])->default('Belum');
            $table->decimal('skor_ortu', 5, 2)->nullable();
            $table->enum('hasil_wawancara_ortu', ['Sangat Baik', 'Baik', 'Cukup', 'Kurang Baik'])->nullable();
            $table->text('catatan_pewawancara_ortu')->nullable();
            $table->string('foto_ortu')->nullable();

            // Rekomendasi Akhir
            $table->enum('rekomendasi_akhir', ['Diterima', 'Diterima Bersyarat', 'Dipertimbangkan', 'Ditolak', 'Belum Diputuskan'])->default('Belum Diputuskan');
            $table->text('catatan_akhir')->nullable();

            $table->timestamps();
        });

        // 3. Detail Jawaban Per Pertanyaan
        Schema::create('spmb_wawancara_jawabans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spmb_wawancara_sesi_id')->constrained('spmb_wawancara_sesis')->cascadeOnDelete();
            $table->foreignId('spmb_wawancara_pertanyaan_id')->constrained('spmb_wawancara_pertanyaans')->cascadeOnDelete();
            $table->enum('kategori', ['siswa', 'orang_tua'])->index();
            $table->longText('jawaban_teks')->nullable();
            $table->json('jawaban_pilihan')->nullable();
            $table->text('jawaban_other')->nullable();
            $table->tinyInteger('jawaban_skala')->nullable();
            $table->string('file_path')->nullable();
            $table->decimal('skor_diperoleh', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['spmb_wawancara_sesi_id', 'spmb_wawancara_pertanyaan_id'], 'sesi_pertanyaan_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spmb_wawancara_jawabans');
        Schema::dropIfExists('spmb_wawancara_sesis');
        Schema::dropIfExists('spmb_wawancara_pertanyaans');
    }
};
