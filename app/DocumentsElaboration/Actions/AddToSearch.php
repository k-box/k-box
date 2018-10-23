<?php

namespace KBox\DocumentsElaboration\Actions;

use Klink\DmsAdapter\Exceptions\KlinkException;
use KBox\Documents\Services\DocumentsService;
use KBox\Contracts\Action;
use Log;

class AddToSearch extends Action
{
    /**
     * @var \KBox\Documents\Services\DocumentsService
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
        try {
            
            // here we use reindexDocument as currently there is no index function that
            // takes an existing DocumentDescriptor
            return $this->documentsService->reindexDocument($descriptor, 'private', true);
        } catch (KlinkException $ex) {
            Log::error("Add to search failed for $descriptor->uuid", ['error' => $ex]);

            return $descriptor;
        }
    }
}
