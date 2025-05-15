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
        Schema::dropIfExists('menu_accesses');
        Schema::dropIfExists('menus');

        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('slug')->unique();
            $table->string('link')->unique();
            $table->string('parent')->default(0);
            $table->integer('position')->default(1);
            $table->string('place');
            $table->boolean('can_access_list')->default(1);
            $table->boolean('can_access_detail')->default(1);
            $table->boolean('can_create')->default(1);
            $table->boolean('can_update')->default(1);
            $table->boolean('can_delete')->default(1);
            $table->foreignId('created_by')
                ->nullable()
                ->default(null)
                ->constrained('users');
            $table->foreignId('deleted_by')
                ->nullable()
                ->default(null)
                ->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
