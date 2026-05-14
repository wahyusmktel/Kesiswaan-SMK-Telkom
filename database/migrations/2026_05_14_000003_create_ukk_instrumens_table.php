<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ukk_instrumens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ukk_ujian_id')->constrained('ukk_ujians')->onDelete('cascade');
            $table->string('nama_instrumen');
            $table->unsignedTinyInteger('bobot_pengetahuan')->default(30); // 0–100 %
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ukk_instrumens');
    }
};
