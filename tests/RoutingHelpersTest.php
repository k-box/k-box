<?php

use KBox\RoutingHelpers;

use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/*
 * Test the RoutingHelpers class
*/
class RoutingHelpersTest extends BrowserKitTestCase
{
    use DatabaseTransactions;
    
    
    public function filter_search_data_provider()
    {
        return [
            // $empty_url, $current_active_filters, $facet, $term, $selected, $expected_url
            ['', [], 'language', 'en', false, '?language=en'],
            ['?', [], 'language', 'en', false, '?language=en'],
            ['?s=pasture', [], 'language', 'en', false, '?s=pasture&language=en'],
            ['?s=pasture', ['language' => ['en']], 'language', 'en', true, '?s=pasture'],
            ['?s=pasture', [], 'properties.mime_type', 'image/svg+xml', true, '?s=pasture&properties.mime_type=image%2Fsvg%2Bxml'],
        ];
    }

    /**
     * @dataProvider filter_search_data_provider
     */
    public function testFilterSearch($empty_url, $current_active_filters, $facet, $term, $selected, $expected_url)
    {
        $url = RoutingHelpers::filterSearch($empty_url, $current_active_filters, $facet, $term, $selected);

        $this->assertEquals($expected_url, $url);
    }

    public function test_document_download_url_generation()
    {
        $user = $this->createAdminUser();
        $document = $this->createDocument($user);

        $url = RoutingHelpers::download($document);

        $this->assertStringEndsWith("/d/download/$document->uuid", $url);
        
        $url_with_version = RoutingHelpers::download($document, $document->file);

        $this->assertStringEndsWith("/d/download/$document->uuid/{$document->file->uuid}", $url_with_version);
    }
    
    public function test_document_embed_url_generation()
    {
        $user = $this->createAdminUser();
        $document = $this->createDocument($user);

        $url = RoutingHelpers::embed($document);

        $this->assertStringEndsWith("/d/download/$document->uuid?embed=true", $url);
        
        $url_with_version = RoutingHelpers::embed($document, $document->file);

        $this->assertStringEndsWith("/d/download/$document->uuid/{$document->file->uuid}?embed=true", $url_with_version);
    }

    public function test_document_preview_url_generation()
    {
        $user = $this->createAdminUser();
        $document = $this->createDocument($user);

        $url = RoutingHelpers::preview($document);

        $this->assertStringEndsWith("/d/show/$document->uuid", $url);
        
        $url_with_version = RoutingHelpers::preview($document, $document->file);

        $this->assertStringEndsWith("/d/show/$document->uuid/{$document->file->uuid}", $url_with_version);
    }

    public function test_document_thumbnail_url_generation()
    {
        $user = $this->createAdminUser();
        $document = $this->createDocument($user);

        $url = RoutingHelpers::thumbnail($document);

        $this->assertStringEndsWith("/d/thumbnail/$document->uuid", $url);
        
        $url_with_version = RoutingHelpers::thumbnail($document, $document->file);

        $this->assertStringEndsWith("/d/thumbnail/$document->uuid/{$document->file->uuid}", $url_with_version);
    }
}
