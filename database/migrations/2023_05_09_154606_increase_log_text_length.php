<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        //
        // increase user_gears_log 'log', 'data' column length
        Schema::table('user_gears_log', function (Blueprint $table) {
            $table->text('log', 2048)->change();
            $table->text('data', 2048)->change();
        });
        //  same for user_weapons_log
        Schema::table('user_weapons_log', function (Blueprint $table) {
            $table->text('log', 2048)->change();
            $table->text('data', 2048)->change();
        });
        // and for pets
        Schema::table('user_pets_log', function (Blueprint $table) {
            $table->text('log', 2048)->change();
            $table->text('data', 2048)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        // dont set this back lol
    }
};
