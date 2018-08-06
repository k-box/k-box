<?php

namespace KBox\Geo\Actions;

use Log;
use Exception;
use KBox\Geo\GeoService;
use OneOffTech\GeoServer\GeoFile;
use OneOffTech\GeoServer\Exception\GeoServerClientException;

use KBox\Contracts\Action;

class SyncWithGeoserver extends Action
{
    /**
     * @var \KBox\Geo\GeoService
     */
    private $service = null;
    
    protected $canFail = true;
     
    /**
     * Create the action.
     *
     * @return void
     */
    public function __construct(GeoService $service)
    {
        $this->service = $service;
    }

    /**
     * Execute the action over the DocumentDescriptor
     * 
     * @param \KBox\DocumentDescriptor $descriptor
     * @return \KBox\DocumentDescriptor
     */
    public function run($descriptor)
    {
        Log::info("Checking if $descriptor->uuid is a geographic file.");

        if($this->service->isEnabled() && GeoFile::isSupported($descriptor->file->absolute_path)){
            $data = GeoFile::from($descriptor->file->absolute_path)->name($descriptor->uuid);

            Log::info("Uploading $descriptor->uuid to geoserver", compact('data'));
            
            $feature = $this->service->connection()->upload($data);
            
            Log::info("Upload of $descriptor->uuid completed", compact('feature'));
        }

        return $descriptor;
    }
}
