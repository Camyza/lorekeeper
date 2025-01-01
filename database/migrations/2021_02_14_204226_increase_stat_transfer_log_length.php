<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IncreaseStatTransferLogLength extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        //
        Schema::table('stat_transfer_log', function (Blueprint $table) {
            $table->string('log', 250)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        Schema::table('stat_transfer_log', function (Blueprint $table) {
            $table->string('log', 191)->change();
        });
    }
}
