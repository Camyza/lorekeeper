<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateClaymores extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        //
        Schema::table('stats', function (Blueprint $table) {
            $table->string('colour')->nullable()->default(null);
            $table->string('abbreviation')->nullable()->default(null)->change();
            $table->renameColumn('step', 'increment');
        });

        Schema::table('levels', function (Blueprint $table) {
            $table->dropColumn('stat_points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        //
        Schema::table('stats', function (Blueprint $table) {
            $table->dropColumn('colour');
            $table->string('abbreviation')->nullable(false)->change();
            $table->renameColumn('increment', 'step');
        });

        Schema::table('levels', function (Blueprint $table) {
            $table->integer('stat_points')->unsigned();
        });
    }
}
