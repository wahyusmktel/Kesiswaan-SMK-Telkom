<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('beritas', function (Blueprint $table) {
            $table->string('seo_title')->nullable()->after('ringkasan');
            $table->string('seo_description', 320)->nullable()->after('seo_title');
            $table->string('focus_keyword')->nullable()->after('seo_description');
            $table->text('seo_keywords')->nullable()->after('focus_keyword');
        });
    }

    public function down(): void
    {
        Schema::table('beritas', function (Blueprint $table) {
            $table->dropColumn([
                'seo_title',
                'seo_description',
                'focus_keyword',
                'seo_keywords',
            ]);
        });
    }
};
