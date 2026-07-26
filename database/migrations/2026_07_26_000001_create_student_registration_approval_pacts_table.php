<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_registration_approval_pacts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('approver_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('approver_name');
            $table->string('approver_email')->nullable();
            $table->json('registration_ids');
            $table->json('student_snapshots');
            $table->json('statements');
            $table->unsignedInteger('approved_count');
            $table->timestamp('signed_at');
            $table->timestamps();

            $table->index(['approver_user_id', 'signed_at'], 'student_registration_pacts_approver_signed_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_registration_approval_pacts');
    }
};
