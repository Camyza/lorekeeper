<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->text('deviantart')->nullable()->default(null);
            $table->text('bluesky')->nullable()->default(null);
            $table->text('toyhouse')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn('deviantart');
            $table->dropColumn('bluesky');
            $table->dropColumn('toyhouse');
        });
    }
};
