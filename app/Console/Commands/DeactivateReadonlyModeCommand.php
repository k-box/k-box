<?php

namespace KBox\Console\Commands;

use KBox\Services\ReadonlyMode;
use Illuminate\Console\Command;

class DeactivateReadonlyModeCommand extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'readonly:down';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable the readonly mode and make the application accepting changes';

    /**
     * @var \KBox\Services\ReadonlyMode
     */
    private $readonly = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ReadonlyMode $service)
    {
        parent::__construct();

        $this->readonly = $service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->readonly->deactivate();

        $this->comment('Application is now live.');
    }
}
