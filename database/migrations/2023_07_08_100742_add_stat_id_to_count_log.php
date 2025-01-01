<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('count_log', function (Blueprint $table) {
            //
            $table->integer('stat_id')->nullable()->default(null)->after('character_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('count_log', function (Blueprint $table) {
            //
            $table->dropColumn('stat_id');
        });
    }
};
