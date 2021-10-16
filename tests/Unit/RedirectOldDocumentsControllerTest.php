<?php

namespace Tests\Unit;

use Tests\TestCase;

use KBox\DocumentDescriptor;
use KBox\User;

class RedirectOldDocumentsControllerTest extends TestCase
{
    public function test_preview_page_reachable_using_old_institution_url()
    {
        $this->withKlinkAdapterFake();
        
        $user = User::factory()->partner()->create();
        
        $doc = factory(DocumentDescriptor::class)->create([
            'owner_id' => $user->id,
            'local_document_id' => '789456123a'
        ]);
        
        $response = $this->actingAs($user)->get(route('documents.by-klink-id', [
            'institution' => 'whatever',
            'local_id' => $doc->local_document_id,
        ]));

        $response->assertRedirect(route('documents.preview', $doc->uuid));
    }

    public function test_redirect_forbidden()
    {
        $this->withKlinkAdapterFake();
        
        $user = User::factory()->partner()->create();
        
        $another_user = User::factory()->partner()->create();
        
        $doc = factory(DocumentDescriptor::class)->create(['owner_id' => $user->id]);
        
        $response = $this->actingAs($another_user)->get(route('documents.by-klink-id', [
            'institution' => 'whatever',
            'local_id' => $doc->local_document_id,
        ]));

        $response->assertForbidden();
    }
}
