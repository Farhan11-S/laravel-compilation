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
        Schema::create('interview_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->datetime('start');
            $table->datetime('end')->nullable();
            $table->string('link')->nullable();
            $table->string('place')->nullable();
            $table->string('pic')->nullable();
            $table->foreignId('created_by')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('status')->nullable();
            $table->datetime('reschedule_request_datetime')->nullable();
            $table->text('reschedule_reasoning')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interview_schedules');
    }
};
