<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berita_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('berita_id')->constrained('beritas')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('berita_comments')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 120);
            $table->string('email')->nullable();
            $table->text('content');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->string('ip_hash', 64)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->foreignId('moderated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('moderated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berita_comments');
    }
};
