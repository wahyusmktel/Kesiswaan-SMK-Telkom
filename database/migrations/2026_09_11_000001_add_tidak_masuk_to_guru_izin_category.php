<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guru_izins', function (Blueprint $table) {
            $table->enum('kategori_penyetujuan', ['sekolah', 'luar', 'tidak_masuk', 'terlambat'])
                ->default('luar')
                ->change();
        });

        // Lingkungan Sekolah kini selesai di Piket; lepaskan antrean Kepala Sekolah lama yang belum diputuskan.
        DB::table('guru_izins')
            ->where('kategori_penyetujuan', 'sekolah')
            ->where('status_kepala_sekolah', 'menunggu')
            ->update(['status_kepala_sekolah' => 'tidak_diperlukan']);
    }

    public function down(): void
    {
        DB::table('guru_izins')->where('kategori_penyetujuan', 'tidak_masuk')->update([
            'kategori_penyetujuan' => 'luar',
        ]);

        Schema::table('guru_izins', function (Blueprint $table) {
            $table->enum('kategori_penyetujuan', ['sekolah', 'luar', 'terlambat'])
                ->default('luar')
                ->change();
        });
    }
};
