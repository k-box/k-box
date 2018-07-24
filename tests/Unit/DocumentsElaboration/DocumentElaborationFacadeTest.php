<?php

namespace Tests\Unit\DocumentsElaboration;

use Tests\TestCase;
use KBox\DocumentDescriptor;
use KBox\DocumentsElaboration\DocumentElaborationManager;
use KBox\DocumentsElaboration\Facades\DocumentElaboration;
use KBox\DocumentsElaboration\Testing\Fakes\DocumentElaborationFake;

class DocumentElaborationFacadeTest extends TestCase
{
    public function test_facade_returns_fake_elaboration_manager()
    {
        DocumentElaboration::fake();

        $instance = app()->make(DocumentElaborationManager::class);

        $this->assertInstanceOf(DocumentElaborationFake::class, $instance);
    }

    public function test_fake_record_queued_elaborations()
    {
        DocumentElaboration::fake();

        $descriptor = new DocumentDescriptor([
            'id' => '1',
            'title' => 'hello',
        ]);

        DocumentElaboration::queue($descriptor);

        DocumentElaboration::assertQueued($descriptor);
    }
    
    public function test_fake_assert_not_queued_elaborations()
    {
        DocumentElaboration::fake();

        $descriptor = new DocumentDescriptor([
            'id' => '1',
            'title' => 'hello',
        ]);

        DocumentElaboration::assertNotQueued($descriptor);
    }
}
