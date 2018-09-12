<?php

namespace KBox\Geo;

use OneOffTech\GeoServer\GeoType as BaseGeoType;

/**
 * The geographical data type: vector or raster
 */
final class GeoType
{
    /**
     * Vector data
     */
    const VECTOR = BaseGeoType::VECTOR;

    /**
     * Raster data
     */
    const RASTER = BaseGeoType::RASTER;
}
