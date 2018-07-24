<?php

namespace Tests\Unit\DocumentsElaboration;

use Tests\TestCase;
use KBox\DocumentDescriptor;
use Illuminate\Support\Facades\Queue;
use KBox\DocumentsElaboration\Jobs\ElaborateDocument;
use KBox\DocumentsElaboration\DocumentElaborationManager;

class DocumentElaborationHelpersTest extends TestCase
{
    public function test_invoking_helper_without_parameters_return_manager_instance()
    {
        $instance = elaborate();

        $this->assertInstanceOf(DocumentElaborationManager::class, $instance);
    }

    public function test_helper_queue_elaboration_pipeline()
    {
        Queue::fake();

        config(['elaboration.queue' => 'custom']);

        $descriptor = new DocumentDescriptor();

        elaborate($descriptor);

        Queue::assertPushedOn('custom', ElaborateDocument::class);
    }
}
