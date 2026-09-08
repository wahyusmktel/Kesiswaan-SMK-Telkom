<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('telegram_bot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('telegram_user_link_id')->constrained()->cascadeOnDelete();
            $table->string('flow');
            $table->string('step');
            $table->json('payload')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['telegram_user_link_id', 'flow']);
            $table->index(['telegram_bot_id', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_conversations');
    }
};
