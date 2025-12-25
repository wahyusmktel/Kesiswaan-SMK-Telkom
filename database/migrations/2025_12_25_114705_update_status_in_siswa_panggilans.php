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
        Schema::table('siswa_panggilans', function (Blueprint $table) {
            $table->enum('status', ['diajukan', 'disetujui', 'ditolak', 'terkirim', 'hadir', 'tidak_hadir'])
                ->default('diajukan')
                ->change();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users');
            $table->text('catatan_waka')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa_panggilans', function (Blueprint $table) {
            $table->enum('status', ['terkirim', 'hadir', 'tidak_hadir'])->default('terkirim')->change();
            $table->dropForeign(['disetujui_oleh']);
            $table->dropColumn(['disetujui_oleh', 'catatan_waka']);
        });
    }
};
