<?php

namespace KBox\Geo\Jobs;

use Log;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RemoveStoreFromGeoserver implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $name = null;
    public $type = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($name, $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("Deleting [{$this->name}] [{$this->type}] from geoserver");
            
        if($data->type === GeoType::VECTOR){
            $this->service->connection()->deleteDatastore($this->name);
        }
        else {
            $this->service->connection()->deleteCoveragestore($this->name);
        }
        
        Log::info("Delete [{$this->name}] [{$this->type}] from geoserver completed");
    }
}
