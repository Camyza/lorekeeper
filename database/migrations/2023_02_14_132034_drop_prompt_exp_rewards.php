<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        //
        Schema::dropIfExists('prompt_exp_rewards');
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn('bonus');
        });

        Schema::table('prompt_rewards', function (Blueprint $table) {
            // change the rewardable_id to allow null values
            $table->integer('rewardable_id')->unsigned()->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        Schema::create('prompt_exp_rewards', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('prompt_id')->unsigned()->default(0);
            $table->string('user_exp')->nullable()->default(null);
            $table->string('user_points')->nullable()->default(null);
            $table->string('chara_exp')->nullable()->default(null);
            $table->string('chara_points')->nullable()->default(null);

            $table->foreign('prompt_id')->references('id')->on('prompts');
        });
        Schema::table('submissions', function (Blueprint $table) {
            $table->string('bonus')->nullable()->default(null);
        });
        Schema::table('prompt_rewards', function (Blueprint $table) {
            $table->integer('rewardable_id')->unsigned()->nullable(false)->default(0)->change();
        });
    }
};
