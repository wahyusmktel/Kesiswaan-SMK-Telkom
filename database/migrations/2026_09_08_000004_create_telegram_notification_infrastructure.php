<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_bots', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('purpose')->index();
            $table->string('bot_username')->nullable();
            $table->text('bot_token');
            $table->string('webhook_secret');
            $table->string('status')->default('not_configured');
            $table->text('last_error')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('telegram_user_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('telegram_bot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('chat_id');
            $table->string('telegram_user_id')->nullable();
            $table->string('telegram_username')->nullable();
            $table->string('telegram_name')->nullable();
            $table->timestamp('linked_at');
            $table->timestamp('last_interaction_at')->nullable();
            $table->timestamps();
            $table->unique(['telegram_bot_id', 'user_id']);
            $table->unique(['telegram_bot_id', 'chat_id']);
        });

        Schema::create('telegram_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('telegram_bot_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('recipient_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('chat_id');
            $table->string('recipient_name')->nullable();
            $table->text('message');
            $table->string('type')->default('general');
            $table->string('event_key')->nullable()->index();
            $table->date('notification_date')->nullable()->index();
            $table->string('status')->default('pending');
            $table->text('error_message')->nullable();
            $table->json('response_data')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::table('fingerprint_auto_sync_settings', function (Blueprint $table) {
            $table->string('notification_channel')->default('whatsapp');
            $table->foreignId('telegram_bot_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('fingerprint_auto_sync_settings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('telegram_bot_id');
            $table->dropColumn('notification_channel');
        });
        Schema::dropIfExists('telegram_logs');
        Schema::dropIfExists('telegram_user_links');
        Schema::dropIfExists('telegram_bots');
    }
};
