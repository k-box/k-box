<?php

namespace Tests\Plugins\Geo\Unit;

use Tests\TestCase;
use KBox\Geo\TypeIdentifiers\GeoJsonTypeIdentifier;
use KBox\Documents\DocumentType;
use KBox\Documents\TypeIdentification;

class GeoJsonTypeIdentifierTest extends TestCase
{
    public function test_geojson_is_recognized_from_plain_json_file()
    {
        $path = base_path('tests/data/geojson-in-plain-json.json');

        $identification = (new GeoJsonTypeIdentifier())->identify($path, new TypeIdentification('text/plain', DocumentType::TEXT_DOCUMENT));

        $this->assertInstanceOf(TypeIdentification::class, $identification);
        $this->assertEquals(DocumentType::GEODATA, $identification->documentType);
        $this->assertEquals('application/geo+json', $identification->mimeType);
    }
    
    public function test_geojson_is_recognized()
    {
        $path = base_path('tests/data/geojson.geojson');

        $identification = (new GeoJsonTypeIdentifier())->identify($path, new TypeIdentification('application/json', DocumentType::CODE));

        $this->assertInstanceOf(TypeIdentification::class, $identification);
        $this->assertEquals(DocumentType::GEODATA, $identification->documentType);
        $this->assertEquals('application/geo+json', $identification->mimeType);
    }
}
