<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use KBox\DocumentDescriptor;
use KBox\Publication;
use KBox\User;
use Tests\TestCase;

class RetryFailedPublicationCommandTest extends TestCase
{
    public function test_failed_publications_can_be_retried()
    {
        $this->withKlinkAdapterFake();

        $user = User::factory()->create();

        $document = factory(DocumentDescriptor::class)->create([
            'is_public' => true
        ]);
            
        $failedPublication = $document->publications()->save(new Publication([
            'published_by' => $user->getKey(),
            'published_at' => null,
            'failed_at' => now(),
            'pending' => false,
        ]));

        $exitCode = Artisan::call('publication:retry', [
            'publication' => $failedPublication->getKey()
        ]);

        $this->assertEquals(0, $exitCode);

        $publication = $failedPublication->fresh();

        $this->assertNotNull($publication->published_at);
    }

    public function test_all_failed_publications_are_retried()
    {
        $this->withKlinkAdapterFake();

        $user = User::factory()->create();

        $document = factory(DocumentDescriptor::class)->create([
            'is_public' => true
        ]);
            
        $failedPublication = $document->publications()->save(new Publication([
            'published_by' => $user->getKey(),
            'published_at' => null,
            'failed_at' => now(),
            'pending' => false,
        ]));

        $exitCode = Artisan::call('publication:retry');

        $this->assertEquals(0, $exitCode);
        
        $publication = $failedPublication->fresh();

        $this->assertNotNull($publication->published_at);
    }
}
