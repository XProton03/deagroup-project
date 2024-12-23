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
        Schema::create('employement_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employees_id')->constrained('employees')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('file_name')->nullable();
            $table->text('file');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employement_files');
    }
};
