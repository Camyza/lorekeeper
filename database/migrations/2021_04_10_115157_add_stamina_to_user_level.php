<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStaminaToUserLevel extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('user_levels', function (Blueprint $table) {
            //
            $table->tinyInteger('stamina')->default(15)->unsigned();
            $table->integer('character_id')->default(null)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('user_levels', function (Blueprint $table) {
            //
            $table->dropColumn('stamina');
            $table->dropColumn('character_id');
        });
    }
}
