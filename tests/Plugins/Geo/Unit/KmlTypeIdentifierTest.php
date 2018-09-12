<?php

namespace Tests\Plugins\Geo\Unit;

use Tests\TestCase;
use KBox\Geo\TypeIdentifiers\KmlTypeIdentifier;
use KBox\Documents\DocumentType;
use KBox\Documents\TypeIdentification;

class KmlTypeIdentifierTest extends TestCase
{
    public function test_kml_is_recognized()
    {
        $path = base_path('tests/data/kml.kml');

        $identification = (new KmlTypeIdentifier())->identify($path, new TypeIdentification('application/xml', DocumentType::CODE));

        $this->assertInstanceOf(TypeIdentification::class, $identification);
        $this->assertEquals(DocumentType::GEODATA, $identification->documentType);
        $this->assertEquals('application/vnd.google-earth.kml+xml', $identification->mimeType);
    }
    
    public function test_kmz_is_recognized()
    {
        $path = base_path('tests/data/kmz.kmz');

        $identification = (new KmlTypeIdentifier())->identify($path, new TypeIdentification('application/zip', DocumentType::ARCHIVE));

        $this->assertInstanceOf(TypeIdentification::class, $identification);
        $this->assertEquals(DocumentType::GEODATA, $identification->documentType);
        $this->assertEquals('application/vnd.google-earth.kmz', $identification->mimeType);
    }
}
