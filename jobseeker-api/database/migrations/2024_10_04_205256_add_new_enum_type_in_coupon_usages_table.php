<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'coupon_usages';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE " . $this->set_schema_table . " MODIFY COLUMN type ENUM('coupon', 'referral', 'discount') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 
    }
};
