<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use KlinkDMS\Publication;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentPublishingTest extends TestCase
{
    use WithoutMiddleware, DatabaseTransactions;
    
    public function test_document_can_be_published()
    {
        Storage::fake('local');

        $this->disableExceptionHandling();

        $adapter = $this->withKlinkAdapterFake();

        $user = factory('KlinkDMS\User')->create();

        $descriptor = factory('KlinkDMS\DocumentDescriptor')->create();

        $response = $this->actingAs($user)->json('POST', '/published-documents', [
            'document_id' => $descriptor->id
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'descriptor', 'publication'
        ]);

        $descriptor = $descriptor->fresh();

        $this->assertTrue($descriptor->isPublic());
        $this->assertTrue($descriptor->isPublished());

        $publication = $descriptor->publication();

        $this->assertNotNull($publication);
        $this->assertEquals($user->id, $publication->published_by);
        $this->assertNotNull($publication->published_at);
        $this->assertFalse($publication->pending);
        $this->assertEquals(Publication::STATUS_PUBLISHED, $publication->status);
    }
 
    public function test_document_can_be_unpublished()
    {
        Storage::fake('local');
        
        $this->disableExceptionHandling();

        $adapter = $this->withKlinkAdapterFake();

        $user = factory('KlinkDMS\User')->create();

        $descriptor = factory('KlinkDMS\DocumentDescriptor')->create(['is_public' => true]);

        Publication::unguard(); // as fields are not mass assignable
        
        $what = $descriptor->publications()->create([
            'published_at' => Carbon::now(),
            'pending' => false
        ]);

        $this->assertTrue($descriptor->isPublic());
        $this->assertTrue($descriptor->isPublished());

        $response = $this->actingAs($user)->json('DELETE', "/published-documents/$descriptor->id");

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'descriptor', 'publication'
        ]);

        $descriptor = $descriptor->fresh();

        $this->assertFalse($descriptor->isPublic());
        $this->assertFalse($descriptor->isPublished());

        $publication = $descriptor->publication();

        $this->assertNotNull($publication);
        $this->assertEquals($user->id, $publication->unpublished_by);
        $this->assertNotNull($publication->published_at);
        $this->assertNotNull($publication->unpublished_at);
        $this->assertFalse($publication->pending);
        $this->assertEquals(Publication::STATUS_UNPUBLISHED, $publication->status);
    }
 
    public function test_document_can_be_published_only_one_time()
    {
        Storage::fake('local');
        
        $this->disableExceptionHandling();

        $adapter = $this->withKlinkAdapterFake();

        $user = factory('KlinkDMS\User')->create();

        $descriptor = factory('KlinkDMS\DocumentDescriptor')->create(['is_public' => true]);

        Publication::unguard(); // as fields are not mass assignable
        
        $what = $descriptor->publications()->create([
            'pending' => true
        ]);

        $response = $this->actingAs($user)->json('POST', '/published-documents', [
            'document_id' => $descriptor->id
        ]);

        $response->assertStatus(200);
        
        $response->assertJsonStructure([
            'status', 'error'
        ]);
    }
}
