<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->string('stella_ai_base_url')->nullable();
            $table->text('stella_ai_api_key')->nullable();
            $table->string('stella_ai_chat_model')->nullable();
            $table->string('stella_ai_image_model')->nullable();
            $table->boolean('stella_ai_enabled')->default(false);
        });

        Schema::create('stella_ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title')->default('Percakapan Baru');
            $table->timestamps();
            $table->index(['user_id', 'updated_at']);
        });

        Schema::create('stella_ai_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('stella_ai_conversations')->cascadeOnDelete();
            $table->enum('role', ['user', 'assistant']);
            $table->text('content');
            $table->text('image_path')->nullable();
            $table->enum('type', ['text', 'image_request', 'image_response'])->default('text');
            $table->timestamps();
            $table->index(['conversation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stella_ai_messages');
        Schema::dropIfExists('stella_ai_conversations');

        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn([
                'stella_ai_base_url',
                'stella_ai_api_key',
                'stella_ai_chat_model',
                'stella_ai_image_model',
                'stella_ai_enabled',
            ]);
        });
    }
};
