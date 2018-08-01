<?php

namespace KBox\Console\Commands;

use Illuminate\Console\Command;
use InvalidArgumentException;
use KBox\Flags;

/**
 * Command for enabling or disabling feature flags.
 */
class FlagsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flags {--enable : enable a flag option}{--disable : disable a flag option} {flag* : the flag name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable or disable a feature flag';

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
        $flags = array_wrap($this->argument('flag'));

        $enable = $this->option('enable');

        $disable = $this->option('disable');

        if ($enable && $disable) {
            throw new InvalidArgumentException('Option --enable and --disable cannot be used together.');
        }

        if (! $enable && ! $disable) {
            $enable = true;
        }

        foreach ($flags as $flag) {
            if ($enable) {
                $this->enable($flag);
            } else {
                $this->disable($flag);
            }
        }

        return 0;
    }

    private function enable($flag)
    {
        Flags::enable($flag);
        $this->line("Flag $flag enabled.");
    }

    private function disable($flag)
    {
        Flags::disable($flag);
        $this->line("Flag $flag disabled.");
    }
}
