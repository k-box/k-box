<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\Concerns\ClearDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class KlinkApiControllerTest extends TestCase
{
    use ClearDatabase, DatabaseTransactions;

    public function test_klink_route_redirect_to_preview_with_uuid()
    {
        $this->withKlinkAdapterFake();

        $document = factory('KBox\DocumentDescriptor')->create();
        
        $url = route('klink_api', ['id' => $document->local_document_id, 'action' => 'document']);

        $response = $this->get($url);
        
        $response->assertRedirect(route('documents.preview', ['uuid' => $document->uuid]));
    }
    
    public function test_download_action_is_redirected()
    {
        $this->withKlinkAdapterFake();

        $document = factory('KBox\DocumentDescriptor')->create();

        $url = route('klink_api', ['id' => $document->local_document_id, 'action' => 'download']);

        $response = $this->get($url);
        
        $response->assertRedirect(route('documents.download', ['uuid' => $document->uuid]));
    }
    public function test_thumbnail_action_is_redirected()
    {
        $this->withKlinkAdapterFake();

        $document = factory('KBox\DocumentDescriptor')->create();

        $url = route('klink_api', ['id' => $document->local_document_id, 'action' => 'thumbnail']);

        $response = $this->get($url);
        
        $response->assertRedirect(route('documents.thumbnail', ['uuid' => $document->uuid]));
    }
}
