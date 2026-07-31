<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sso_applications', function (Blueprint $table) {
            $table->id();
            $table->uuid('passport_client_id')->unique();
            $table->text('description')->nullable();
            $table->string('homepage_url', 2048)->nullable();
            $table->string('logo_path')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign('passport_client_id')
                ->references('id')
                ->on('oauth_clients')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sso_applications');
    }
};
