<?php

namespace KBox\Console\Commands;

use Illuminate\Console\Command;
use KBox\Facades\UserQuota;

class QuotaCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quota:check {--u|user=* : the user to verify} {--no-notification}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify if user(s) are over-quota';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        UserQuota::withUser($user)->checkAvailableSpace($notify);
    }
}
