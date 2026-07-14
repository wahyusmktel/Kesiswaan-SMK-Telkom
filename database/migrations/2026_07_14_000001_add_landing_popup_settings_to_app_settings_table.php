<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->boolean('landing_popup_enabled')->default(true);
            $table->string('landing_popup_type', 30)->default('registration');
            $table->string('landing_popup_title')->default('Selamat Datang, Calon Siswa Baru!');
            $table->text('landing_popup_description')->nullable();
            $table->string('landing_popup_cta_text')->default('Registrasi Sekarang');
            $table->string('landing_popup_cta_url')->default('/registrasi-siswa-baru');
            $table->string('landing_popup_frequency', 30)->default('daily');
        });
    }

    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn([
                'landing_popup_enabled',
                'landing_popup_type',
                'landing_popup_title',
                'landing_popup_description',
                'landing_popup_cta_text',
                'landing_popup_cta_url',
                'landing_popup_frequency',
            ]);
        });
    }
};
