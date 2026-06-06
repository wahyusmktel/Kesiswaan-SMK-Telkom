<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_calendar_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['holiday', 'collective_leave'])->default('holiday');
            $table->date('date_from');
            $table->date('date_to');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['date_from', 'date_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_calendar_events');
    }
};
