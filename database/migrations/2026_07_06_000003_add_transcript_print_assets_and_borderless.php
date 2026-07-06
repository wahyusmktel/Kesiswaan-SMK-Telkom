<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transcript_configs', function (Blueprint $table) {
            $table->string('watermark_path')->nullable()->after('letterhead_path');
            $table->boolean('is_borderless')->default(false)->after('paper_size');
        });
    }

    public function down(): void
    {
        Schema::table('transcript_configs', function (Blueprint $table) {
            $table->dropColumn(['watermark_path', 'is_borderless']);
        });
    }
};
