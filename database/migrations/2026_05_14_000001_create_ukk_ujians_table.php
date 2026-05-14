<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ukk_ujians', function (Blueprint $table) {
            $table->id();
            $table->string('nama_ujian');
            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajaran')->onDelete('cascade');
            $table->string('jurusan');
            $table->string('nama_project')->nullable();
            $table->date('tanggal_pelaksanaan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ukk_ujians');
    }
};
