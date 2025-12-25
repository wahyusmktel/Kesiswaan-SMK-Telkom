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
        Schema::table('siswa_pemutihans', function (Blueprint $table) {
            $table->enum('status', ['diajukan', 'disetujui', 'ditolak'])->default('disetujui')->after('keterangan');
            $table->foreignId('diajukan_oleh')->nullable()->constrained('users')->onDelete('set null')->after('status');
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->onDelete('set null')->after('diajukan_oleh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa_pemutihans', function (Blueprint $table) {
            $table->dropForeign(['diajukan_oleh']);
            $table->dropForeign(['disetujui_oleh']);
            $table->dropColumn(['status', 'diajukan_oleh', 'disetujui_oleh']);
        });
    }
};
