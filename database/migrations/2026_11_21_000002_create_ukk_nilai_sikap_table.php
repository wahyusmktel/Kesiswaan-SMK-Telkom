<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ukk_nilai_sikap')) {
            Schema::create('ukk_nilai_sikap', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ukk_ujian_id')->constrained('ukk_ujians')->onDelete('cascade');
                $table->foreignId('master_siswa_id')->constrained('master_siswa')->onDelete('cascade');
                $table->foreignId('penguji_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('kerapian', 30)->nullable();
                $table->string('ketelitian', 30)->nullable();
                $table->string('kedisiplinan', 30)->nullable();
                $table->string('tanggung_jawab', 30)->nullable();
                $table->string('inisiatif', 30)->nullable();
                $table->string('kesimpulan', 50)->nullable();
                $table->text('catatan')->nullable();
                $table->timestamps();

                $table->unique(['ukk_ujian_id', 'master_siswa_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ukk_nilai_sikap');
    }
};
