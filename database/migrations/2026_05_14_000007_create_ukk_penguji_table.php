<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ukk_penguji', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ukk_ujian_id')->constrained('ukk_ujians')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['ukk_ujian_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ukk_penguji');
    }
};
