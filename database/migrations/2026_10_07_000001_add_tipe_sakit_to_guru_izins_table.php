<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guru_izins', function (Blueprint $table) {
            $table->string('tipe_sakit')->nullable()->after('jenis_izin');
        });
    }

    public function down(): void
    {
        Schema::table('guru_izins', function (Blueprint $table) {
            $table->dropColumn('tipe_sakit');
        });
    }
};
