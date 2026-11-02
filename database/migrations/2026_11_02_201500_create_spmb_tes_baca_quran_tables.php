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
        // 1. MASTER RUBRIK / KRITERIA INSTRUMEN TES BACA AL-QUR'AN
        Schema::create('spmb_tes_baca_quran_rubriks', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('nomor_urut')->default(1);
            $table->string('kategori')->default('penilaian'); // penilaian, rekapitulasi, media, catatan
            $table->string('aspek'); // TAJWID, MAKHARIJUL HURUF, KELANCARAN, etc.
            $table->text('petunjuk')->nullable();
            $table->string('tipe_input', 30)->default('radio'); // radio, select, file_upload, textarea
            $table->json('pilihan_opsi')->nullable(); // Array string opsi
            $table->json('skor_bobot')->nullable(); // Mapping opsi ke skor (misal SANGAT BAIK => 100)
            $table->boolean('is_required')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. SESI TES BACA AL-QUR'AN PER PESERTA SELEKSI
        Schema::create('spmb_tes_baca_quran_sesis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spmb_peserta_seleksi_id')->constrained('spmb_peserta_seleksis', indexName: 'fk_tbq_sesi_peserta')->onDelete('cascade');
            $table->foreignId('spmb_piket_tanggal_id')->nullable()->constrained('spmb_piket_tanggals', indexName: 'fk_tbq_sesi_tanggal')->nullOnDelete();
            $table->string('tahun_ajaran', 20)->default('2027/2028');
            $table->enum('status', ['Belum', 'Selesai'])->default('Belum');
            $table->string('nilai_tajwid')->nullable(); // SANGAT BAIK, BAIK, CUKUP, KURANG
            $table->string('nilai_makharijul_huruf')->nullable(); // SANGAT BAIK, BAIK, CUKUP, KURANG
            $table->string('nilai_kelancaran')->nullable(); // BAIK, CUKUP, KURANG
            $table->string('rekapitulasi_hasil')->nullable(); // SANGAT BAIK, BAIK, CUKUP, KURANG
            $table->decimal('skor_angka', 5, 2)->default(0); // 0.00 - 100.00
            $table->string('file_rekaman')->nullable(); // Path ke file audio/video rekaman
            $table->text('catatan')->nullable();
            $table->foreignId('penguji_user_id')->nullable()->constrained('users', indexName: 'fk_tbq_sesi_penguji')->nullOnDelete();
            $table->string('penguji_nama')->nullable();
            $table->timestamp('waktu_tes')->nullable();
            $table->timestamps();
        });

        // 3. DETAIL JAWABAN / SKOR PER RUBRIK
        Schema::create('spmb_tes_baca_quran_jawabans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spmb_tes_baca_quran_sesi_id')->constrained('spmb_tes_baca_quran_sesis', indexName: 'fk_tbq_jawab_sesi')->onDelete('cascade');
            $table->foreignId('spmb_tes_baca_quran_rubrik_id')->constrained('spmb_tes_baca_quran_rubriks', indexName: 'fk_tbq_jawab_rubrik')->onDelete('cascade');
            $table->string('jawaban_pilihan')->nullable();
            $table->text('jawaban_teks')->nullable();
            $table->string('file_path')->nullable();
            $table->decimal('skor_diperoleh', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spmb_tes_baca_quran_jawabans');
        Schema::dropIfExists('spmb_tes_baca_quran_sesis');
        Schema::dropIfExists('spmb_tes_baca_quran_rubriks');
    }
};
