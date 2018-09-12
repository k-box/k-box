<?php

namespace Tests\Plugins\Geo\Unit;

use Tests\TestCase;
use KBox\Geo\Providers\GeoServiceProvider;
use KBox\Documents\Facades\Files;
use KBox\Documents\Facades\Thumbnails;

class GeoServiceProviderTest extends TestCase
{
    public function test_provider_register_file_types()
    {
        $provider = new GeoServiceProvider(app());

        $provider->register();
        
        $this->assertContains([
            "accept" => [
                "text/plain",
                "application/json",
                "application/geo+json",
            ],
            "priority" => 1,
            "mime" => "application/geo+json",
            "doc" => "geodata",
            "extension" => "geojson",
            "identifier" => "KBox\Geo\TypeIdentifiers\GeoJsonTypeIdentifier"
        ], Files::identifiers());
        
        $this->assertContains([
            "accept" => [
                "application/zip",
                "application/vnd.google-earth.kml+xml",
                "application/vnd.google-earth.kmz",
              ],
              "priority" => 1,
              "mime" => "application/vnd.google-earth.kml+xml",
              "doc" => "geodata",
              "extension" => "kml",
              "identifier" => "KBox\Geo\TypeIdentifiers\KmlTypeIdentifier"
        ], Files::identifiers());
        
        $this->assertContains([
            "accept" => [
                "application/zip",
                "application/vnd.google-earth.kml+xml",
                "application/vnd.google-earth.kmz",
              ],
              "priority" => 1,
              "mime" => "application/vnd.google-earth.kmz",
              "doc" => "geodata",
              "extension" => "kmz",
              "identifier" => "KBox\Geo\TypeIdentifiers\KmlTypeIdentifier"
        ], Files::identifiers());
        
        $this->assertContains([
            "accept" => [
                "application/xml",
                "application/gpx+xml",
              ],
              "priority" => 1,
              "mime" => "application/gpx+xml",
              "doc" => "geodata",
              "extension" => "gpx",
              "identifier" => "KBox\Geo\TypeIdentifiers\GpxTypeIdentifier",
        ], Files::identifiers());

        $this->assertContains([
            "accept" => [
              "application/octet-stream",
              "application/zip"
            ],
            "priority" => 5,
            "mime" => "application/octet-stream",
            "doc" => "geodata",
            "extension" => "shp",
            "identifier" => "KBox\Geo\TypeIdentifiers\ShapefileTypeIdentifier"
        ], Files::identifiers());

        $this->assertContains([
            "accept" => [
              "image/tiff",
            ],
            "priority" => 5,
            "mime" => "image/tiff",
            "doc" => "geodata",
            "extension" => "tiff",
            "identifier" => "KBox\Geo\TypeIdentifiers\GeoTiffTypeIdentifier"
          ], Files::identifiers());

        $this->assertContains([
            "accept" => [
              "application/octet-stream",
              "application/zip"
            ],
            "priority" => 5,
            "mime" => "application/zip",
            "doc" => "geodata",
            "extension" => "zip",
            "identifier" => "KBox\Geo\TypeIdentifiers\ShapefileTypeIdentifier"
          ], Files::identifiers());
    }

    public function test_thumbnail_generators_are_registered()
    {
        $provider = new GeoServiceProvider(app());

        $provider->register();

        $this->assertContains('image/tiff', Thumbnails::supportedMimeTypes());
    }
}
