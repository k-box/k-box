<?php

namespace Tests\Unit\DocumentsElaboration;

use Tests\TestCase;
use KBox\DocumentDescriptor;
use Illuminate\Support\Facades\Queue;
use Klink\DmsAdapter\Traits\SwapInstance;
use KBox\DocumentsElaboration\Jobs\ElaborateDocument;
use KBox\DocumentsElaboration\DocumentElaborationManager;
use KBox\DocumentsElaboration\Facades\DocumentElaboration;
use KBox\DocumentsElaboration\Actions\AddToSearch;

class DocumentElaborationManagerTest extends TestCase
{
    use SwapInstance;

    public function test_manager_returns_configured_actions()
    {
        $this->swap(DocumentElaborationManager::class, new DocumentElaborationManager());

        $actions = DocumentElaboration::actions();

        $this->assertEquals(config('elaboration.pipelines.default'), $actions);
    }

    public function test_action_can_be_registered()
    {
        $this->swap(DocumentElaborationManager::class, new DocumentElaborationManager());

        $configured_actions = config('elaboration.pipelines.default');

        DocumentElaboration::register('Class');
        
        $actions = DocumentElaboration::actions();

        $this->assertEquals(array_merge($configured_actions, ['Class']), $actions);
    }

    public function test_action_can_be_registered_before_an_existing_action()
    {
        $this->swap(DocumentElaborationManager::class, new DocumentElaborationManager());

        $configured_actions = config('elaboration.pipelines.default');

        DocumentElaboration::register('Class', AddToSearch::class);
        
        $actions = DocumentElaboration::actions();

        $expected_actions = [
            \KBox\DocumentsElaboration\Actions\ExtractFileProperties::class,
            \KBox\DocumentsElaboration\Actions\GuessLanguage::class,
            'Class',
            \KBox\DocumentsElaboration\Actions\AddToSearch::class,
            \KBox\DocumentsElaboration\Actions\EnsureCorrectPictureOrientation::class,
            \KBox\DocumentsElaboration\Actions\GenerateThumbnail::class,
            \KBox\DocumentsElaboration\Actions\ElaborateVideo::class
        ];

        $this->assertEquals($expected_actions, $actions);
    }

    public function test_helper_queue_elaboration_pipeline_execution()
    {
        Queue::fake();

        config(['elaboration.queue' => 'custom']);
        $this->swap(DocumentElaborationManager::class, new DocumentElaborationManager());

        $descriptor = new DocumentDescriptor();

        elaborate($descriptor);

        Queue::assertPushedOn('custom', ElaborateDocument::class);
    }
}
