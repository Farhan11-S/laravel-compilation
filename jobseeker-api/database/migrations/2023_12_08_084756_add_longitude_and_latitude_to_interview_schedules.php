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
        Schema::table('interview_schedules', function (Blueprint $table) {
            $table->dropColumn('place');
            $table->decimal('latitude', 10, 8)->after('link')->nullable();
            $table->decimal('longitude', 11, 8)->after('latitude')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interview_schedules', function (Blueprint $table) {
            $table->string('place')->nullable();
            $table->dropColumn(['longitude', 'latitude']);
        });
    }
};
