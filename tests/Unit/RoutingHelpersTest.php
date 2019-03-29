<?php

namespace Tests\Unit;

use KBox\User;
use Tests\TestCase;
use KBox\Capability;
use KBox\RoutingHelpers;
use KBox\DocumentDescriptor;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/*
 * Test the RoutingHelpers class
*/
class RoutingHelpersTest extends TestCase
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
    public function test_filter_search($empty_url, $current_active_filters, $facet, $term, $selected, $expected_url)
    {
        $url = RoutingHelpers::filterSearch($empty_url, $current_active_filters, $facet, $term, $selected);

        $this->assertEquals($expected_url, $url);
    }

    public function test_document_download_url_generation()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });
        $document = factory(DocumentDescriptor::class)->create(['owner_id' => $user->id]);

        $url = RoutingHelpers::download($document);

        $this->assertStringEndsWith("/d/download/$document->uuid", $url);
        
        $url_with_version = RoutingHelpers::download($document, $document->file);

        $this->assertStringEndsWith("/d/download/$document->uuid/{$document->file->uuid}", $url_with_version);
    }
    
    public function test_document_embed_url_generation()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });
        $document = factory(DocumentDescriptor::class)->create(['owner_id' => $user->id]);

        $url = RoutingHelpers::embed($document);

        $this->assertStringEndsWith("/d/download/$document->uuid?embed=true", $url);
        
        $url_with_version = RoutingHelpers::embed($document, $document->file);

        $this->assertStringEndsWith("/d/download/$document->uuid/{$document->file->uuid}?embed=true", $url_with_version);
    }

    public function test_document_preview_url_generation()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });
        $document = factory(DocumentDescriptor::class)->create(['owner_id' => $user->id]);

        $url = RoutingHelpers::preview($document);

        $this->assertStringEndsWith("/d/show/$document->uuid", $url);
        
        $url_with_version = RoutingHelpers::preview($document, $document->file);

        $this->assertStringEndsWith("/d/show/$document->uuid/{$document->file->uuid}", $url_with_version);
    }

    public function test_document_thumbnail_url_generation()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });
        $document = factory(DocumentDescriptor::class)->create(['owner_id' => $user->id]);

        $url = RoutingHelpers::thumbnail($document);

        $this->assertStringEndsWith("/d/thumbnail/$document->uuid", $url);
        
        $url_with_version = RoutingHelpers::thumbnail($document, $document->file);

        $this->assertStringEndsWith("/d/thumbnail/$document->uuid/{$document->file->uuid}", $url_with_version);
    }
}
