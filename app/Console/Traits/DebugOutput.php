<?php namespace KlinkDMS\Console\Traits;

/**
 * Add the debugLine method that print the text only if verbosity level is greater than 1
 *
 * Must be used inside a class that extends Illuminate\Console\Command
 */
trait DebugOutput
{
    
    
    function debugLine($text)
    {
        
        if( $this->getOutput()->getVerbosity() > 1 ){
            $this->line($text);
        }
        
    }
    
}
