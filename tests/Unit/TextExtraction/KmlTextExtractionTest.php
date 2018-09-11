<?php

namespace Tests\Unit\TextExtraction;

use Tests\TestCase;
use KBox\Documents\ExtractText\KmlExtractor;

class KmlTextExtractionTest extends TestCase
{
    public function test_can_extract_text_from_kml_file()
    {
        $extractor = new KmlExtractor();

        $file = base_path('tests/data/kml-example-short.kml');

        $this->assertEquals('KML Samples Placemarks Simple placemark Styles and Markup Highlighted Icon Roll over this icon Descriptive HTML Unleash your creativity with the help of these examples!', $extractor->load($file)->text());
    }
}
