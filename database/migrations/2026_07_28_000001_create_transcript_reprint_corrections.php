<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transcript_reprint_corrections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_siswa_id')->constrained('master_siswa')->cascadeOnDelete();
            $table->foreignId('rombel_id')->constrained('rombels')->cascadeOnDelete();
            $table->string('corrected_name');
            $table->string('corrected_birth_place');
            $table->date('corrected_birth_date');
            $table->text('correction_reason');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['master_siswa_id', 'rombel_id'], 'transcript_reprint_correction_unique');
        });

        Schema::create('transcript_reprint_correction_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transcript_reprint_correction_id');
            $table->json('old_data');
            $table->json('new_data');
            $table->text('reason');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(
                'transcript_reprint_correction_id',
                'tr_reprint_history_correction_fk'
            )->references('id')->on('transcript_reprint_corrections')->cascadeOnDelete();
            $table->index(
                ['transcript_reprint_correction_id', 'created_at'],
                'transcript_reprint_history_created_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transcript_reprint_correction_histories');
        Schema::dropIfExists('transcript_reprint_corrections');
    }
};
