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
        Schema::create('grade_criterias', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('grade_id')->constrained()->onDelete('cascade');
            $table->foreignId('assignment_criteria_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_criterias');
    }
};
