<?php

namespace KBox\Console\Commands;

use KBox\Services\ReadonlyMode;
use Illuminate\Console\Command;
use Illuminate\Support\InteractsWithTime;

class ActivateReadonlyModeCommand extends Command
{
    use InteractsWithTime;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'readonly:up {--message= : The message for the readonly mode}
                                        {--retry= : The number of seconds after which the request may be retried}
                                        {--allow=* : IP or networks allowed to access the application while in readonly mode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Put the application into readonly mode';

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
        $this->readonly->activate($this->getReadonlyConfiguration());

        $this->comment('Application is now in readonly mode.');
    }

    /**
     * Get the readonly mode configuration.
     *
     * @return array
     */
    protected function getReadonlyConfiguration()
    {
        return [
            'time' => $this->currentTime(),
            'message' => $this->option('message'),
            'retry' => $this->getRetryTime(),
            'allowed' => $this->option('allow'),
        ];
    }

    /**
     * Get the number of seconds the client should wait before retrying their request.
     *
     * @return int|null
     */
    protected function getRetryTime()
    {
        $retry = $this->option('retry');

        return is_numeric($retry) && $retry > 0 ? (int) $retry : null;
    }
}
