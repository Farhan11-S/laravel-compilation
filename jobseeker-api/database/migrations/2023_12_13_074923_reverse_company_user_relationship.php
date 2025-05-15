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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')
                ->after('customer_id')
                ->nullable()
                ->default(null)
                ->constrained();
        });

        DB::statement("UPDATE users  u, companies  c SET u.company_id = c.id  WHERE u.id = c.user_id;");

        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->after('sub_industry')
                ->nullable()
                ->default(null)
                ->constrained();
        });

        DB::raw("UPDATE users  u, companies  c SET c.user_id = u.id  WHERE c.id = u.company_id;");
    }
};
