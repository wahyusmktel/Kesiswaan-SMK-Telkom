<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('manual_signed_document_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manual_signed_document_id')->constrained('manual_signed_documents')->cascadeOnDelete();
            $table->foreignId('signer_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('digital_document_id')->nullable()->constrained('digital_documents')->nullOnDelete();
            $table->unsignedInteger('sequence');
            $table->string('status')->default('waiting');
            $table->unsignedInteger('signed_page')->nullable();
            $table->decimal('qr_x_mm', 8, 2)->nullable();
            $table->decimal('qr_y_mm', 8, 2)->nullable();
            $table->decimal('qr_size_mm', 8, 2)->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();

            $table->unique(['manual_signed_document_id', 'sequence'], 'manual_doc_step_sequence_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manual_signed_document_steps');
    }
};
