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

        if($this->service->isEnabled() && $this->service->isSupported($descriptor->file)){

            Log::info("Uploading [$descriptor->uuid:{$descriptor->file->uuid}] to geoserver");
            
            $details = $this->service->upload($descriptor->file);

            // TODO:
            // Eventually add additional properties to the DocumentDescriptor here, as the upload 
            // method returns some more details of the file
            
            Log::info("Upload of [$descriptor->uuid:{$descriptor->file->uuid}] completed", compact('details'));
        }

        return $descriptor;
    }
}
