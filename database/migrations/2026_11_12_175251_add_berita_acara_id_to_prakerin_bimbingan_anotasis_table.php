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
        Schema::table('prakerin_bimbingan_anotasis', function (Blueprint $table) {
            $table->foreignId('prakerin_bimbingan_berita_acara_id')
                ->nullable()
                ->after('prakerin_bimbingan_tahap_id')
                ->constrained('prakerin_bimbingan_berita_acaras', indexName: 'fk_anotasi_ba_id')
                ->nullOnDelete();

            $table->index(['prakerin_bimbingan_tahap_id', 'prakerin_bimbingan_berita_acara_id'], 'idx_anotasi_tahap_ba');
        });

        // Hubungkan anotasi lama ke Berita Acara terakhir jika ada tahap yang sudah pernah menerbitkan Berita Acara
        try {
            $beritaAcaras = \Illuminate\Support\Facades\DB::table('prakerin_bimbingan_berita_acaras')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($beritaAcaras as $ba) {
                \Illuminate\Support\Facades\DB::table('prakerin_bimbingan_anotasis')
                    ->where('prakerin_bimbingan_tahap_id', $ba->prakerin_bimbingan_tahap_id)
                    ->whereNull('prakerin_bimbingan_berita_acara_id')
                    ->where('created_at', '<=', $ba->diterbitkan_at ?? $ba->created_at)
                    ->update(['prakerin_bimbingan_berita_acara_id' => $ba->id]);
            }
        } catch (\Throwable $e) {
            // Silently ignore if data migration encounters edge case during fresh migrations
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prakerin_bimbingan_anotasis', function (Blueprint $table) {
            $table->dropForeign('fk_anotasi_ba_id');
            $table->dropIndex('idx_anotasi_tahap_ba');
            $table->dropColumn('prakerin_bimbingan_berita_acara_id');
        });
    }
};
