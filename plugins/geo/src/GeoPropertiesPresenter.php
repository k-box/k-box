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

        $presentable = [
            trans('geo::properties.crs') => $this->properties->get('crs.label', $this->properties->get('crs.geoserver', '')),
            trans('geo::properties.type') => $this->properties->get('type', ''),
            trans('geo::properties.geoserver.store') => $this->properties->get('geoserver.store', ''),
        ];

        if(! empty($this->properties->get('dimension.width', ''))){
            $presentable[trans('geo::properties.dimension')] = trans('geo::properties.dimension_pixels', ['width' => $this->properties->get('dimension.width', ''), 'height' => $this->properties->get('dimension.height', '')]);
        }

        return view('components.properties', [
            'title' => $this->title,
            'properties' => $presentable
        ]);
    }
}
