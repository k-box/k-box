# Geographic Data Extension Changelog

All notable changes to this plugin will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/0.3.0/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

## [0.2.3] - 2019-02-19

### Fixed

- Additional fix for Geographic data section loading ([#210](https://github.com/k-box/k-box/pull/210))

## [0.2.2] - 2019-02-18

### Fixed

- Geographic documents loading error when using partner account ([#201](https://github.com/k-box/k-box/pull/201))


## [0.2.1] - 2018-11-20

### Fixed

- Authorization handling in WMS proxy request if file is in collection ([#179](https://github.com/k-box/k-box/pull/179))

## [0.2.0] - 2018-11-13

### Added

- Ability to delete map providers ([#147](https://github.com/k-box/k-box/pull/147))
- GeoPackage v1.2 format support ([#152](https://github.com/k-box/k-box/pull/152)) 
- Add control for layer opacity ([#150](https://github.com/k-box/k-box/pull/150)) 
- Search of Geographic data ([#140](https://github.com/k-box/k-box/pull/140))
- GeoServer WMS request proxy ([#164](https://github.com/k-box/k-box/pull/164)) 
- Ability to display feature information when clicking on a map preview ([#154](https://github.com/k-box/k-box/pull/154))

### Fixed

- Improve GeoTiff preview of greyscale image ([#153](https://github.com/k-box/k-box/pull/153)) 
- Improve GeoTiff thumbnail of greyscale image ([#149](https://github.com/k-box/k-box/pull/149)) 
- Improve UTF-8 support ([#162](https://github.com/k-box/k-box/pull/162)) 

## [0.1.0] - 2018-10-01

### Added

- Plugin settings page
- Plugin default configuration
- Connection to Geoserver for the Web Map Service
- Ability to configure providers for the map visualization
- File type detection for Shapefile, GeoTiff, GeoJSON, KML, KMZ and GPX formats
- File metadata extraction for Shapefile, GeoTiff, GeoJSON, KML, KMZ and GPX formats
- Preview driver for Shapefile, GeoTiff, GeoJSON, KML, KMZ and GPX formats
- Thumbnail driver for Shapefile, GeoTiff, GeoJSON, KML, KMZ and GPX formats
