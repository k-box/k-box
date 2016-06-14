<?php namespace KlinkDMS\Traits;

use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * 
 */
trait RunCommand
{
    protected function runCommand($command, $input = [], $output = null)
    {
        if(is_null($output)){
             $output = new NullOutput;
        }
        
        return $command->run(new ArrayInput($input), $output);
    }
}
