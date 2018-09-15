<?php

namespace KBox\Geo\Previews;

use KBox\File;
use KBox\Geo\GeoService;
use KBox\Documents\Preview\MapPreviewDriver;
use Illuminate\Contracts\Support\Renderable;

/**
 * Preview driver for shapefiles, GeoTIFF, KML, GPX and GeoJSON
 */
class GeodataPreviewDriver extends MapPreviewDriver
{
    
    private $geoservice = null;

    public function __construct()
    {
        $this->geoservice = app(GeoService::class);
    }

    public function preview(File $file) : Renderable
    {
        parent::preview($file);

        // Get the configured map providers
        $mapConfig = $this->geoservice->config('map');
        $this->view_data['providers'] = $mapConfig['providers'];
        $this->view_data['defaultProvider'] = $mapConfig['providers'][$mapConfig['default']]['label'] ?? $mapConfig['default'];

        // Get the WMS base url for the file
        $this->view_data['wmsBaseUrl'] = $this->geoservice->wmsBaseUrl();

        // latitude, longitude
        // in the geoserver they seems to be inverted!
        $boundingBox = collect($file->properties->get('boundingBox', []));
        
        if($boundingBox->count() === 5){
            $bounds = sprintf('[[%1$s, %2$s], [%3$s, %4$s]]', $boundingBox->get('minY'), $boundingBox->get('minX'), $boundingBox->get('maxY'), $boundingBox->get('maxX'));
            $center = sprintf('[%s,%s]', ($boundingBox->get('minY') + $boundingBox->get('maxY')) / 2, ($boundingBox->get('minX') + $boundingBox->get('maxX')) / 2);
        }

        $this->view_data['mapCenter'] = $center ?? '';
        $this->view_data['mapBoundings'] = $bounds ?? '';
        $this->view_data['layers'] = join(',', $file->properties->get('layers', [])); "kbox:9dde0eca-1e4a-4d5e-9320-e41e1bba750b";
        $this->view_data['styles'] = "";
        $this->view_data['attribution'] = "$file->name kbox:9dde0eca-1e4a-4d5e-9320-e41e1bba750b";

        return $this;
    }

    public function supportedMimeTypes()
    {
        return [
            'application/vnd.google-earth.kml+xml',
            'application/vnd.google-earth.kmz',
            'application/octet-stream',
            'application/geo+json',
            'application/gpx+xml',
            'application/zip',
            'image/tiff',
        ];
    }
}
