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
        Schema::table('guru_izins', function (Blueprint $table) {
            $table->enum('kategori_penyetujuan', ['sekolah', 'luar', 'terlambat'])->default('luar')->change();
        });
    }

    public function down(): void
    {
        Schema::table('guru_izins', function (Blueprint $table) {
            $table->enum('kategori_penyetujuan', ['sekolah', 'luar'])->default('luar')->change();
        });
    }
};
