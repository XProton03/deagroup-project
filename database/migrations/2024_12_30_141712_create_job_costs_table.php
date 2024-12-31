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
        Schema::create('job_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tasks_id')->constrained('tasks')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedInteger('mandays')->nullable();
            $table->unsignedInteger('transports')->nullable();
            $table->unsignedInteger('accomodations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_costs');
    }
};
