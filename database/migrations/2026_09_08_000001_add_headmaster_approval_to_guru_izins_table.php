<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guru_izins', function (Blueprint $table) {
            $table->string('status_kepala_sekolah')->default('tidak_diperlukan')->after('status_sdm');
            $table->foreignId('kepala_sekolah_id')->nullable()->after('sdm_id')->constrained('users')->nullOnDelete();
            $table->timestamp('kepala_sekolah_at')->nullable()->after('sdm_at');
            $table->text('catatan_kepala_sekolah')->nullable()->after('catatan_sdm');
        });
    }

    public function down(): void
    {
        Schema::table('guru_izins', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kepala_sekolah_id');
            $table->dropColumn(['status_kepala_sekolah', 'kepala_sekolah_at', 'catatan_kepala_sekolah']);
        });
    }
};
