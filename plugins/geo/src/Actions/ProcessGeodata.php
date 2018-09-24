<?php

namespace KBox\Geo\Actions;

use Log;
use Exception;
use KBox\Geo\GeoService;
use OneOffTech\GeoServer\GeoFile;
use OneOffTech\GeoServer\GeoType;
use KBox\Jobs\ThumbnailGenerationJob;
use KBox\Geo\Exceptions\GeoServerUnsupportedFileException;
use OneOffTech\GeoServer\Exception\GeoServerClientException;

use KBox\Contracts\Action;

/**
 * Process geographical files to extract properties and
 * send them to the configured Geoserver
 */
class ProcessGeodata extends Action
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

            Log::info("$descriptor->uuid is a geographic file.");

            $file = $descriptor->file;

            $geofile = $this->service->asGeoFile($file);

            Log::info("Uploading [$descriptor->uuid:{$file->uuid}] to geoserver");
            
            try {
                $details = $this->service->upload($geofile);

                Log::info("Upload of [$descriptor->uuid:{$file->uuid}] completed", compact('details'));
                
            }catch(GeoServerUnsupportedFileException $ex){
                $details = null;
                
                Log::warning("Upload of [$descriptor->uuid:{$file->uuid}] to geoserver not supported. {$ex->getMessage()}");
            }
            
            Log::info("Saving geo properties for: [$descriptor->uuid:{$file->uuid}]");
        
            // the default layer name, also useful for the WMS service is the store name
            $baseLayer = optional($details)->store['name'] ?? [];
            $geoserverCrs = $details->type() === 'raster' ? optional($details)->nativeCRS : (optional($details)->srs ?? optional($details)->boundingBox->crs);
                    
            $file->properties = $geofile->properties()->merge([
                'crs.geoserver' => $geoserverCrs ?? '',
                'boundings.geoserver' => optional($details)->boundingBox ?? [],
                'geoserver.layers' => array_wrap($baseLayer),
                'geoserver.store' => optional($details)->name,
            ]);
            
            $file->save();

            // Dispatch again the thumbnail generation for shapefile as
            // geoserver upload is required to use the thumbnail feature
            if($details->type() === GeoType::VECTOR && in_array($file->mime_type, ['application/octet-stream', 'application/zip'])){
                try{
                    dispatch_now(new ThumbnailGenerationJob($file));
                }catch(Exception $ex)
                {
                    Log::error('Thumbnail generation after geoserver file upload failed', ['error' => $ex]);
                }
            }
        }

        return $descriptor;
    }
}
