<?php

namespace Tests\Plugins\Geo\Unit;

use SplFileInfo;
use Tests\TestCase;
use KBox\Geo\Gdal\Gdal;
use KBox\Geo\GeoProperties;

class GdalTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        if (! (new Gdal())->isInstalled()) {
            $this->markTestSkipped(
                'GDal library not installed the system.'
            );
        }
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
        $file = base_path("tests/data/geojson.geojson");

        $gdal = new Gdal();

        $info = $gdal->info($file);

        $this->assertInstanceOf(GeoProperties::class, $info);
        $this->assertEquals('vector', $info->type);
        $this->assertEquals("WGS84", $info->get('crs.label'));
        $this->assertEquals(["geojson"], $info->layers);
        $this->assertEquals("POLYGON((-80.870885 35.215151,-80.703248 35.215151,-80.703248 35.401487,-80.870885 35.215151))", $info->{"boundings.wkt"});
        $this->assertEquals("(-80.870885, 35.215151) - (-80.703248, 35.401487)", $info->{"boundings.original"});
        $this->assertNotEmpty($info->{"boundings.geojson"});
    }
    
    public function test_gpx_vector_info()
    {
        $file = base_path("tests/data/gpx.gpx");

        $gdal = new Gdal();

        $info = $gdal->info($file);

        $this->assertInstanceOf(GeoProperties::class, $info);
        $this->assertEquals('vector', $info->type);
        $this->assertEquals("WGS84", $info->get('crs.label'));
        $this->assertEquals(["track_points"], $info->layers);
        $this->assertEquals("POLYGON((-122.326897 47.644548,-122.326897 47.644548,-122.326897 47.644548,-122.326897 47.644548))", $info->{"boundings.wkt"});
        $this->assertEquals("(-122.326897, 47.644548) - (-122.326897, 47.644548)", $info->{"boundings.original"});
        $this->assertNotEmpty($info->{"boundings.geojson"});
    }
    
    public function test_kml_vector_info()
    {
        $file = base_path("tests/data/kml.kml");

        $gdal = new Gdal();

        $info = $gdal->info($file);

        $this->assertInstanceOf(GeoProperties::class, $info);
        $this->assertEquals('vector', $info->type);
        $this->assertEquals("WGS84", $info->get('crs.label'));
        $this->assertEquals(["Highlighted Icon"], $info->layers);
        $this->assertEquals("POLYGON((-122.085655 37.422431,-122.085655 37.422431,-122.085655 37.422431,-122.085655 37.422431))", $info->{"boundings.wkt"});
        $this->assertEquals("(-122.085655, 37.422431) - (-122.085655, 37.422431)", $info->{"boundings.original"});
        $this->assertNotEmpty($info->{"boundings.geojson"});
    }
    
    public function test_kmz_vector_info()
    {
        $file = base_path("tests/data/kmz.kmz");

        $gdal = new Gdal();

        $info = $gdal->info($file);

        $this->assertInstanceOf(GeoProperties::class, $info);
        $this->assertEquals('vector', $info->type);
        $this->assertEquals(["weather"], $info->layers);
        $this->assertEquals("POLYGON((-218.526285 -57.474374,196.504859 -57.474374,196.504859 72.085834,-218.526285 -57.474374))", $info->{"boundings.wkt"});
        $this->assertEquals("(-218.526285, -57.474374) - (196.504859, 72.085834)", $info->{"boundings.original"});
        $this->assertNotEmpty($info->{"boundings.geojson"});
    }

    public function test_convert_to_pdf()
    {
        $file = base_path("tests/data/kmz.kmz");

        $gdal = new Gdal();

        $pdf = $gdal->convert($file, Gdal::FORMAT_PDF);
        
        $this->assertInstanceOf(SplFileInfo::class, $pdf);

        // read the file magic number to check if is a real pdf
        $handle = fopen($pdf->getRealPath(), 'rb');
        $data = fread($handle, 1);
        $data2 = fread($handle, 1);
        $data3 = fread($handle, 1);
        $data4 = fread($handle, 1);
        fclose($handle);
        unlink($pdf->getRealPath());

        $magicNumber = current(unpack('a', $data)).current(unpack('a', $data2)).current(unpack('a', $data3)).current(unpack('a', $data4));

        $this->assertEquals('%PDF', $magicNumber);
    }
}
