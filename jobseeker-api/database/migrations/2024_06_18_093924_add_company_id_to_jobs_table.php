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
            $table->foreignId('company_id')
                ->after('user_id')
                ->nullable()
                ->default(null)
                ->constrained();
        });

        DB::statement("UPDATE companies SET name=CONCAT(name, id) WHERE id IN (SELECT * FROM (SELECT c.id FROM companies c INNER JOIN (SELECT name FROM companies c2 GROUP  BY name HAVING COUNT(id) > 1) dup ON c.name = dup.name) AS t)");

        Schema::table('companies', function (Blueprint $table) {
            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropUnique(['name']);
        });
    }
};
