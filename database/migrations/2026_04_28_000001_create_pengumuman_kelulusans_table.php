<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumuman_kelulusans', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('keterangan')->nullable();
            $table->dateTime('waktu_publikasi');
            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajaran')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumuman_kelulusans');
    }
};
