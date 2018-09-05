<?php

namespace Tests\Plugins\Geo\Unit;

use Tests\TestCase;
use KBox\Geo\GeoFile;
use KBox\Geo\GeoType;
use KBox\Geo\GeoFormat;

class GeoFileTest extends TestCase
{
    public function supported_files()
    {
        return [
            [__DIR__.'/../../../data/shapefile.shp'],
            [__DIR__.'/../../../data/shapefile.zip'],
            [__DIR__.'/../../../data/geojson.geojson'],
            [__DIR__.'/../../../data/geojson-in-plain-json.json'],
            [__DIR__.'/../../../data/kml.kml'],
            [__DIR__.'/../../../data/kmz.kmz'],
            [__DIR__.'/../../../data/gpx.gpx'],
            [__DIR__.'/../../../data/geotiff.tiff'],
        ];
    }
    
    public function unsupported_files()
    {
        return [
            [__DIR__.'/../../../data/plain.json'],
            [__DIR__.'/../../../data/plain.zip'],
            [__DIR__.'/../../../data/tiff.tif'],
        ];
    }

    /**
     * @dataProvider supported_files
     */
    public function test_supported_function_identifies_supported_files($file)
    {
        $this->assertTrue(GeoFile::isSupported($file));
    }

    /**
     * @dataProvider unsupported_files
     */
    public function test_supported_function_reject_unsupported_files($file)
    {
        $this->assertFalse(GeoFile::isSupported($file));
    }

    public function test_shapefile_is_recognized()
    {
        $file = GeoFile::from(__DIR__.'/../../../data/shapefile.shp');

        $this->assertInstanceOf(GeoFile::class, $file);
        $this->assertEquals(GeoFormat::SHAPEFILE, $file->format);
        $this->assertEquals(GeoType::VECTOR, $file->type);
        $this->assertEquals('application/octet-stream', $file->mimeType);
        $this->assertEquals('shp', $file->extension);
        $this->assertEquals('shapefile.shp', $file->name);
        $this->assertEquals($file->originalName, $file->name);
    }
    
    public function test_shapefile_packed_in_zip_is_recognized()
    {
        $file = GeoFile::from(__DIR__.'/../../../data/shapefile.zip');

        $this->assertInstanceOf(GeoFile::class, $file);
        $this->assertEquals(GeoFormat::SHAPEFILE_ZIP, $file->format);
        $this->assertEquals(GeoType::VECTOR, $file->type);
        $this->assertEquals('application/zip', $file->mimeType);
        $this->assertEquals('zip', $file->extension);
        $this->assertEquals('shapefile.zip', $file->name);
        $this->assertEquals($file->originalName, $file->name);
    }
    
    public function test_geojson_is_recognized_from_plain_json_file()
    {
        $file = GeoFile::from(__DIR__.'/../../../data/geojson-in-plain-json.json');

        $this->assertInstanceOf(GeoFile::class, $file);
        $this->assertEquals(GeoFormat::GEOJSON, $file->format);
        $this->assertEquals(GeoType::VECTOR, $file->type);
        $this->assertEquals('application/geo+json', $file->mimeType);
        $this->assertEquals('json', $file->extension);
        $this->assertEquals('geojson-in-plain-json.json', $file->name);
        $this->assertEquals($file->originalName, $file->name);
    }
    
    public function test_geojson_is_recognized()
    {
        $file = GeoFile::from(__DIR__.'/../../../data/geojson.geojson');

        $this->assertInstanceOf(GeoFile::class, $file);
        $this->assertEquals(GeoFormat::GEOJSON, $file->format);
        $this->assertEquals(GeoType::VECTOR, $file->type);
        $this->assertEquals('application/geo+json', $file->mimeType);
        $this->assertEquals('geojson', $file->extension);
        $this->assertEquals('geojson.geojson', $file->name);
        $this->assertEquals($file->originalName, $file->name);
    }
    
    public function test_kml_is_recognized()
    {
        $file = GeoFile::from(__DIR__.'/../../../data/kml.kml');

        $this->assertInstanceOf(GeoFile::class, $file);
        $this->assertEquals(GeoFormat::KML, $file->format);
        $this->assertEquals(GeoType::VECTOR, $file->type);
        $this->assertEquals('application/vnd.google-earth.kml+xml', $file->mimeType);
        $this->assertEquals('kml', $file->extension);
        $this->assertEquals('kml.kml', $file->name);
        $this->assertEquals($file->originalName, $file->name);
    }

    public function test_kmz_is_recognized()
    {
        $file = GeoFile::from(__DIR__.'/../../../data/kmz.kmz');

        $this->assertInstanceOf(GeoFile::class, $file);
        $this->assertEquals(GeoFormat::KMZ, $file->format);
        $this->assertEquals(GeoType::VECTOR, $file->type);
        $this->assertEquals('application/vnd.google-earth.kmz', $file->mimeType);
        $this->assertEquals('kmz', $file->extension);
        $this->assertEquals('kmz.kmz', $file->name);
        $this->assertEquals($file->originalName, $file->name);
    }

    public function test_gpx_is_recognized()
    {
        $file = GeoFile::from(__DIR__.'/../../../data/gpx.gpx');

        $this->assertInstanceOf(GeoFile::class, $file);
        $this->assertEquals(GeoFormat::GPX, $file->format);
        $this->assertEquals(GeoType::VECTOR, $file->type);
        $this->assertEquals('application/gpx+xml', $file->mimeType);
        $this->assertEquals('gpx', $file->extension);
        $this->assertEquals('gpx.gpx', $file->name);
        $this->assertEquals($file->originalName, $file->name);
    }

    public function test_geotiff_is_recognized()
    {
        $file = GeoFile::from(__DIR__.'/../../../data/geotiff.tiff');

        $this->assertInstanceOf(GeoFile::class, $file);
        $this->assertEquals(GeoFormat::GEOTIFF, $file->format);
        $this->assertEquals(GeoType::RASTER, $file->type);
        $this->assertEquals('image/tiff', $file->mimeType);
        $this->assertEquals('tiff', $file->extension);
        $this->assertEquals('geotiff.tiff', $file->name);
        $this->assertEquals($file->originalName, $file->name);
    }
}
