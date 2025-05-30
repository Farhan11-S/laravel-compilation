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
        Schema::create('subscription_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')
                ->constrained('banks')
                ->onDelete('restrict');
            $table->string('account_number');
            $table->string('account_name');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_banks');
    }
};
