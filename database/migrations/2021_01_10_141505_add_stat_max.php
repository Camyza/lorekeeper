<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatMax extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        //
        Schema::table('stats', function (Blueprint $table) {
            $table->integer('max_level')->nullable()->unsigned()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        Schema::table('stats', function (Blueprint $table) {
            $table->dropColumn('max_level');
        });
    }
}
