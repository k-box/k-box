<?php

namespace KBox\Geo\Actions;

use Log;
use Exception;
use KBox\Geo\GeoService;
use OneOffTech\GeoServer\GeoFile;
use OneOffTech\GeoServer\GeoType;
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

            $file = $descriptor->file;

            Log::info("Uploading [$descriptor->uuid:{$file->uuid}] to geoserver");
            
            $details = $this->service->upload($file);
            
            Log::info("Upload of [$descriptor->uuid:{$file->uuid}] completed", compact('details'));
            
            Log::info("Saving properties returned by GeoServer for: [$descriptor->uuid:{$file->uuid}]");
        
            // the default layer name, also useful for the WMS service is the store name
            $baseLayer = $details->store['name'] ?? [];
                    
            $file->properties = [
                'coordinateSystem' => $details->srs ?? ($details->boundingBox->crs ?? $details->nativeCRS),
                'boundingBox' => $details->boundingBox,
                'layers' => array_wrap($baseLayer),
                'type' => $details->type(), //vector or raster
                'nameInGeoserver' => $details->name,
            ];
            
            $file->save();
        }

        return $descriptor;
    }
}
