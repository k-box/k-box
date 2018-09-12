# Geographical Extension

This plugin provides support for geographical data, e.g. GeoJson, KML, Shapefile. 

> **This is highly experimental**, the plugin must be enabled from the Administration > Plugins section. Plugin support can be enabled with `php artisan flags plugins`

## Supported files

* [x] Shapefile
* [x] Shapefile in a zip container
* [x] GeoTIFF
* [ ] GeoJSON
* [ ] KML and KMZ
* [ ] GPX

## Offered features

* [x] File upload and detection of geographical files
* [x] Thumbnail generation
* [x] Metadata extraction
* [ ] Delivery of WMS endpoints for each file for preview
* [ ] Conversion of files between formats for download

## Requirements

The plugin requires 

- a working [GeoServer](http://geoserver.org/) instance v2.13.0 or above

## Specific Geoserver configurations

- For the beta and development period, the WMS service of the GeoServer must be browsable without authentication

