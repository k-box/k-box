<?php

namespace Tests\Feature;

use KBox\DocumentDescriptor;
use KBox\Documents\Services\DocumentsService;
use KBox\Jobs\PublishDocumentJob;
use KBox\Publication;
use KBox\User;
use Tests\TestCase;

class PublishDocumentJobTest extends TestCase
{
    public function test_failed_publications_can_be_retried()
    {
        $this->withKlinkAdapterFake();

        $user = User::factory()->create();

        $document = DocumentDescriptor::factory()->create([
            'is_public' => true
        ]);
            
        $failedPublication = $document->publications()->save(new Publication([
            'published_by' => $user->getKey(),
            'published_at' => null,
            'failed_at' => now(),
            'pending' => false,
        ]));

        (new PublishDocumentJob($failedPublication))->handle(app()->make(DocumentsService::class));
        
        $publication = $failedPublication->fresh();

        $this->assertNotNull($publication->published_at);
    }

    public function test_publish_and_already_published_document()
    {
        $this->withKlinkAdapterFake();

        $user = User::factory()->create();

        $document = DocumentDescriptor::factory()->create([
            'is_public' => true
        ]);

        $originalPublicationDate = now()->subDays(2);
            
        $publication = $document->publications()->save(new Publication([
            'published_by' => $user->getKey(),
            'published_at' => $originalPublicationDate,
            'failed_at' => null,
            'pending' => false,
        ]));

        (new PublishDocumentJob($publication))->handle(app()->make(DocumentsService::class));
        
        $expectedPublication = $publication->fresh();

        $this->assertNotNull($expectedPublication->published_at);
        $this->assertNotEquals($originalPublicationDate, $expectedPublication->published_at);
    }

    public function test_pending_publications_can_be_processed()
    {
        $this->withKlinkAdapterFake();

        $user = User::factory()->create();

        $document = DocumentDescriptor::factory()->create([
            'is_public' => false
        ]);
            
        $publication = $document->publications()->save(new Publication([
            'published_by' => $user->getKey(),
            'published_at' => null,
            'failed_at' => null,
            'pending' => true,
        ]));

        (new PublishDocumentJob($publication))->handle(app()->make(DocumentsService::class));
        
        $expectedPublication = $publication->fresh();

        $this->assertNotNull($expectedPublication->published_at);
        $this->assertFalse($expectedPublication->pending);
    }
}
