<?php

namespace Tests\Plugins\Geo\Unit;

use Tests\TestCase;
use KBox\Documents\DocumentType;
use KBox\Documents\TypeIdentification;
use KBox\Geo\TypeIdentifiers\GeoTiffTypeIdentifier;
use KBox\Geo\TypeIdentifiers\ShapefileTypeIdentifier;

class GeoserverTypeIdentifierTest extends TestCase
{
    public function test_shapefile_is_recognized()
    {
        $path = base_path('tests/data/shapefile.shp');

        $identification = (new ShapefileTypeIdentifier())->identify($path, new TypeIdentification('application/octet-stream', DocumentType::BINARY));

        $this->assertInstanceOf(TypeIdentification::class, $identification);
        $this->assertEquals(DocumentType::GEODATA, $identification->documentType);
        $this->assertEquals('application/octet-stream', $identification->mimeType);
    }
    
    public function test_shapefile_zip_is_recognized()
    {
        $path = base_path('tests/data/shapefile.zip');

        $identification = (new ShapefileTypeIdentifier())->identify($path, new TypeIdentification('application/octet-stream', DocumentType::BINARY));

        $this->assertInstanceOf(TypeIdentification::class, $identification);
        $this->assertEquals(DocumentType::GEODATA, $identification->documentType);
        $this->assertEquals('application/zip', $identification->mimeType);
    }
    
    public function test_geotiff_is_recognized()
    {
        $path = base_path('tests/data/geotiff.tiff');

        $identification = (new GeoTiffTypeIdentifier())->identify($path, new TypeIdentification('image/tiff', DocumentType::IMAGE));

        $this->assertInstanceOf(TypeIdentification::class, $identification);
        $this->assertEquals(DocumentType::GEODATA, $identification->documentType);
        $this->assertEquals('image/tiff', $identification->mimeType);
    }
}
