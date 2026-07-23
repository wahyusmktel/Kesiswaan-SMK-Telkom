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
        Schema::create('whatsapp_devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone_number')->nullable();
            $table->string('session_id')->unique();
            $table->string('provider')->default('fonnte'); // fonnte, wablas, node_baileys, custom_http
            $table->string('server_url')->nullable();
            $table->text('api_key')->nullable();
            $table->enum('status', ['disconnected', 'connecting', 'qr_ready', 'connected'])->default('disconnected');
            $table->longText('qr_code_data')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamp('last_connected_at')->nullable();
            $table->string('webhook_url')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('whatsapp_device_id')->nullable()->constrained('whatsapp_devices')->onDelete('cascade');
            $table->string('recipient');
            $table->string('recipient_name')->nullable();
            $table->text('message');
            $table->string('type')->default('general'); // test, absensi, perizinan, fingerprint, general
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->json('response_data')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->string('event_key')->unique(); // e.g., absensi_alpha, perizinan_disetujui, fingerprint_terlambat, panggilan_siswa
            $table->string('title');
            $table->string('category')->default('presensi'); // presensi, perizinan, kedisiplinan, umum
            $table->boolean('is_enabled')->default(true);
            $table->text('template_text');
            $table->json('variables')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_templates');
        Schema::dropIfExists('whatsapp_logs');
        Schema::dropIfExists('whatsapp_devices');
    }
};
