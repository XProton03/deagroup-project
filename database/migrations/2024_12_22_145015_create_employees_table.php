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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departments_id')->constrained('departments')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('provinces_id')->constrained('provinces')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('regencies_id')->constrained('regencies')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('districts_id')->constrained('districts')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('villages_id')->constrained('villages')->cascadeOnUpdate()->cascadeOnDelete()->nullable();
            $table->string('employee_code')->nullable();
            $table->string('name')->nullable();
            $table->string('gender')->nullable();
            $table->string('birth_date')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->foreignId('employement_statuses_id')->constrained('employement_statuses')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('job_positions_id')->constrained('job_positions')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
