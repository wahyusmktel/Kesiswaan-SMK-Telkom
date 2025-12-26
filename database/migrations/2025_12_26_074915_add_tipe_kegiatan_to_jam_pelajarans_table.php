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
        Schema::table('jam_pelajarans', function (Blueprint $table) {
            $table->string('tipe_kegiatan')->nullable()->after('jam_selesai')->comment('istirahat, sholawat_pagi, upacara, ishoma, kegiatan_4r');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jam_pelajarans', function (Blueprint $table) {
            $table->dropColumn('tipe_kegiatan');
        });
    }
};
