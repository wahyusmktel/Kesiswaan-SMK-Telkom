<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswa_kelulusans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengumuman_kelulusan_id')->constrained('pengumuman_kelulusans')->cascadeOnDelete();
            $table->foreignId('master_siswa_id')->constrained('master_siswa')->cascadeOnDelete();
            $table->enum('status', ['lulus', 'tidak_lulus'])->default('lulus');
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->unique(['pengumuman_kelulusan_id', 'master_siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa_kelulusans');
    }
};
