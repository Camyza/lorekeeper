<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        // since attacks are in a previous migration, we need to drop it first
        Schema::dropIfExists('attacks');

        Schema::create('attacks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable()->default(null);
            // category basically
            $table->integer('type_id')->nullable()->default(null);

            // maybe if someone wants to add attacks to things other than weapons
            // I won't for sure but shrug
            $table->string('modifier_type')->default('Weapon');
            // by default attack will just multiply by X but if you want to add
            // something else you can code it using this
            $table->string('modifier')->default('*');
            $table->double('modifier_value')->default(1.25);

            $table->boolean('has_image')->default(false);

            // to allow for evolution of attacks
            $table->integer('parent_id')->nullable()->default(null);
        });

        Schema::create('attack_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable()->default(null);

            $table->boolean('has_image')->default(false);
        });

        Schema::create('attack_weaknesses', function (Blueprint $table) {
            $table->integer('attack_id')->unsigned();
            $table->integer('weakness_id')->unsigned();
            $table->integer('count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        Schema::dropIfExists('attacks');
        Schema::dropIfExists('attack_types');
        Schema::dropIfExists('attack_weaknesses');
    }
};
