<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSpeciesToStatsAndSkills extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('species_limits', function (Blueprint $table) {
            $table->integer('species_id')->unsigned();
            $table->string('type')->default('stat');
            $table->integer('type_id')->unsigned();
            $table->boolean('is_subtype')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::dropIfExists('species_limits');
    }
}
