<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetCountLog extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset-count-log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes unneccessary count logs.';

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
        \App\Models\Stat\CountLog::where('log_type', 'Health Regenerated')->delete();
    }
}
