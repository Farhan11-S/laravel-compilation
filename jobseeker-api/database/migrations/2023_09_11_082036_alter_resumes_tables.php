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
        Schema::table('certifications', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
        });
        Schema::table('work_experiences', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE certifications SET description = '' WHERE description IS NULL;");
        DB::statement("UPDATE work_experiences SET description = '' WHERE description IS NULL;");
        Schema::table('certifications', function (Blueprint $table) {
            $table->string('description')->nullable(false)->change();
        });
        Schema::table('work_experiences', function (Blueprint $table) {
            $table->string('description')->nullable(false)->change();
        });
    }
};
