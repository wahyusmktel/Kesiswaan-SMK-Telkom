<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_gurus', function (Blueprint $table) {
            $table->string('nik', 20)->nullable()->unique()->after('kode_guru');
        });
    }

    public function down(): void
    {
        Schema::table('master_gurus', function (Blueprint $table) {
            $table->dropColumn('nik');
        });
    }
};
