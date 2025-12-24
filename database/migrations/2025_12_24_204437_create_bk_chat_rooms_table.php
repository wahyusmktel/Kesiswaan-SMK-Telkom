<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bk_chat_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('guru_bk_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->unique(['siswa_user_id', 'guru_bk_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bk_chat_rooms');
    }
};
