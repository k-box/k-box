<?php

namespace KBox\Geo;

use KBox\FileProperties;
use KBox\Documents\Properties\Presenter;

/**
 * Presenter for GeoProperties
 * 
 */
final class GeoPropertiesPresenter extends Presenter
{
    protected $hide = [
        'crs.wkt',
        'crs.geoserver',
        'layers',
        'boundings.geoserver',
        'boundings.geojson',
        'boundings.wkt',
        'boundings.original',
    ];

    protected $cast = [
        'layers' => 'array'
    ];

    public function __construct(FileProperties $properties)
    {
        parent::__construct($properties);

        $this->title = trans('geo::properties.section');
        
    }
    

    public function toHtml()
    {
        return view('components.properties', [
            'title' => $this->title,
            'properties' => [
                trans('geo::properties.crs') => $this->properties->get('crs.label', ''),
                trans('geo::properties.type') => $this->properties->get('type', ''),
                trans('geo::properties.dimension') => trans('geo::properties.dimension_pixels', ['width' => $this->properties->get('dimension.width', ''), 'height' => $this->properties->get('dimension.height', '')]),
                trans('geo::properties.layers') => implode(',', $this->properties->get('layers', [])),
                trans('geo::properties.geoserver.store') => $this->properties->get('geoserver.store', ''),
            ]
        ]);
    }
}
