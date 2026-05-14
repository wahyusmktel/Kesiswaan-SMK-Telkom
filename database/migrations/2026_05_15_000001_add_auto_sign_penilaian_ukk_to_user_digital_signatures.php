<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_digital_signatures', function (Blueprint $table) {
            $table->boolean('auto_sign_penilaian_ukk')->default(false)->after('auto_sign_izin_guru');
        });
    }

    public function down(): void
    {
        Schema::table('user_digital_signatures', function (Blueprint $table) {
            $table->dropColumn('auto_sign_penilaian_ukk');
        });
    }
};
