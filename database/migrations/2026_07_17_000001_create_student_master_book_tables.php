<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_master_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_siswa_id')->unique()->constrained('master_siswa')->cascadeOnDelete();
            $table->date('admission_date')->nullable();
            $table->string('admission_status')->nullable();
            $table->string('previous_school')->nullable();
            $table->string('previous_diploma_number')->nullable();
            $table->date('previous_diploma_date')->nullable();
            $table->string('blood_type', 5)->nullable();
            $table->text('medical_history')->nullable();
            $table->text('special_needs_notes')->nullable();
            $table->string('student_status')->default('aktif');
            $table->date('transfer_date')->nullable();
            $table->string('transfer_destination')->nullable();
            $table->text('transfer_reason')->nullable();
            $table->date('graduation_date')->nullable();
            $table->string('graduation_certificate_number')->nullable();
            $table->text('homeroom_notes')->nullable();
            $table->json('additional_data')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('student_master_book_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_master_book_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tahun_pelajaran_id')->nullable()->constrained('tahun_pelajaran')->nullOnDelete();
            $table->foreignId('rombel_id')->nullable()->constrained('rombels')->nullOnDelete();
            $table->string('school_year');
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->json('grades')->nullable();
            $table->json('extracurriculars')->nullable();
            $table->unsignedSmallInteger('sick_days')->default(0);
            $table->unsignedSmallInteger('permitted_days')->default(0);
            $table->unsignedSmallInteger('absent_days')->default(0);
            $table->string('conduct')->nullable();
            $table->text('development_notes')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(
                ['student_master_book_id', 'school_year', 'semester'],
                'master_book_period_unique'
            );
        });

        Schema::create('student_master_book_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_master_book_id')->constrained()->cascadeOnDelete();
            $table->string('category');
            $table->string('title');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['student_master_book_id', 'category'], 'master_book_attachment_category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_master_book_attachments');
        Schema::dropIfExists('student_master_book_periods');
        Schema::dropIfExists('student_master_books');
    }
};
