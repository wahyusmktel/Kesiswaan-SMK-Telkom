<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notted_uno_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('code', 12)->unique();
            $table->foreignId('host_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('guest_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('winner_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 20)->default('waiting');
            $table->json('state')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'last_activity_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notted_uno_rooms');
    }
};
