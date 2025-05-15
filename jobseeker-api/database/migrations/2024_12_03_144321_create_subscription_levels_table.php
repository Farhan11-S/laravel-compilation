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
        Schema::create('subscription_levels', function (Blueprint $table) {
            $table->id();
            $table->integer('limit_create_job')->default(10);
            $table->integer('limit_interview_schedules')->default(10);
            $table->boolean('unlimited_candidate_application')->default(false);
            $table->boolean('show_resume_search_menu')->default(false);
            $table->string('premium_ads')->default(json_encode([]));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_levels');
    }
};
