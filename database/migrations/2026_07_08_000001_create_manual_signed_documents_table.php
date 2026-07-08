<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('manual_signed_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('digital_document_id')->nullable()->constrained('digital_documents')->nullOnDelete();
            $table->string('title');
            $table->string('original_file_name');
            $table->string('original_file_path');
            $table->string('signed_file_path');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->unsignedInteger('page_count')->default(1);
            $table->unsignedInteger('signed_page')->default(1);
            $table->decimal('qr_x_mm', 8, 2)->default(15);
            $table->decimal('qr_y_mm', 8, 2)->default(15);
            $table->decimal('qr_size_mm', 8, 2)->default(28);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manual_signed_documents');
    }
};
