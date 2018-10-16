<?php

namespace KBox\Geo;

use OneOffTech\GeoServer\GeoFormat as BaseGeoFormat;

final class GeoFormat
{
    const SHAPEFILE_ZIP = BaseGeoFormat::SHAPEFILE_ZIP;
    const SHAPEFILE = BaseGeoFormat::SHAPEFILE;
    const GEOTIFF = BaseGeoFormat::GEOTIFF;
    const GEOPACKAGE = BaseGeoFormat::GEOPACKAGE;

    const GEOJSON = "geojson";
    const KML = "kml";
    const KMZ = "kmz";
    const GPX = "gpx";
}
