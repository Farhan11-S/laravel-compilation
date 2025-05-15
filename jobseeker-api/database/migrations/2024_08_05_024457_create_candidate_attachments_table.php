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
        Schema::create('candidate_attachments', function (Blueprint $table) {
            $table->id();
            $table->string('content_link');
            $table->string('content_type');
            $table->string('user_attachments')->nullable();
            $table->foreignId('candidate_id')
                ->constrained('candidates');
            $table->foreignId('created_by')
                ->nullable()
                ->default(null)
                ->constrained('users');
            $table->foreignId('deleted_by')
                ->nullable()
                ->default(null)
                ->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_attachments');
    }
};
