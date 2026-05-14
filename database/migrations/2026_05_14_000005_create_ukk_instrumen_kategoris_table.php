<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ukk_instrumen_kategoris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instrumen_id')->constrained('ukk_instrumens')->onDelete('cascade');
            $table->string('nama_kategori');
            $table->unsignedTinyInteger('bobot')->default(0); // % dalam keterampilan, total harus 100
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ukk_instrumen_kategoris');
    }
};
