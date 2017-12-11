<?php

namespace KBox\DocumentsElaboration\Actions;

use Klink\DmsDocuments\DocumentsService;
use KBox\Contracts\Action;

class AddToSearch extends Action
{
    /**
     * @var \Klink\DmsDocuments\DocumentsService
     */
    private $documentsService = null;
    
    protected $canFail = true;
     
    /**
     * Create the action.
     *
     * @return void
     */
    public function __construct(DocumentsService $documentsService)
    {
        $this->documentsService = $documentsService;
    }

    public function run($descriptor)
    {
        // here we use reindexDocument as currently there is no index function that
        // takes an existing DocumentDescriptor
        return $this->documentsService->reindexDocument($descriptor, 'private', true);
    }
}
