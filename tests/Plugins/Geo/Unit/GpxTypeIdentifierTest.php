<?php

namespace Tests\Plugins\Geo\Unit;

use Tests\TestCase;
use KBox\Geo\TypeIdentifiers\GpxTypeIdentifier;
use KBox\Documents\DocumentType;
use KBox\Documents\TypeIdentification;

class GpxTypeIdentifierTest extends TestCase
{
    public function test_gpx_is_recognized()
    {
        $path = base_path('tests/data/gpx.gpx');

        $identification = (new GpxTypeIdentifier())->identify($path, new TypeIdentification('application/xml', DocumentType::CODE));

        $this->assertInstanceOf(TypeIdentification::class, $identification);
        $this->assertEquals(DocumentType::GEODATA, $identification->documentType);
        $this->assertEquals('application/gpx+xml', $identification->mimeType);
    }
}
