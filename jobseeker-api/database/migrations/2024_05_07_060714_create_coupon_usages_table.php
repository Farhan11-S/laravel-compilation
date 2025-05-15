<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users');
            $table->foreignId('coupon_id')
                ->nullable()
                ->default(null)
                ->constrained('coupons');
            $table->foreignId('referral_id')
                ->nullable()
                ->default(null)
                ->constrained('users');
            $table->enum('type', ['coupon', 'referral']);
            $table->enum('status', ['active', 'deactive'])->default('active');
            $table->timestamp('ended_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_usages');
    }
};
