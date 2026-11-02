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
        // 1. KATEGORI / MATA UJI TES POTENSI AKADEMIK (TPA)
        Schema::create('spmb_tpa_kategoris', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique(); // BINDO, MTK, BING, TIK
            $table->string('nama'); // Bahasa Indonesia, Matematika, Bahasa Inggris, Dasar TIK
            $table->text('deskripsi')->nullable();
            $table->unsignedInteger('jumlah_soal_ditampilkan')->default(10);
            $table->unsignedInteger('durasi_menit')->default(15);
            $table->unsignedInteger('passing_grade')->default(65);
            $table->unsignedInteger('nomor_urut')->default(1);
            $table->string('warna_tema', 30)->default('red'); // red, blue, purple, emerald
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. BANK SOAL TES POTENSI AKADEMIK (TPA)
        Schema::create('spmb_tpa_soals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spmb_tpa_kategori_id')->constrained('spmb_tpa_kategoris', indexName: 'fk_tpa_soal_kat')->onDelete('cascade');
            $table->unsignedInteger('nomor_urut')->default(1);
            $table->text('pertanyaan');
            $table->string('gambar')->nullable(); // Ilustrasi/rumus/grafik
            $table->string('tipe_soal', 30)->default('pilihan_ganda'); // pilihan_ganda
            $table->text('opsi_a')->nullable();
            $table->text('opsi_b')->nullable();
            $table->text('opsi_c')->nullable();
            $table->text('opsi_d')->nullable();
            $table->text('opsi_e')->nullable();
            $table->string('kunci_jawaban', 10); // A, B, C, D, E
            $table->text('pembahasan')->nullable();
            $table->enum('tingkat_kesulitan', ['Mudah', 'Sedang', 'Sulit'])->default('Sedang');
            $table->unsignedInteger('bobot')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. SESI UJIAN TPA PER PESERTA SELEKSI
        Schema::create('spmb_tpa_sesis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spmb_peserta_seleksi_id')->constrained('spmb_peserta_seleksis', indexName: 'fk_tpa_sesi_peserta')->onDelete('cascade');
            $table->foreignId('spmb_piket_tanggal_id')->nullable()->constrained('spmb_piket_tanggals', indexName: 'fk_tpa_sesi_tanggal')->nullOnDelete();
            $table->string('tahun_ajaran', 20)->default('2027/2028');
            $table->enum('status', ['Belum', 'Sedang Mengerjakan', 'Selesai'])->default('Belum');
            $table->timestamp('waktu_mulai')->nullable();
            $table->timestamp('waktu_selesai')->nullable();
            $table->unsignedInteger('total_soal')->default(40);
            $table->unsignedInteger('total_benar')->default(0);
            $table->unsignedInteger('total_salah')->default(0);
            $table->unsignedInteger('total_kosong')->default(0);
            $table->decimal('skor_bindo', 5, 2)->default(0);
            $table->decimal('skor_mtk', 5, 2)->default(0);
            $table->decimal('skor_bing', 5, 2)->default(0);
            $table->decimal('skor_tik', 5, 2)->default(0);
            $table->decimal('skor_total', 5, 2)->default(0);
            $table->enum('hasil_evaluasi', ['Lulus', 'Dipertimbangkan', 'Tidak Lulus', 'Belum Diuji'])->default('Belum Diuji');
            $table->text('catatan')->nullable();
            $table->foreignId('pengawas_user_id')->nullable()->constrained('users', indexName: 'fk_tpa_sesi_pengawas')->nullOnDelete();
            $table->string('pengawas_nama')->nullable();
            $table->timestamps();
        });

        // 4. DETAIL JAWABAN PESERTA PER SOAL TPA
        Schema::create('spmb_tpa_jawabans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spmb_tpa_sesi_id')->constrained('spmb_tpa_sesis', indexName: 'fk_tpa_jawab_sesi')->onDelete('cascade');
            $table->foreignId('spmb_tpa_soal_id')->constrained('spmb_tpa_soals', indexName: 'fk_tpa_jawab_soal')->onDelete('cascade');
            $table->string('jawaban_peserta', 10)->nullable(); // A, B, C, D, E
            $table->boolean('is_correct')->default(false);
            $table->decimal('skor_diperoleh', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spmb_tpa_jawabans');
        Schema::dropIfExists('spmb_tpa_sesis');
        Schema::dropIfExists('spmb_tpa_soals');
        Schema::dropIfExists('spmb_tpa_kategoris');
    }
};
