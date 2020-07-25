<?php

namespace KBox\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use KBox\DocumentDescriptor;
use KBox\Documents\Services\DocumentsService;
use KBox\Http\Controllers\Document\DocumentAccessController;
use KBox\RoutingHelpers;

/**
 * Redirect old document requests from format /documents/{institution}/{identifier}
 * to the preview page of the document following the new UUID based format
 *
 * This is used for preserving compatibility with old link format used
 */
class RedirectOldDocumentsController extends DocumentAccessController
{
    public function __construct(DocumentsService $documentsService)
    {
        parent::__construct($documentsService);
    }

    /**
     * Redirect to the preview of the document following the new URL format if
     * a local document exists given the old local identifier.
     * The institution value is not considered as support was removed.
     */
    public function show($institution, $local_id, Request $request)
    {
        $foundByLocalId = DocumentDescriptor::local()->fromLocalDocumentId($local_id)->first();

        if (! $foundByLocalId) {
            throw new ModelNotFoundException();
        }

        // this is currently a double call, but
        // will better check for file authentication
        list($document) = $this->getDocument($request, $foundByLocalId->uuid, null);

        $url = RoutingHelpers::preview($document);

        return redirect($url);
    }
}
