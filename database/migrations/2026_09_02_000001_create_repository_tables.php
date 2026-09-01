<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repository_settings', function (Blueprint $table) {
            $table->id();
            $table->string('local_base_url')->nullable();
            $table->string('public_base_url')->nullable();
            $table->timestamps();
        });

        Schema::create('repository_files', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_token')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('original_name');
            $table->string('path')->unique();
            $table->string('extension', 30)->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size');
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('repository_uploads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('original_name');
            $table->string('extension', 30)->nullable();
            $table->string('client_mime_type')->nullable();
            $table->unsignedBigInteger('size');
            $table->unsignedInteger('chunk_size');
            $table->unsignedInteger('total_chunks');
            $table->timestamp('expires_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repository_uploads');
        Schema::dropIfExists('repository_files');
        Schema::dropIfExists('repository_settings');
    }
};
