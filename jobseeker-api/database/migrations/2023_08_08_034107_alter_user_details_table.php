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
        Schema::table('user_details', function (Blueprint $table) {
            $table->string('social_medias')->after('postal_code')->nullable();
            $table->date('date_of_birth')->after('social_medias')->nullable();
            $table->string('place_of_birth')->after('date_of_birth')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->dropColumn(['social_medias', 'date_of_birth', 'place_of_birth']);
        });
    }
};
