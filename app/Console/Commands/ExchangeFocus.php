<?php

namespace App\Console\Commands;

use App\Models\Submission\Submission;
use App\Models\Submission\SubmissionCharacter;
use Illuminate\Console\Command;

class ExchangeFocus extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange-focus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

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
        $submissions = Submission::where('focus_chara_id', '!=', null)->get();
        foreach ($submissions as $submission) {
            if (!SubmissionCharacter::where('submission_id', $submission->id)->where('character_id', $submission->focus_chara_id)->exists()) {
                SubmissionCharacter::create([
                    'submission_id' => $submission->id,
                    'character_id'  => $submission->focus_chara_id,
                    'is_focus'      => 1,
                ]);
            }
        }
        $this->info('Complete. Please run php artisan migrate.');
    }
}
