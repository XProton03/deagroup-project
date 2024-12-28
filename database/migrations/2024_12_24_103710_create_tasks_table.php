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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotations_id')->constrained('quotations')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('task_number')->nullable();
            $table->foreignId('companies_id')->constrained('companies')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('pic')->nullable();
            $table->unsignedInteger('phone')->nullable();
            $table->text('short_description')->nullable();
            $table->text('job_description')->nullable();
            $table->date('schedule')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedInteger('duration')->nullable();
            $table->foreignId('employees_id')->constrained('employees')->cascadeOnUpdate()->cascadeOnDelete()->nullable();
            $table->string('status')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
