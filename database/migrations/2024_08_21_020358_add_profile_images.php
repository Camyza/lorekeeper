<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        // Add columns for avatar / references your default icon. If you prefer a png etc make sure to change the extension
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_img')->default('default.png');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('profile_img');
        });
    }
};