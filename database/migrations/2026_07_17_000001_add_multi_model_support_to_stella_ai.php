<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->json('stella_ai_models')->nullable()->after('stella_ai_chat_model');
        });

        Schema::table('stella_ai_conversations', function (Blueprint $table) {
            $table->string('model')->nullable()->after('title');
            $table->index(['user_id', 'model']);
        });
    }

    public function down(): void
    {
        Schema::table('stella_ai_conversations', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'model']);
            $table->dropColumn('model');
        });

        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn('stella_ai_models');
        });
    }
};
