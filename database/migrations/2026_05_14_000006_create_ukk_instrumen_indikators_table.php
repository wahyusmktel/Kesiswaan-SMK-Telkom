<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ukk_instrumen_indikators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('ukk_instrumen_kategoris')->onDelete('cascade');
            $table->text('nama_indikator');
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ukk_instrumen_indikators');
    }
};
