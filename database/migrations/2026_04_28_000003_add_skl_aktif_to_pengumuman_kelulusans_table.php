<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengumuman_kelulusans', function (Blueprint $table) {
            $table->boolean('skl_aktif')->default(false)->after('waktu_publikasi');
        });
    }

    public function down(): void
    {
        Schema::table('pengumuman_kelulusans', function (Blueprint $table) {
            $table->dropColumn('skl_aktif');
        });
    }
};
