<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ukk_nilai_pengetahuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_siswa_id')->constrained('master_siswa')->onDelete('cascade');
            $table->foreignId('soal_id')->constrained('ukk_instrumen_soals')->onDelete('cascade');
            $table->foreignId('penguji_id')->constrained('users')->onDelete('cascade');
            $table->tinyInteger('nilai')->default(0)->comment('0=salah, 1=benar');
            $table->timestamps();
            $table->unique(['master_siswa_id', 'soal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ukk_nilai_pengetahuan');
    }
};
