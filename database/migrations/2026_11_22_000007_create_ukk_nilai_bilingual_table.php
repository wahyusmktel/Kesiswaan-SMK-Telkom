<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ukk_nilai_bilingual')) {
            Schema::create('ukk_nilai_bilingual', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ukk_ujian_id')->constrained('ukk_ujians')->onDelete('cascade');
                $table->foreignId('master_siswa_id')->constrained('master_siswa')->onDelete('cascade');
                $table->foreignId('penguji_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('fluency', 30)->nullable();
                $table->string('pronunciation', 30)->nullable();
                $table->string('vocabulary', 30)->nullable();
                $table->string('comprehension', 30)->nullable();
                $table->string('kesimpulan', 50)->nullable();
                $table->text('catatan')->nullable();
                $table->timestamps();

                $table->unique(['ukk_ujian_id', 'master_siswa_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ukk_nilai_bilingual');
    }
};
