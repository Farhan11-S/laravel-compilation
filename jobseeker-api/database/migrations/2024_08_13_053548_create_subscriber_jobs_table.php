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
        Schema::create('subscriber_jobs', function (Blueprint $table) {
            $table->id();
            $table->text('email');
            $table->text('token')->nullable();
            $table->text('status');
            $table->foreignId('user_id')
                ->nullable()
                ->default(null)
                ->constrained('users');
            $table->foreignId('created_by')
                ->nullable()
                ->default(null)
                ->constrained('users');
            $table->foreignId('deleted_by')
                ->nullable()
                ->default(null)
                ->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriber_jobs');
    }
};
