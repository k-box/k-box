<?php

namespace KlinkDMS\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class DmsTestConfiguration extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'dms:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will test the K-Link Core configuration and connection. (want experience more try to use -vvv)';

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
    public function fire()
    {
        $verbosity = $this->getOutput()->getVerbosity();

        $debug_enabled = ! ! $this->option('debug') || ($verbosity>2 && $verbosity<4);

        $core_address = \Config::get('dms.core.address');

        $core_one = new \KlinkAuthentication($core_address, \Config::get('dms.core.username'), \Config::get('dms.core.password'));

        $klink_config = new \KlinkConfiguration(\Config::get('dms.institutionID'), \Config::get('dms.identifier'), [$core_one]);

        if ($debug_enabled) {
            $klink_config->enableDebug();
        }

        $error = null;
        $health = null;

        $passed = \KlinkCoreClient::test($klink_config, $error, false, $health);

        if ($verbosity<4) {
            $this->line('');
            if (! $passed) {
                $this->error("K-Link Connection test failed.");
                $this->line('');
                $this->line($error->getMessage());
            } else {
                $this->line("Connection to $core_address <info>success</info>");
            }
            $this->line('');
        } else {
            $tag = $passed ? 'info':'error';

            $this->line('<'.$tag.'>  _  ___      _       _     </'.$tag.'>');
            $this->line('<'.$tag.'> | |/ / |    (_)     | |   '.' </'.$tag.'>');
            $this->line('<'.$tag.'> | \' /| |     _ _ __ | | __'.' </'.$tag.'>');
            $this->line('<'.$tag.'> |  < | |    | | \'_ \| |/ /'.' </'.$tag.'>');
            $this->line('<'.$tag.'> | . \| |____| | | | |   < '.' </'.$tag.'>');
            $this->line('<'.$tag.'> |_|\_\______|_|_| |_|_|\_\\'.' </'.$tag.'>');
            $this->line('<'.$tag.'>                            </'.$tag.'>');

            if (! $passed) {
                $this->line('');
                $this->line($error->getMessage());
            }
        }

        if (! $passed) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['debug', null, InputOption::VALUE_NONE, 'If the debug mode needs to be enabled, it\'s the same of -vv', null],
        ];
    }
}
