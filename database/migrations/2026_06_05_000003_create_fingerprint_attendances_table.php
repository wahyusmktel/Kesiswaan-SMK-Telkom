<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fingerprint_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fingerprint_device_id')->constrained('fingerprint_devices')->cascadeOnDelete();
            $table->unsignedInteger('uid')->nullable();
            $table->string('user_id');
            $table->foreignId('app_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('timestamp');
            $table->string('status')->nullable();
            $table->string('punch')->nullable();
            $table->timestamps();

            $table->unique(['fingerprint_device_id', 'user_id', 'timestamp'], 'fp_att_unique');
            $table->index(['timestamp', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fingerprint_attendances');
    }
};
