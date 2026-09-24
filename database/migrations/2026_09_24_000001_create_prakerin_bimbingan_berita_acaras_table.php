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
        Schema::dropIfExists('prakerin_bimbingan_berita_acaras');

        Schema::create('prakerin_bimbingan_berita_acaras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prakerin_bimbingan_tahap_id')
                ->constrained('prakerin_bimbingan_tahaps', indexName: 'fk_ba_tahap_id')
                ->cascadeOnDelete();
            $table->foreignId('prakerin_bimbingan_laporan_id')
                ->constrained('prakerin_bimbingan_laporans', indexName: 'fk_ba_laporan_id')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users', indexName: 'fk_ba_user_id')
                ->nullOnDelete(); // Guru pembimbing yang menerbitkan
            $table->string('nomor_berita_acara');
            $table->enum('jenis', ['revisi', 'disetujui'])->default('revisi');
            $table->unsignedInteger('revisi_ke')->nullable(); // 1, 2, dst jika revisi
            $table->string('judul_tahap');
            $table->text('catatan_pembimbing')->nullable();
            $table->unsignedInteger('total_anotasi')->default(0);
            $table->string('file_pdf_path')->nullable();
            $table->timestamp('diterbitkan_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prakerin_bimbingan_berita_acaras');
    }
};
