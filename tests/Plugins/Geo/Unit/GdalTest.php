<?php

namespace Tests\Plugins\Geo\Unit;

use Tests\TestCase;
use KBox\Geo\Gdal\Gdal;
use KBox\Geo\GeoProperties;

class GdalTest extends TestCase
{
    protected function setUp()
    {
        if (! (new Gdal())->isInstalled()) {
            $this->markTestSkipped(
                'GDal library not installed the system.'
            );
        }

        parent::setUp();
    }

    public function test_gdal_version_returned()
    {
        $gdal = new Gdal();

        $version = $gdal->version();

        $this->assertNotEmpty($version);
        $this->assertStringMatchesFormat('GDAL%w%d.%d.%d%S', $version);
    }

    public function test_gdal_raster_info()
    {
        $file = base_path("tests/data/geotiff.tiff");

        $gdal = new Gdal();

        $info = $gdal->info($file);

        $this->assertInstanceOf(GeoProperties::class, $info);
        $this->assertEquals('raster', $info->type);
        $this->assertEquals(571, $info->get('dimension.width'));
        $this->assertEquals(658, $info->get('dimension.height'));
        $this->assertEquals("NAD83/GeorgiaEast", $info->get('crs.label'));
        $this->assertEquals([], $info->layers);
        $this->assertEquals(json_encode(["type" => "Polygon",
            "coordinates" => [[
            [-84.0696504,33.9418858],
            [-84.0680441,33.8695934],
            [-83.9928359,33.8707312],
            [-83.9943787,33.9430266],
            [-84.0696504,33.9418858]]]]), $info->{"boundings.geojson"});
    }
    
    public function test_geojson_vector_info()
    {
        $this->markTestSkipped('Vector properties not yet implemented.');

        $file = base_path("tests/data/geojson.geojson");

        $gdal = new Gdal();

        $info = $gdal->info($file);

        $this->assertInstanceOf(GeoProperties::class, $info);
        $this->assertEquals('vector', $info->type);
    }
    
    public function test_gpx_vector_info()
    {
        $this->markTestSkipped('Vector properties not yet implemented.');

        $file = base_path("tests/data/gpx.gpx");

        $gdal = new Gdal();

        $info = $gdal->info($file);

        $this->assertInstanceOf(GeoProperties::class, $info);
        $this->assertEquals('vector', $info->type);
    }
    
    public function test_kml_vector_info()
    {
        $this->markTestSkipped('Vector properties not yet implemented.');

        $file = base_path("tests/data/kml.kml");

        $gdal = new Gdal();

        $info = $gdal->info($file);

        $this->assertInstanceOf(GeoProperties::class, $info);
        $this->assertEquals('vector', $info->type);
    }
    
    public function test_kmz_vector_info()
    {
        $this->markTestSkipped('Vector properties not yet implemented.');

        $file = base_path("tests/data/kmz.kmz");

        $gdal = new Gdal();

        $info = $gdal->info($file);

        $this->assertInstanceOf(GeoProperties::class, $info);
        $this->assertEquals('vector', $info->type);
    }
}
