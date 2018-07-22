<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use KBox\Publication;
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

        $user = factory(\KBox\User::class)->create();

        $descriptor = factory(\KBox\DocumentDescriptor::class)->create();
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

        $user = factory(\KBox\User::class)->create();

        $descriptor = factory(\KBox\DocumentDescriptor::class)->create(['is_public' => true]);

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

        $user = factory(\KBox\User::class)->create();

        $descriptor = factory(\KBox\DocumentDescriptor::class)->create(['is_public' => true]);

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

    public function invalid_copyright_owner_provider()
    {
        return [
            [[]],
            [['name' => 'the owner name']],
            [['name' => '', 'website' => '']],
            [['name' => '', 'email' => '']],
            [['email' => '']],
            [['website' => '']],
            [['website' => 'https://klink.asia']],
            [['address' => 'something']],
            [['email' => 'some@email.io']],
        ];
    }

    /**
     * @dataProvider invalid_copyright_owner_provider
     */
    public function test_not_valid_copyright_owner_is_reported($owner)
    {
        $descriptor = factory(\KBox\DocumentDescriptor::class)->create([
            'copyright_owner' => collect($owner),
        ]);

        $this->assertFalse($descriptor->isCopyrightOwnerValidForPublishing());
    }

    public function copyright_owner_provider()
    {
        return [
            [['name' => 'the owner name', 'website' => 'https://klink.asia']],
            [['name' => 'the owner name', 'email' => 'some@email.io']],
            [['name' => 'the owner name', 'email' => 'some@email.io', 'website' => 'https://klink.asia']],
            [['name' => 'the owner name', 'email' => 'some@email.io', 'website' => 'https://klink.asia', 'address' => 'something']],
        ];
    }

    /**
     * @dataProvider copyright_owner_provider
     */
    public function test_valid_copyright_owner_is_reported($owner)
    {
        $descriptor = factory(\KBox\DocumentDescriptor::class)->create([
            'copyright_owner' => collect($owner),
        ]);

        $this->assertTrue($descriptor->isCopyrightOwnerValidForPublishing());
    }
}
