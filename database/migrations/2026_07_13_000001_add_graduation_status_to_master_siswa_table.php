<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_siswa', function (Blueprint $table) {
            $table->string('status', 20)->default('aktif')->after('user_id')->index();
            $table->date('graduated_at')->nullable()->after('status');
            $table->foreignId('graduation_tahun_pelajaran_id')
                ->nullable()
                ->after('graduated_at')
                ->constrained('tahun_pelajaran')
                ->nullOnDelete();
            $table->text('graduation_notes')->nullable()->after('graduation_tahun_pelajaran_id');
        });
    }

    public function down(): void
    {
        Schema::table('master_siswa', function (Blueprint $table) {
            $table->dropForeign(['graduation_tahun_pelajaran_id']);
            $table->dropIndex(['status']);
            $table->dropColumn([
                'status',
                'graduated_at',
                'graduation_tahun_pelajaran_id',
                'graduation_notes',
            ]);
        });
    }
};
