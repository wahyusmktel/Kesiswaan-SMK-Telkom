<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tu_letter_requests', function (Blueprint $table) {
            $table->string('file_path')->after('subject')->nullable();
            $table->enum('type', ['upload', 'create'])->after('file_path')->default('upload');
            $table->longText('content')->after('type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tu_letter_requests', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'type', 'content']);
        });
    }
};
