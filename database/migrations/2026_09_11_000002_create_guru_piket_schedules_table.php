<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guru_piket_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('weekday', 10);
            $table->unsignedTinyInteger('slot');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['weekday', 'slot']);
            $table->unique(['weekday', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru_piket_schedules');
    }
};
