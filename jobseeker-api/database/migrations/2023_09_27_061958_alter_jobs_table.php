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
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('hours_per_week')->nullable()->after('communication_email');
            $table->string('contract_length')->nullable()->after('communication_email');
            $table->string('contract_period')->nullable()->after('communication_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn([
                'hours_per_week', 'contract_length', 'contract_period'
            ]);
        });
    }
};
