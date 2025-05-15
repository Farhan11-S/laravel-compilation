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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('description')->nullable();
            $table->foreignId('created_by')
                ->nullable()
                ->default(null)
                ->constrained('users');
            $table->foreignId('deleted_by')
                ->nullable()
                ->default(null)
                ->constrained('users');
            $table->integer('duration')->comment('Duration for how long the bonus will active (in days), example : 7 is for a week, 1 is for a day');
            $table->timestamp('expired_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
