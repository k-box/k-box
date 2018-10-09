<?php

namespace KBox\Geo\Actions;

use Log;
use Exception;
use KBox\Geo\GeoService;
use Klink\DmsAdapter\Geometries;
use OneOffTech\GeoServer\GeoFile;
use OneOffTech\GeoServer\GeoType;
use KBox\Jobs\ThumbnailGenerationJob;
use KBox\Geo\Exceptions\FileConversionException;
use KBox\Geo\Exceptions\GeoServerUnsupportedFileException;
use OneOffTech\GeoServer\Exception\ErrorResponseException;
use OneOffTech\GeoServer\Exception\GeoServerClientException;

use KBox\Contracts\Action;

/**
 * Process geographical files. 
 * Extract properties and send files to the configured Geoserver
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

            }catch(FileConversionException $ex){
                $details = null;
                
                Log::error("Conversion to shapefile before upload to geoserver failed for [$descriptor->uuid:{$file->uuid}]. {$ex->getMessage()}");

            }catch(ErrorResponseException $ex){
                $details = null;
                
                Log::error("Upload to geoserver failed for [$descriptor->uuid:{$file->uuid}]. {$ex->getMessage()}");
            }
            
            Log::info("Saving geo properties for: [$descriptor->uuid:{$file->uuid}]");
        
            // the default layer name, also useful for the WMS service is the store name
            $baseLayer = optional($details)->store['name'] ?? [];
            if($details){
                $geoserverCrs = $details->type() === 'raster' ? $details->nativeCRS : ($details->srs ?? $details->boundingBox->crs);
            }
                    
            $properties = $geofile->properties()->merge([
                'type' => $properties->type ?? optional($details)->type(), //although it might be already set, we add it twice in case Gdal extraction fails
                'crs.geoserver' => $geoserverCrs ?? '',
                'boundings.geoserver' => optional($details)->boundingBox ?? [],
                'geoserver.layers' => array_wrap($baseLayer),
                'geoserver.store' => optional($details)->name,
            ]);

            // Gdal bounding box extraction might fail, so if we have a succesfull geoserver upload we use the Geoserver calculated bounding box
            if(empty($properties->get('boundings.geojson'))){
                $properties['boundings.geojson'] = Geometries::boundingBoxFromGeoserver($properties->get('boundings.geoserver', null));
            }

            $file->properties = $properties;

            $file->save();

            // Dispatch again the thumbnail generation for shapefile as
            // geoserver upload is required to use the thumbnail feature
            if($properties->type === GeoType::VECTOR && in_array($file->mime_type, ['application/octet-stream', 'application/zip'])){
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
