<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->boolean('system_announcement_enabled')->default(true)->after('landing_popup_frequency');
            $table->string('system_announcement_version', 50)->default('v1')->after('system_announcement_enabled');
            $table->string('system_announcement_title', 120)->nullable()->after('system_announcement_version');
            $table->string('system_announcement_subtitle', 255)->nullable()->after('system_announcement_title');
        });
    }

    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn([
                'system_announcement_enabled',
                'system_announcement_version',
                'system_announcement_title',
                'system_announcement_subtitle',
            ]);
        });
    }
};
