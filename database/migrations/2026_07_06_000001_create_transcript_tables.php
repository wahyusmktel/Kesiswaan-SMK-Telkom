<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transcript_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('sort_order')->default(1);
            $table->enum('group', ['umum', 'muatan_lokal', 'kejuruan']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['name', 'group']);
        });

        Schema::create('transcript_diploma_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_siswa_id')->unique()->constrained('master_siswa')->cascadeOnDelete();
            $table->string('diploma_number')->nullable();
            $table->timestamps();
        });

        Schema::create('transcript_configs', function (Blueprint $table) {
            $table->id();
            $table->string('school_name')->nullable();
            $table->string('npsn')->nullable();
            $table->date('graduation_date')->nullable();
            $table->string('signature_city')->nullable();
            $table->date('signature_date')->nullable();
            $table->string('principal_name')->nullable();
            $table->string('principal_nip')->nullable();
            $table->text('letterhead')->nullable();
            $table->string('number_start')->nullable();
            $table->string('number_end')->nullable();
            $table->string('number_suffix')->default('/SMKTEL-LPG/KURL.03/V/2026');
            $table->date('number_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transcript_configs');
        Schema::dropIfExists('transcript_diploma_numbers');
        Schema::dropIfExists('transcript_subjects');
    }
};
