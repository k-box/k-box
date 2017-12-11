<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RecentDocumentsTest extends TestCase
{
    use DatabaseTransactions, WithoutMiddleware;

    public function test_shared_is_listed_as_recent_even_if_user_has_no_personal_documents()
    {
        $adapter = $this->withKlinkAdapterFake();
        
        $user_sender = factory('KBox\User')->create();
        $user_receiver = factory('KBox\User')->create();

        $descriptor = factory('KBox\DocumentDescriptor')->create();

        $share = $descriptor->shares()->create([
            'user_id' => $user_sender->id,
            'sharedwith_id' => $user_receiver->id,
            'sharedwith_type' => get_class($user_receiver),
            'token' => 'share-token',
        ]);

        $response = $this->actingAs($user_receiver)->get(route('documents.recent'));

        $response->assertStatus(200);
        $response->assertViewIs('documents.recent');
        $response->assertViewHas('documents');

        $original_response = $response->getOriginalContent();
        $listed_documents = $original_response['documents']->values()->collapse();
        $this->assertEquals(1, $listed_documents->count());
        
        $recent = $listed_documents->first();
        $this->assertEquals($descriptor->id, $recent->id);
    }
}
