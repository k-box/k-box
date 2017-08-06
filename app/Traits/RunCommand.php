<?php

namespace KlinkDMS\Traits;

use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * Add the support of executing an Artisan command
 * by its class
 */
trait RunCommand
{
    /**
     * Execute an artisan command
     *
     * @param Illuminate\Console\Command $command the command instance to be executed
     * @param array $input the input arguments and options of the command
     * @param Symfony\Component\Console\Output\BufferedOutput $output
     * @return int the command return code
     */
    protected function runCommand($command, $input = [], $output = null)
    {
        if (is_null($output)) {
            $output = new NullOutput;
        }
        
        return $command->run(new ArrayInput($input), $output);
    }
}
