<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transcript_configs', function (Blueprint $table) {
            $table->boolean('manual_signature_enabled')->default(false)->after('is_borderless');
            $table->string('manual_signature_path')->nullable()->after('manual_signature_enabled');
            $table->decimal('manual_signature_x', 5, 2)->default(54)->after('manual_signature_path');
            $table->decimal('manual_signature_y', 5, 2)->default(74)->after('manual_signature_x');
            $table->decimal('manual_signature_width', 5, 2)->default(43)->after('manual_signature_y');
            $table->string('scan_color_mode', 20)->default('color')->after('manual_signature_width');
        });
    }

    public function down(): void
    {
        Schema::table('transcript_configs', function (Blueprint $table) {
            $table->dropColumn([
                'manual_signature_enabled',
                'manual_signature_path',
                'manual_signature_x',
                'manual_signature_y',
                'manual_signature_width',
                'scan_color_mode',
            ]);
        });
    }
};
