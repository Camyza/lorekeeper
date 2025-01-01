<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddingFieldsToMoreClaymoresTables extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('character_classes', function (Blueprint $table) {
            $table->boolean('is_visible')->default(1);
            $table->text('parsed_description')->after('description')->nullable()->default(null);
        });

        Schema::table('elements', function (Blueprint $table) {
            $table->boolean('is_visible')->default(1);
        });

        Schema::table('gears', function (Blueprint $table) {
            $table->text('parsed_description')->after('description')->nullable()->default(null);
            $table->boolean('is_visible')->default(1);
        });

        Schema::table('gear_categories', function (Blueprint $table) {
            $table->text('parsed_description')->after('description')->nullable()->default(null);
            $table->boolean('is_visible')->default(1);
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->boolean('is_visible')->default(1);
            $table->text('parsed_description')->after('description')->nullable()->default(null);
        });

        Schema::table('skill_categories', function (Blueprint $table) {
            $table->boolean('is_visible')->default(1);
            $table->text('parsed_description')->after('description')->nullable()->default(null);
        });

        Schema::table('pet_categories', function (Blueprint $table) {
            $table->boolean('is_visible')->default(1);
        });

        Schema::table('weapons', function (Blueprint $table) {
            $table->text('parsed_description')->after('description')->nullable()->default(null);
            $table->boolean('is_visible')->default(1);
        });

        Schema::table('weapon_categories', function (Blueprint $table) {
            $table->text('parsed_description')->after('description')->nullable()->default(null);
            $table->boolean('is_visible')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('character_classes', function (Blueprint $table) {
            $table->dropColumn('is_visible');
            $table->dropColumn('parsed_description');
        });

        Schema::table('elements', function (Blueprint $table) {
            $table->dropColumn('is_visible');
        });

        Schema::table('gears', function (Blueprint $table) {
            $table->dropColumn('parsed_description');
            $table->dropColumn('is_visible');
        });

        Schema::table('gear_categories', function (Blueprint $table) {
            $table->dropColumn('parsed_description');
            $table->dropColumn('is_visible');
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->dropColumn('is_visible');
            $table->dropColumn('parsed_description');
        });

        Schema::table('skill_categories', function (Blueprint $table) {
            $table->dropColumn('is_visible');
            $table->dropColumn('parsed_description');
        });

        Schema::table('pet_categories', function (Blueprint $table) {
            $table->dropColumn('is_visible');
        });

        Schema::table('weapons', function (Blueprint $table) {
            $table->dropColumn('parsed_description');
            $table->dropColumn('is_visible');
        });

        Schema::table('weapon_categories', function (Blueprint $table) {
            $table->dropColumn('parsed_description');
            $table->dropColumn('is_visible');
        });
    }
}
