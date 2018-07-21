<?php

namespace KBox\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use KBox\Option;

declare(ticks = 1); // http://php.net/manual/it/function.pcntl-signal.php

class DmsQueueListen extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'dms:queuelisten';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start listening for jobs on the queue and report the status to the admin interface.';

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
        // set_exception_handler(array($this, "default_exception_handler"));
        
        register_shutdown_function([$this, 'shutdownCallback']);

        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGTERM, [$this, 'sigintShutdown']);
            pcntl_signal(SIGINT, [$this, 'sigintShutdown']);
        }

        try {
            $this->line("Starting DMS queue listener for <info>".$this->getLaravel()->environment()."</info>...");
        
            Option::put('dms.queuelistener.active', true);
            Option::put('dms.queuelistener.errorState', false);

            $queue_listen_result = $this->call('queue:listen', [
                '--tries' => 3,
                '--timeout' => 240,
                '--memory' => 256,
            ]);
        } catch (\Exception $ex) {
            $this->error($ex->getMessage());

            \Log::error('Queue Listener Error', ['error' => $ex->getMessage()]);
        }

        return 0;
    }

    public function default_exception_handler(Exception $e)
    {
    }
 
    public function sigintShutdown($signal)
    {
        if ($signal === SIGINT || $signal === SIGTERM) {
            $this->shutdownCallback();
        }
    }

    public function shutdownCallback()
    {

        // Will be called also after fatal errors

        $lastError = error_get_last();

        Option::put('dms.queuelistener.active', false);

        if (! is_null($lastError)) {
            var_dump($lastError);

            Option::put('dms.queuelistener.errorState', true);
        }

        $this->comment('Shutdown... :(');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            // ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            // ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }

    private function log($text)
    {
        $verbosity = $this->getOutput()->getVerbosity();

        if ($verbosity > 1) {
            $this->line($text);
        }
    }

    private function write($text)
    {
        $verbosity = $this->getOutput()->getVerbosity();

        if ($verbosity > 1) {
            $this->line($text);
        } else {
            $this->getOutput()->write($text);
        }
    }

    private function launch($command, array $arguments = [])
    {
        $verbosity = $this->getOutput()->getVerbosity();

        if ($verbosity > 1) {
            return $this->call($command, $arguments);
        }

        return $this->callSilent($command, $arguments);
    }

    public function launchAndCapture($command, &$capture, array $arguments = [])
    {
        $instance = $this->getApplication()->find($command);

        $arguments['command'] = $command;

        $out = new BufferedOutput;

        $res = $instance->run(new ArrayInput($arguments), $out);

        $capture = $out->pluck();

        return $res;
    }
}
