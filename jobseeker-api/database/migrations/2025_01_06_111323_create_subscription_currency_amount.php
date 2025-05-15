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
        Schema::create('subscription_currency_amount', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_item_id')
                ->nullable()
                ->default(null)
                ->constrained('subscription_items');
            $table->string('currency_code');
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_currency_amount');
    }
};
