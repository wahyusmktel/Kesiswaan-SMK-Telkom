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
        Schema::create('smart_tv_media', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->enum('tipe', ['qr_hadir', 'qr_pulang', 'mading', 'pengumuman', 'lainnya'])->default('mading');
            $table->string('file_path');
            $table->enum('file_type', ['pdf', 'image'])->default('image');
            $table->time('jam_mulai')->default('06:00:00');
            $table->time('jam_selesai')->default('08:00:00');
            $table->json('hari')->nullable(); // null = setiap hari, atau ["Senin","Selasa",...]
            $table->enum('mode_tayang', ['eksklusif', 'kombinasi'])->default('kombinasi'); // eksklusif = takeover layar penuh tanpa jadwal; kombinasi = disisipkan ke slideshow jadwal
            $table->unsignedInteger('durasi_detik')->default(20); // durasi tampil di slide kombinasi
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smart_tv_media');
    }
};
