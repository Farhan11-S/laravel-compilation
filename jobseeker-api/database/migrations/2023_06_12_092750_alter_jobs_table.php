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
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('job_type')->nullable()->change();
            $table->integer('int_hires_needed')->nullable()->change();
            $table->integer('expected_hire_date')->nullable()->change();
            $table->integer('minimum_wage')->nullable()->change();
            $table->integer('maximum_wage')->nullable()->change();
            $table->string('rate')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE jobs SET job_type = '' WHERE job_type IS NULL;");
        DB::statement("UPDATE jobs SET int_hires_needed = 0 WHERE int_hires_needed IS NULL;");
        DB::statement("UPDATE jobs SET expected_hire_date = 0 WHERE expected_hire_date IS NULL;");
        DB::statement("UPDATE jobs SET minimum_wage = 0 WHERE minimum_wage IS NULL;");
        DB::statement("UPDATE jobs SET maximum_wage = 0 WHERE maximum_wage IS NULL;");
        DB::statement("UPDATE jobs SET rate = '' WHERE rate IS NULL;");
        
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('job_type')->nullable(false)->change();
            $table->integer('int_hires_needed')->nullable(false)->change();
            $table->integer('expected_hire_date')->nullable(false)->change();
            $table->integer('minimum_wage')->nullable(false)->change();
            $table->integer('maximum_wage')->nullable(false)->change();
            $table->string('rate')->nullable(false)->change();
        });
    }
};
