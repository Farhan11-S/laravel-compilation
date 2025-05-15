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
        Schema::table('jemaah', function (Blueprint $table) {
            $table->string('ktp_path')->nullable()->after('submitted_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jemaah', function (Blueprint $table) {
            $table->dropColumn('ktp_path');
        });
    }
};
