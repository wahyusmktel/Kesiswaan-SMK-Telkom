<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fingerprint_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fingerprint_device_id')->constrained('fingerprint_devices')->cascadeOnDelete();
            $table->unsignedInteger('uid')->nullable();
            $table->string('user_id');
            $table->foreignId('app_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('role')->nullable();
            $table->string('password')->nullable();
            $table->string('cardno')->nullable();
            $table->timestamps();

            $table->unique(['fingerprint_device_id', 'user_id']);
            $table->index(['name', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fingerprint_users');
    }
};
