<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kantin_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kantin_id')->constrained('users')->cascadeOnDelete(); // The Kantin Owner user_id
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete(); // The Student user_id
            $table->string('order_number')->unique();
            $table->decimal('total_amount', 10, 2);
            $table->string('payment_method'); // e.g., 'qris', 'cash', 'saldo'
            $table->string('status')->default('pending'); // pending, preparing, ready, completed, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kantin_orders');
    }
};
