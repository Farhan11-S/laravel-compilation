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
            $table->date('application_deadline')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE jobs SET application_deadline = NOW() WHERE application_deadline IS NULL;");
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('application_deadline')->nullable(false)->change();
        });
    }
};
