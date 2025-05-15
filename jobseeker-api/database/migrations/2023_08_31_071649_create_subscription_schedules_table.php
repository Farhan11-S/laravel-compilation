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
        Schema::create('subscription_schedules', function (Blueprint $table) {
            $table->id();
            $table->enum('interval', ['DAY', 'WEEK', 'MONTH']);
            $table->integer('interval_count');
            $table->integer('total_recurrence')->nullable();
            $table->enum('retry_interval', ['DAY'])->nullable();
            $table->integer('retry_interval_count')->nullable();
            $table->integer('total_retry')->nullable();
            $table->string('failed_attempt_notifications')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_schedules');
    }
};
