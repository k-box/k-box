<?php

namespace Tests\Unit;

use Tests\TestCase;
use KBox\DocumentDescriptor;
use Illuminate\Support\Facades\Event;
use KBox\Events\DocumentDescriptorDeleted;
use KBox\Events\DocumentDescriptorRestored;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentDescriptorEventsTest extends TestCase
{
    use DatabaseTransactions;
    
    public function test_deleted_event_fired_for_document_trash()
    {
        Event::fake();
        $document = factory(DocumentDescriptor::class)->create();
        $this->actingAs($document->owner);

        $document->delete();

        Event::assertDispatched(DocumentDescriptorDeleted::class, function ($e) use ($document) {
            return $e->document->id === $document->id && ! $e->forceDeleted && $e->user->is($document->owner);
        });
    }
    
    public function test_deleted_event_fired_for_document_delete()
    {
        Event::fake();
        $document = factory(DocumentDescriptor::class)->create();
        $this->actingAs($document->owner);

        $document->forceDelete();

        Event::assertDispatched(DocumentDescriptorDeleted::class, function ($e) use ($document) {
            return $e->document->id === $document->id && $e->forceDeleted && $e->user->is($document->owner);
        });
    }
    
    public function test_restored_event_fired_for_trashed_document()
    {
        Event::fake();
        $document = factory(DocumentDescriptor::class)->create();
        $this->actingAs($document->owner);
        $document->delete();

        $document->restore();

        Event::assertDispatched(DocumentDescriptorRestored::class, function ($e) use ($document) {
            return $e->document->id === $document->id && $e->user->is($document->owner);
        });
    }
}
