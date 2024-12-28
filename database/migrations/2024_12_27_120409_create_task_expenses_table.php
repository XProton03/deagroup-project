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
        Schema::create('task_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tasks_id')->constrained('tasks')->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->unsignedInteger('ammount')->nullable();
            $table->string('type')->nullable();
            $table->text('file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_expenses');
    }
};
