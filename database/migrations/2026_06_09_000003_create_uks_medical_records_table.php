<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uks_medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_siswa_id')->constrained('master_siswa')->cascadeOnDelete();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('visited_at');
            $table->string('complaint');
            $table->json('symptoms')->nullable();
            $table->text('anamnesis')->nullable();
            $table->string('diagnosis')->nullable();
            $table->text('treatment')->nullable();
            $table->text('medicine')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->string('blood_pressure')->nullable();
            $table->unsignedSmallInteger('pulse')->nullable();
            $table->unsignedTinyInteger('oxygen_saturation')->nullable();
            $table->enum('condition', ['ringan', 'sedang', 'berat'])->default('ringan');
            $table->enum('disposition', ['kembali_kelas', 'istirahat_uks', 'pulang', 'rujukan'])->default('kembali_kelas');
            $table->dateTime('rest_until')->nullable();
            $table->string('referral_facility_type')->nullable();
            $table->string('referral_facility_name')->nullable();
            $table->text('referral_reason')->nullable();
            $table->text('parent_notification')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['visited_at', 'disposition']);
            $table->index(['master_siswa_id', 'visited_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uks_medical_records');
    }
};
