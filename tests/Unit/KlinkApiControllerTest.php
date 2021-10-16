<?php

namespace Tests\Unit;

use KBox\DocumentDescriptor;
use Tests\TestCase;
use Tests\Concerns\ClearDatabase;

class KlinkApiControllerTest extends TestCase
{
    use ClearDatabase;

    public function test_klink_route_redirect_to_preview_with_uuid()
    {
        $this->withKlinkAdapterFake();

        $document = DocumentDescriptor::factory()->create();
        
        $url = route('klink_api', ['id' => $document->local_document_id, 'action' => 'document']);

        $response = $this->get($url);
        
        $response->assertRedirect(route('documents.preview', ['uuid' => $document->uuid]));
    }
    
    public function test_download_action_is_redirected()
    {
        $this->withKlinkAdapterFake();

        $document = DocumentDescriptor::factory()->create();

        $url = route('klink_api', ['id' => $document->local_document_id, 'action' => 'download']);

        $response = $this->get($url);
        
        $response->assertRedirect(route('documents.download', ['uuid' => $document->uuid]));
    }
    public function test_thumbnail_action_is_redirected()
    {
        $this->withKlinkAdapterFake();

        $document = DocumentDescriptor::factory()->create();

        $url = route('klink_api', ['id' => $document->local_document_id, 'action' => 'thumbnail']);

        $response = $this->get($url);
        
        $response->assertRedirect(route('documents.thumbnail', ['uuid' => $document->uuid]));
    }
}
