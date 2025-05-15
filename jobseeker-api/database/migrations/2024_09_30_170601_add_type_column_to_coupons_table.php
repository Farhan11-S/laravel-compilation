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
        Schema::table('coupons', function (Blueprint $table) {
            $table->integer('duration')->nullable()->change();
            $table->string('type')->default('REGISTRATION')->after('duration');
            $table->string('value')->nullable()->after('type');
        });
        Schema::table('subscription_items', function (Blueprint $table) {
            $table->foreignId('coupon_id')
                ->nullable()
                ->default(null)
                ->after('schedule_id')
                ->constrained('coupons');
        });
        Schema::table('subscription_transactions', function (Blueprint $table) {
            $table->foreignId('coupon_id')
                ->nullable()
                ->default(null)
                ->after('account_name')
                ->constrained('coupons');
        });
        Schema::table('coupon_usages', function (Blueprint $table) {
            $table->timestamp('ended_at')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn(['type', 'value']);
            $table->integer('duration')->nullable(false)->change();
        });
        Schema::table('subscription_items', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropColumn(['coupon_id']);
        });
        Schema::table('subscription_transactions', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropColumn(['coupon_id']);
        });
        Schema::table('coupon_usages', function (Blueprint $table) {
            $table->timestamp('ended_at')->nullable(false)->change();
        });
    }
};
