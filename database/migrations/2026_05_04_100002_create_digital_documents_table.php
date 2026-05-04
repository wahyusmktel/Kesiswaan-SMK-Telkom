<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digital_documents', function (Blueprint $table) {
            $table->id();
            $table->uuid('token')->unique();
            $table->string('document_type');        // 'SKL', 'SURAT_IZIN', etc.
            $table->string('document_title');
            $table->unsignedBigInteger('reference_id')->nullable(); // e.g. siswa_kelulusan.id
            $table->string('document_hash');        // SHA-256 of key content
            $table->string('hmac_signature');       // HMAC-SHA256 (hash, APP_KEY)
            $table->foreignId('signed_by')->constrained('users')->cascadeOnDelete();
            $table->string('signer_name');
            $table->string('signer_nip')->nullable();
            $table->string('signer_role');
            $table->timestamp('signed_at');
            $table->boolean('is_valid')->default(true);
            $table->timestamp('revoked_at')->nullable();
            $table->string('revoke_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_documents');
    }
};
