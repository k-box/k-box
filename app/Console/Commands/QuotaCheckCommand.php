<?php

namespace KBox\Console\Commands;

use KBox\User;
use Illuminate\Console\Command;
use KBox\Jobs\CalculateUserUsedQuota;

class QuotaCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quota:check {--u|user=* : the user to verify}';

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
        $users = collect($this->option('user') ?? []);

        $this->line('Checking user storage quota...');

        if ($users->isEmpty()) {
            User::chunk(50, function ($users) {
                $users->each(function ($user) {
                    dispatch_now(new CalculateUserUsedQuota($user));
                });
            });
        } else {
            User::whereIn('id', $users->toArray())->chunk(50, function ($users) {
                $users->each(function ($user) {
                    dispatch_now(new CalculateUserUsedQuota($user));
                });
            });
        }

        return 0;
    }
}
