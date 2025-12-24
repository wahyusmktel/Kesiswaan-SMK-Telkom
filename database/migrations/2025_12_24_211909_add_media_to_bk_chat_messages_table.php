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
        Schema::table('bk_chat_messages', function (Blueprint $table) {
            $table->string('type')->default('text')->after('message')->comment('text, image, video, file');
            $table->string('file_path')->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bk_chat_messages', function (Blueprint $table) {
            $table->dropColumn(['type', 'file_path']);
        });
    }
};
