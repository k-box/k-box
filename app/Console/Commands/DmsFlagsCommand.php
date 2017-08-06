<?php

namespace KlinkDMS\Console\Commands;

use Illuminate\Console\Command;
use InvalidArgumentException;
use KlinkDMS\Flags;

/**
 * Command for enabling or disabling feature flags.
 * Feature flags protects the work in progress of a feature we want
 * to apply only to specific instance
 */
class DmsFlagsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dms:flags {--enable : enable a flag option}{--disable : disable a flag option} {flag : the flag name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable or Disable a feature flag';

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
        $flag = $this->argument('flag');

        $enable = $this->option('enable');

        $disable = $this->option('disable');

        if ($enable && $disable) {
            throw new InvalidArgumentException('Option --enable and --disable cannot be used together.');
        }

        if (! $enable && ! $disable) {
            $is_now_enabled = Flags::toggle($flag);

            $this->line("Flag <comment>$flag</comment> is now <info>".($is_now_enabled ? 'enabled' : 'disabled')."</info>.");
        } elseif ($enable) {
            Flags::enable($flag);
            $this->line("Flag <comment>$flag</comment> is now <info>enabled</info>.");
        } elseif ($disable) {
            Flags::disable($flag);
            $this->line("Flag <comment>$flag</comment> is now <info>disabled</info>.");
        }

        return 0;
    }
}
