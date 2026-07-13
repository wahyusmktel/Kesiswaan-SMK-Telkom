<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_tahun_pelajaran_id')->constrained('tahun_pelajaran')->cascadeOnDelete();
            $table->foreignId('target_tahun_pelajaran_id')->constrained('tahun_pelajaran')->cascadeOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('promoted_count')->default(0);
            $table->unsignedInteger('graduated_count')->default(0);
            $table->unsignedInteger('created_rombel_count')->default(0);
            $table->json('summary')->nullable();
            $table->timestamps();

            $table->unique(
                ['source_tahun_pelajaran_id', 'target_tahun_pelajaran_id'],
                'class_promotions_source_target_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_promotions');
    }
};
