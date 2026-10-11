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
        Schema::create('teacher_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_guru_id')->nullable()->constrained('master_gurus')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_lengkap');
            $table->string('slug')->unique();
            $table->string('gelar_depan')->nullable();
            $table->string('gelar_belakang')->nullable();
            $table->string('nip')->nullable();
            $table->string('nuptk')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('bidang_studi')->nullable();
            $table->string('kategori')->default('guru_produktif');
            $table->text('bio')->nullable();
            $table->text('filosofi_mengajar')->nullable();
            $table->string('foto')->nullable();
            $table->json('keahlian')->nullable();
            $table->json('riwayat_pendidikan')->nullable();
            $table->json('sertifikasi')->nullable();
            $table->json('sosial_media')->nullable();
            $table->boolean('is_published')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamps();

            $table->index(['is_published', 'kategori']);
            $table->index('is_featured');
        });

        Schema::create('teacher_portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_profile_id')->constrained('teacher_profiles')->cascadeOnDelete();
            $table->string('judul');
            $table->string('slug')->nullable();
            $table->string('kategori')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('gambar')->nullable();
            $table->string('tautan_proyek')->nullable();
            $table->string('tahun', 10)->nullable();
            $table->json('teknologi')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['teacher_profile_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_portfolios');
        Schema::dropIfExists('teacher_profiles');
    }
};
