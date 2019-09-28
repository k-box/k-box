<?php

namespace KBox\Console\Commands;

use Illuminate\Console\Command;
use KBox\Jobs\PurgeExpiredInvites;

class PurgeInvitesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invite:purge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge expired invitations';

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
        $this->line('Purging expired invites...');

        dispatch_now(new PurgeExpiredInvites);
        
        $this->line('completed');

        return 0;
    }
}
