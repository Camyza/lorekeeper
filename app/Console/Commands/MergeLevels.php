<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MergeLevels extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'merge-levels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Combines level rewards and requirements into a tables instead of type divided tables.';

    /**
     * Create a new command instance.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        if (Schema::hasTable('level_rewards')) {
            $this->info('Table already exists, command will not run.');

            return;
        }

        $characterRewards = DB::table('character_level_rewards')->get();

        // put all the rewards into user_level_rewards
        foreach ($characterRewards as $reward) {
            DB::table('user_level_rewards')->insert([
                'level_id'        => $reward->level_id,
                'rewardable_type' => $reward->rewardable_type,
                'rewardable_id'   => $reward->rewardable_id,
                'quantity'        => $reward->quantity,
            ]);
        }

        // rename the table user_level_rewards to level_rewards
        Schema::rename('user_level_rewards', 'level_rewards');

        Schema::table('level_rewards', function (Blueprint $table) {
            $table->integer('rewardable_id')->nullable()->default(null)->change();
        });

        // drop the table character_level_rewards
        Schema::dropIfExists('character_level_rewards');

        // do the same for requirements
        $characterRequirements = DB::table('character_level_requirements')->get();

        // put all the requirements into user_level_requirements
        foreach ($characterRequirements as $requirement) {
            DB::table('user_level_requirements')->insert([
                'level_id'        => $requirement->level_id,
                'limit_type'      => $requirement->limit_type,
                'limit_id'        => $requirement->character_id,
                'quantity'        => $requirement->quantity,
            ]);
        }

        // rename the table user_level_requirements to level_requirements
        Schema::rename('user_level_requirements', 'level_requirements');

        // drop the table character_level_requirements
        Schema::dropIfExists('character_level_requirements');

        // running migrate
        $this->info('Running migrate...');
        $this->call('migrate');

        $this->info('Done');
    }
}
