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
     * By default the new action is added at the end. Specify a $before action class if you want to add the issue in a different place
     *
     * @param string $action The class of the action to add
     * @param string $before The class of the action that should be preceed by $action
     * @return DocumentElaborationManager
     */
    public function register($action, $before = null)
    {
        if (in_array($action, $this->actions)) {
            return $this;
        }

        if (is_null($before) || ! in_array($before, $this->actions)) {
            array_push($this->actions, $action);
        } else {
            $index = array_search($before, $this->actions);
            array_splice($this->actions, $index, 0, $action);
        }

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
