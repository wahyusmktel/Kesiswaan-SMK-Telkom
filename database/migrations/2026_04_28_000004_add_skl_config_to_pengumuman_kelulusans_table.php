<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengumuman_kelulusans', function (Blueprint $table) {
            $table->string('kop_surat_path')->nullable()->after('skl_aktif');
            $table->string('nomor_surat_prefix')->nullable()->after('kop_surat_path');
            $table->unsignedSmallInteger('nomor_surat_start')->default(1)->after('nomor_surat_prefix');
            $table->string('kota_surat')->nullable()->after('nomor_surat_start');
            $table->date('tanggal_surat')->nullable()->after('kota_surat');
            $table->string('nama_kepala_sekolah')->nullable()->after('tanggal_surat');
            $table->string('nip_kepala_sekolah')->nullable()->after('nama_kepala_sekolah');
            $table->string('ttd_stempel_path')->nullable()->after('nip_kepala_sekolah');
        });
    }

    public function down(): void
    {
        Schema::table('pengumuman_kelulusans', function (Blueprint $table) {
            $table->dropColumn([
                'kop_surat_path',
                'nomor_surat_prefix',
                'nomor_surat_start',
                'kota_surat',
                'tanggal_surat',
                'nama_kepala_sekolah',
                'nip_kepala_sekolah',
                'ttd_stempel_path',
            ]);
        });
    }
};
