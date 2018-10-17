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
        $this->view_data['providers'] = collect($mapConfig['providers'])->where('enable', true)->toArray();
        $this->view_data['defaultProvider'] = $mapConfig['providers'][$mapConfig['default']]['label'] ?? $mapConfig['default'];

        // Get the WMS base url for the file
        

        // latitude, longitude
        // in the geoserver they seems to be inverted!
        $boundingBox = collect($file->properties->get('boundings.geoserver', []));
        $canBeFoundOnGeoserver = !is_null($file->properties->get('geoserver.store', null));
        
        if($boundingBox->count() === 5){
            // Y === Longitude, X = latitude
            list($bounds, $center) = $this->geoserverBoundingsToLeaflet($boundingBox);
        }
        else {
            $boundingBox = $file->properties->get('boundings.geojson', null);
            list($bounds, $center) = $this->geojsonBoundingBoxToLeaflet($boundingBox);
        }


        $this->view_data['center'] = $center ?? null;
        $this->view_data['boundings'] = $bounds ?? null;
        $this->view_data['layers'] = join(',', $file->properties->get('geoserver.layers', []));
        $this->view_data['styles'] = "";
        $this->view_data['attribution'] = "$file->name {$file->properties->get('geoserver.store', '')}";
        $this->view_data['geojson'] = !$canBeFoundOnGeoserver ? $this->getFileContentAsGeoJson($file) : null;
        $this->view_data['geoserver'] = $canBeFoundOnGeoserver ? $this->geoservice->wmsBaseUrl() : null;
        $this->view_data['disableTiling'] = $canBeFoundOnGeoserver ? $this->requiresBandStretching($file) : false;

        return $this;
    }

    
    /**
     * Check the bands metadata to see if there is at least one band whose type is not Byte
     */
    private function requiresBandStretching($file)
    {
        $bands = $file->properties->bands ?? [];

        // check if there are bands with type different than Byte
        $filtered_bands = array_filter($bands, function($band){
            return isset($band['type']) && $band['type'] !== 'Byte';
        });

        return !empty($filtered_bands);
    }

    private function geoserverBoundingsToLeaflet($boundingBox)
    {
        $bounds = sprintf('[[%1$s, %2$s], [%3$s, %4$s]]', $boundingBox->get('minY'), $boundingBox->get('minX'), $boundingBox->get('maxY'), $boundingBox->get('maxX'));
        $center = sprintf('[%s,%s]', ($boundingBox->get('minY') + $boundingBox->get('maxY')) / 2, ($boundingBox->get('minX') + $boundingBox->get('maxX')) / 2);

        return [$bounds, $center];
    }

    private function geojsonBoundingBoxToLeaflet($json)
    {
        $geometry = app('geometry')->parseGeoJson($json);

        $bbox = $geometry->getBBox();
        $centroid = $geometry->centroid()->asArray();
        
        $bounds = sprintf('[[%1$s, %2$s], [%3$s, %4$s]]', $bbox['miny'], $bbox['minx'], $bbox['maxy'], $bbox['maxx']);
        $center = sprintf('[%s,%s]', $centroid[0], $centroid[1]);

        return [$bounds, $center];
    }

    private function getFileContentAsGeoJson($file)
    {
        if($file->size > 1024 * 1024){
            return null;
        }

        $geometry = app('geometry');

        if($file->mime_type === 'application/gpx+xml'){

            return $geometry->parseGpx(file_get_contents($file->absolute_path))->toGeoJson();
        }
        else if($file->mime_type === 'application/geo+json'){

            return file_get_contents($file->absolute_path);
        }

        return null;
    }

    public function supportedMimeTypes()
    {
        return [
            'application/vnd.google-earth.kml+xml',
            'application/vnd.google-earth.kmz',
            'application/geopackage+sqlite3',
            'application/octet-stream',
            'application/geo+json',
            'application/gpx+xml',
            'application/zip',
            'image/tiff',
        ];
    }
}
