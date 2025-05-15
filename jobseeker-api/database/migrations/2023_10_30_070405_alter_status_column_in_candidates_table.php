<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'candidates';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE " . $this->set_schema_table . " MODIFY COLUMN status ENUM('waiting_review','accepted','rejected', 'saved', 'reviewed') NOT NULL DEFAULT 'waiting_review'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DELETE FROM " . $this->set_schema_table . " WHERE status = 'reviewed';");
        DB::statement("ALTER TABLE " . $this->set_schema_table . " MODIFY COLUMN status ENUM('waiting_review','accepted','rejected', 'saved') NOT NULL DEFAULT 'waiting_review'");
    }
};
