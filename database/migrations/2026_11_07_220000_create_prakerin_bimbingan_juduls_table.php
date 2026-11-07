<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('prakerin_bimbingan_juduls')) {
            Schema::create('prakerin_bimbingan_juduls', function (Blueprint $table) {
                $table->id();
                $table->foreignId('prakerin_bimbingan_laporan_id')
                    ->constrained('prakerin_bimbingan_laporans')
                    ->cascadeOnDelete();
                $table->unsignedInteger('pengajuan_ke')->default(1);
                $table->string('judul_laporan');
                $table->text('deskripsi_judul')->nullable();
                $table->enum('status', ['diajukan', 'disetujui', 'ditolak'])->default('diajukan');
                $table->text('catatan_pembimbing')->nullable();
                $table->string('nomor_berita_acara')->nullable();
                $table->timestamp('diajukan_at')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        // Sinkronisasi data judul yang sudah ada di tabel utama
        if (Schema::hasTable('prakerin_bimbingan_laporans')) {
            $existingLaporans = DB::table('prakerin_bimbingan_laporans')
                ->whereNotNull('judul_laporan')
                ->where('judul_laporan', '!=', '')
                ->get();

            foreach ($existingLaporans as $laporan) {
                $exists = DB::table('prakerin_bimbingan_juduls')
                    ->where('prakerin_bimbingan_laporan_id', $laporan->id)
                    ->exists();

                if (! $exists) {
                    $nomorBa = null;
                    if ($laporan->status_judul === 'disetujui') {
                        $nomorBa = 'BA-JUDUL-ACC/' . date('Ymd') . '/' . str_pad($laporan->id, 4, '0', STR_PAD_LEFT);
                    } elseif ($laporan->status_judul === 'ditolak') {
                        $nomorBa = 'BA-JUDUL-TOLAK/' . date('Ymd') . '/' . str_pad($laporan->id, 4, '0', STR_PAD_LEFT) . '/R1';
                    }

                    DB::table('prakerin_bimbingan_juduls')->insert([
                        'prakerin_bimbingan_laporan_id' => $laporan->id,
                        'pengajuan_ke' => 1,
                        'judul_laporan' => $laporan->judul_laporan,
                        'deskripsi_judul' => $laporan->deskripsi_judul,
                        'status' => in_array($laporan->status_judul, ['disetujui', 'ditolak']) ? $laporan->status_judul : 'diajukan',
                        'catatan_pembimbing' => $laporan->catatan_judul,
                        'nomor_berita_acara' => $nomorBa,
                        'diajukan_at' => $laporan->judul_diajukan_at ?? now(),
                        'reviewed_at' => $laporan->judul_reviewed_at,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prakerin_bimbingan_juduls');
    }
};
