<?php

namespace KBox\DocumentsElaboration;

use KBox\Contracts\Action;
use KBox\DocumentDescriptor;
use KBox\DocumentsElaboration\Jobs\ElaborateDocument;

class DocumentElaborationManager
{
    /**
     * The array of elaboration actions
     *
     * @var array
     */
    protected $actions = [];

    /**
     * The queue to dispatch jobs on
     *
     * @var string
     */
    protected $queue = null;
    
    /**
     * The pipeline name to use
     *
     * @var string
     */
    protected $pipeline = null;

    /**
     * Create a new queue manager instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->queue = config('elaboration.queue');
        $this->pipeline = config('elaboration.pipeline');
        $this->actions = config("elaboration.pipelines.$this->pipeline");
    }

    /**
     * Register a new elaboration action
     *
     * The new action is added at the end.
     *
     * @param string $action The class of the action to add
     * @return DocumentElaborationManager
     */
    public function register($action)
    {
        array_push($this->actions, $action);

        return $this;
    }

    /**
     * Return the list of configured actions to execute
     */
    public function actions()
    {
        return $this->actions;
    }

    /**
     * Immeditely trigger the elaboration pipeline for a document
     *
     * This is a synchrounous method. It returns when the pipeline is complete
     */
    public function elaborate(DocumentDescriptor $descriptor)
    {
        dispatch_now((new ElaborateDocument($descriptor))->onQueue($this->queue));
    }
    
    /**
     * Enqueue a document descriptor for elaboration
     */
    public function queue(DocumentDescriptor $descriptor)
    {
        dispatch((new ElaborateDocument($descriptor))->onQueue($this->queue));
    }
}
