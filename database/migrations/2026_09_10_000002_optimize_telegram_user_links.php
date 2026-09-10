<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('telegram_user_links', function (Blueprint $table) {
            $table->string('commands_hash', 64)->nullable()->after('last_interaction_at');
            $table->index(['telegram_bot_id', 'telegram_user_id'], 'telegram_links_bot_telegram_user_idx');
        });
    }

    public function down(): void
    {
        Schema::table('telegram_user_links', function (Blueprint $table) {
            $table->dropIndex('telegram_links_bot_telegram_user_idx');
            $table->dropColumn('commands_hash');
        });
    }
};
