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
        Schema::create('job_channel_groups', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('job_id')
                ->nullable()
                ->default(null)
                ->constrained('jobs');
            $table->integer('facebook')->default(0);
            $table->integer('twitter')->default(0);
            $table->integer('linkedin')->default(0);
            $table->integer('instagram')->default(0);
            $table->integer('whatsapp')->default(0);
            $table->integer('telegram')->default(0);
            $table->integer('email')->default(0);
            $table->integer('shared_url')->default(0);
            $table->integer('system')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_channel_groups');
    }
};
