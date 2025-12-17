<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tahun_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->string('tahun', 9); // Contoh: 2024/2025
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->boolean('is_active')->default(false); // Penanda tahun aktif
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tahun_pelajaran');
    }
};
