<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElementTypingTables extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('elements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);
            $table->boolean('has_image')->default(false);
            $table->string('colour')->nullable()->default(null);
        });

        Schema::create('element_weaknesses', function (Blueprint $table) {
            $table->id();
            $table->integer('element_id');
            $table->integer('weakness_id'); // the element that element_id is weak against
            $table->float('multiplier');
        });

        Schema::create('element_immunities', function (Blueprint $table) {
            $table->id();
            $table->integer('element_id');
            $table->integer('immunity_id'); // the element that element_id is immune against
        });

        // add typing tracking table
        Schema::create('typings', function (Blueprint $table) {
            $table->id();
            $table->string('typing_model'); // character, pet, gear, weapon etc
            $table->integer('typing_id'); // the id of the character, pet, gear, weapon etc
            $table->string('element_ids'); // comma separated list of element ids
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::dropIfExists('elements');
        Schema::dropIfExists('element_weaknesses');
        Schema::dropIfExists('element_immunities');
        Schema::dropIfExists('typings');
    }
}
