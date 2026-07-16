<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->string('stella_ai_image_endpoint', 500)->nullable()->after('stella_ai_image_model');
            $table->text('stella_ai_image_api_key')->nullable()->after('stella_ai_image_endpoint');
        });
    }

    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn([
                'stella_ai_image_endpoint',
                'stella_ai_image_api_key',
            ]);
        });
    }
};
