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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('country');
            $table->string('language');
            $table->boolean('is_hiring_manager');
            $table->string('job_title');
            $table->string('location');
            $table->string('job_type');
            $table->integer('int_hires_needed');
            $table->integer('expected_hire_date');
            $table->integer('minimum_wage');
            $table->integer('maximum_wage');
            $table->string('rate');
            $table->string('job_description');
            $table->enum('resume_required', ['yes', 'no', 'optional']);
            $table->date('application_deadline');
            $table->string('communication_email');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
