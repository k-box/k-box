<?php

namespace Tests\Unit\DocumentsElaboration;

use Tests\TestCase;
use KBox\DocumentDescriptor;
use Illuminate\Support\Facades\Queue;
use KBox\DocumentsElaboration\Jobs\ElaborateDocument;
use KBox\DocumentsElaboration\Facades\DocumentElaboration;

class DocumentElaborationManagerTest extends TestCase
{
    public function test_manager_returns_configured_actions()
    {
        $actions = DocumentElaboration::actions();

        $this->assertEquals(config('elaboration.pipelines.default'), $actions);
    }

    public function test_action_can_be_registered()
    {
        $configured_actions = config('elaboration.pipelines.default');

        DocumentElaboration::register('Class');
        
        $actions = DocumentElaboration::actions();

        $this->assertEquals(array_merge($configured_actions, ['Class']), $actions);
    }

    public function test_helper_queue_elaboration_pipeline_execution()
    {
        Queue::fake();

        config(['elaboration.queue' => 'custom']);

        $descriptor = new DocumentDescriptor();

        elaborate($descriptor);

        Queue::assertPushedOn('custom', ElaborateDocument::class);
    }
}
