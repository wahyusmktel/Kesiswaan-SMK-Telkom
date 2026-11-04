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
        Schema::table('spmb_tpa_sesis', function (Blueprint $table) {
            $table->enum('status_mtk', ['Belum', 'Sedang Mengerjakan', 'Selesai'])->default('Belum')->after('status');
            $table->enum('status_bing', ['Belum', 'Sedang Mengerjakan', 'Selesai'])->default('Belum')->after('status_mtk');
            $table->enum('status_bindo', ['Belum', 'Sedang Mengerjakan', 'Selesai'])->default('Belum')->after('status_bing');
            $table->enum('status_tik', ['Belum', 'Sedang Mengerjakan', 'Selesai'])->default('Belum')->after('status_bindo');

            $table->timestamp('waktu_mulai_mtk')->nullable()->after('waktu_mulai');
            $table->timestamp('waktu_selesai_mtk')->nullable()->after('waktu_mulai_mtk');
            $table->timestamp('waktu_mulai_bing')->nullable()->after('waktu_selesai_mtk');
            $table->timestamp('waktu_selesai_bing')->nullable()->after('waktu_mulai_bing');
            $table->timestamp('waktu_mulai_bindo')->nullable()->after('waktu_selesai_bing');
            $table->timestamp('waktu_selesai_bindo')->nullable()->after('waktu_mulai_bindo');
            $table->timestamp('waktu_mulai_tik')->nullable()->after('waktu_selesai_bindo');
            $table->timestamp('waktu_selesai_tik')->nullable()->after('waktu_mulai_tik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spmb_tpa_sesis', function (Blueprint $table) {
            $table->dropColumn([
                'status_mtk',
                'status_bing',
                'status_bindo',
                'status_tik',
                'waktu_mulai_mtk',
                'waktu_selesai_mtk',
                'waktu_mulai_bing',
                'waktu_selesai_bing',
                'waktu_mulai_bindo',
                'waktu_selesai_bindo',
                'waktu_mulai_tik',
                'waktu_selesai_tik',
            ]);
        });
    }
};
