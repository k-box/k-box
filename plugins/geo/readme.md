# Geographic Data Extension

This plugin provides support for geographical data, e.g. GeoJson, KML, Shapefile. 

> **This is highly experimental**, the plugin must be enabled from the Administration > Plugins section. Plugin support can be enabled with `php artisan flags plugins`

## Supported files

* [x] Shapefile
* [x] Shapefile in a zip container
* [x] GeoTIFF
* [x] GeoJSON
* [x] KML and KMZ
* [x] GPX
* [x] [GeoPackage 1.2](http://www.geopackage.org/spec120/)

## Offered features

* [x] File upload and detection of geographical files
* [x] Thumbnail generation
* [x] Metadata extraction
* [x] Delivery of WMS endpoints for each file for preview
* [ ] Conversion of files between formats for download

## Requirements

The plugin requires 

- a working [GeoServer](http://geoserver.org/) instance v2.13.0 or above
- [GDAL 2.1.0](https://www.gdal.org/index.html) or above (See [Gdal Installation](#gdal-installation) for more details)

## Specific Geoserver configurations

- For the beta and development period, the WMS service of the GeoServer must be browsable without authentication


## GDAL installation

The [GDAL library](https://www.gdal.org/index.html) is required to pre-process and convert geographic files.

As of now only Linux and Windows versions are supported.

### Linux

Install the library using the package manager, as indicated on the [Gdal Download page](https://trac.osgeo.org/gdal/wiki/DownloadingGdalBinaries)

### Windows

The Gdal project do not offer precompiled Windows binaries, but a compiled version can be found in the [GisInternals map server release](http://www.gisinternals.com/release.php). 

To install download the latest package available in zip format, e.g. [`release-1911-x64-gdal-2-3-1-mapserver-7-2-0/release-1911-x64-gdal-2-3-1-mapserver-7-2-0.zip`](http://download.gisinternals.com/sdk/downloads/release-1911-x64-gdal-2-3-1-mapserver-7-2-0.zip).

Once downloaded, extract the content of the zip file in the `plugins/geo/bin` directory.


