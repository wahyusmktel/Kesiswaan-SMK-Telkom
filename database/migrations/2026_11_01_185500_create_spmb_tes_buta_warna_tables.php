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
        // 1. MASTER SOAL INSTRUMEN TES BUTA WARNA
        Schema::create('spmb_tes_buta_warna_soals', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('nomor_urut')->default(1);
            $table->string('judul')->default('Soal nomor');
            $table->text('petunjuk')->nullable();
            $table->string('gambar'); // Path to image (e.g. images/soal-tes-buta-warna/1.png or storage/...)
            $table->json('pilihan_opsi'); // Array 3 pilihan (e.g. ["2", "8", "Tidak Terlihat Angka"])
            $table->string('kunci_jawaban'); // Jawaban benar (e.g. "2")
            $table->unsignedInteger('bobot')->default(10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. SESI TES BUTA WARNA PER PESERTA SELEKSI
        Schema::create('spmb_tes_buta_warna_sesis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spmb_peserta_seleksi_id')->constrained('spmb_peserta_seleksis')->onDelete('cascade');
            $table->foreignId('spmb_piket_tanggal_id')->nullable()->constrained('spmb_piket_tanggals')->nullOnDelete();
            $table->string('tahun_ajaran', 20)->default('2027/2028');
            $table->enum('status', ['Belum', 'Sedang Mengerjakan', 'Selesai'])->default('Belum');
            $table->timestamp('waktu_mulai')->nullable();
            $table->timestamp('waktu_selesai')->nullable();
            $table->unsignedInteger('total_soal')->default(10);
            $table->unsignedInteger('jawaban_benar')->default(0);
            $table->unsignedInteger('jawaban_salah')->default(0);
            $table->decimal('skor', 5, 2)->default(0); // 0.00 - 100.00
            $table->enum('hasil_evaluasi', ['Normal', 'Buta Warna Parsial', 'Buta Warna Total', 'Belum Diuji'])->default('Belum Diuji');
            $table->text('catatan_petugas')->nullable();
            $table->foreignId('penguji_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('penguji_nama')->nullable();
            $table->timestamps();
        });

        // 3. DETAIL JAWABAN PESERTA PER SOAL TES BUTA WARNA
        Schema::create('spmb_tes_buta_warna_jawabans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spmb_tes_buta_warna_sesi_id')->constrained('spmb_tes_buta_warna_sesis')->onDelete('cascade');
            $table->foreignId('spmb_tes_buta_warna_soal_id')->constrained('spmb_tes_buta_warna_soals')->onDelete('cascade');
            $table->string('jawaban_peserta')->nullable();
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
        Schema::dropIfExists('spmb_tes_buta_warna_jawabans');
        Schema::dropIfExists('spmb_tes_buta_warna_sesis');
        Schema::dropIfExists('spmb_tes_buta_warna_soals');
    }
};
