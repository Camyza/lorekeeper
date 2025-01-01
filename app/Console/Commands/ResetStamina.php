<?php

namespace App\Console\Commands;

use App\Models\User\UserLevel;
use Illuminate\Console\Command;
use Settings;

class ResetStamina extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset-stamina';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'resets user level stamina.';

    /**
     * Create a new command instance.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        // Reset stamina
        UserLevel::where('stamina', '<', Settings::get('stamina_per_object'))->update(['stamina' => Settings::get('stamina_per_object')]);
    }
}
