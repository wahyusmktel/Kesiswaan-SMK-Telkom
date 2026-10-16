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
        Schema::table('role_menu_settings', function (Blueprint $table) {
            $table->string('custom_label')->nullable()->after('parent_key');
            $table->string('custom_section')->nullable()->after('custom_label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_menu_settings', function (Blueprint $table) {
            $table->dropColumn(['custom_label', 'custom_section']);
        });
    }
};
