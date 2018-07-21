<?php

namespace KBox\Http\Composers;

use KBox\DocumentDescriptor;
use Illuminate\Contracts\View\View;
use Klink\DmsDocuments\DocumentsService;

class DuplicateDocumentsComposer
{

    /**
     * @var \Klink\DmsDocuments\DocumentsService
     */
    private $documents = null;
    
    /**
     * Create a new profile composer.
     *
     * @param  UserRepository  $users
     * @return void
     */
    public function __construct(DocumentsService $documentsService)
    {
        $this->documents = $documentsService;
    }

    public function duplicatePartial(View $view)
    {
        $document = isset($view['duplicate']) ? $view['duplicate']->duplicateOf : null;
        
        $auth_check = \Auth::check();

        if (! $auth_check) {
            return;
        }

        if (! is_a($document, DocumentDescriptor::class)) {
            return;
        }

        $auth_user = \Auth::user();
        
        $collections = $this->documents->getDocumentCollections($document, $auth_user);
                
        $view->with('is_in_collection', ! $collections->isEmpty());

        $view->with('collections', $collections);
    }
}
