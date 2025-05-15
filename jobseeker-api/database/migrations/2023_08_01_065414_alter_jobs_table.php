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
            $table->string('application_deadline')->nullable()->change();
            $table->boolean('auto_reject_candidate')->default(false);
            $table->boolean('can_message')->default(true);
            $table->boolean('reveal_email_to_candidate')->default(false);
            $table->boolean('send_email_on_candidate_apply')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('application_deadline')->nullable(false)->change();
            $table->dropColumn([
                'auto_reject_candidate', 'can_message', 'reveal_email_to_candidate', 'send_email_on_candidate_apply'
            ]);
        });
    }
};
