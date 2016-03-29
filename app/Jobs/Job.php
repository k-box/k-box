<?php namespace KlinkDMS\Jobs;

use Illuminate\Bus\Queueable;

abstract class Job
{
    /*
    |--------------------------------------------------------------------------
    | Queueable Jobs
    |--------------------------------------------------------------------------
    |
    | This job base class provides a central location to place any logic that
    | is shared across all of your jobs. The trait included with the class
    | provides access to the "onQueue" and "delay" queue helper methods.
    |
    */
    use Queueable;
    
    
    
    protected function fail(){

        \Log::warning('Job explicit Failure', ['job' => $this->job]);
        
        $failer = app()->make('queue.failer');
            
        if (property_exists($this, 'job') && !is_null($this->job) && $failer) {
            $failer->log('connection', is_null($this->job->getQueue()) ? 'default' : $this->job->getQueue(), $this->job->getRawBody());

            if(method_exists($this->job, 'failed')){
                $this->job->failed();
            }
        }
        
        $this->delete();

    }
}
