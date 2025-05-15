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
            $table->text('job_description')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE jobs SET job_description = '' WHERE CHAR_LENGTH(job_description) >= 254;");
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('job_description')->change();
        });
    }
};
