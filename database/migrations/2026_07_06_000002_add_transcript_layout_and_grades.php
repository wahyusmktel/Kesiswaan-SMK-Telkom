<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transcript_configs', function (Blueprint $table) {
            $table->string('letterhead_path')->nullable()->after('letterhead');
            $table->decimal('margin_top', 6, 2)->default(15)->after('number_date');
            $table->decimal('margin_right', 6, 2)->default(15)->after('margin_top');
            $table->decimal('margin_bottom', 6, 2)->default(15)->after('margin_right');
            $table->decimal('margin_left', 6, 2)->default(15)->after('margin_bottom');
            $table->string('paper_size', 20)->default('A4')->after('margin_left');
        });

        Schema::create('transcript_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_siswa_id')->constrained('master_siswa')->cascadeOnDelete();
            $table->foreignId('transcript_subject_id')->constrained('transcript_subjects')->cascadeOnDelete();
            $table->decimal('score', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['master_siswa_id', 'transcript_subject_id'], 'transcript_grade_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transcript_grades');

        Schema::table('transcript_configs', function (Blueprint $table) {
            $table->dropColumn([
                'letterhead_path',
                'margin_top',
                'margin_right',
                'margin_bottom',
                'margin_left',
                'paper_size',
            ]);
        });
    }
};
