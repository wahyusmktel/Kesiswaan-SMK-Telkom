<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ukk_nilai_keterampilan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_siswa_id')->constrained('master_siswa')->onDelete('cascade');
            $table->foreignId('indikator_id')->constrained('ukk_instrumen_indikators')->onDelete('cascade');
            $table->foreignId('penguji_id')->constrained('users')->onDelete('cascade');
            $table->tinyInteger('nilai')->default(0)->comment('0=Belum, 1=Cukup, 2=Baik, 3=Sangat Baik');
            $table->timestamps();
            $table->unique(['master_siswa_id', 'indikator_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ukk_nilai_keterampilan');
    }
};
