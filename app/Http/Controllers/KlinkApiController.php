<?php

namespace KBox\Http\Controllers;

use KBox\DocumentDescriptor;
use KBox\File;
use Illuminate\Http\Request;
use KBox\Documents\Services\ThumbnailsService;
use KBox\Documents\Services\PreviewService;
use KBox\Documents\Services\DocumentsService;

/**
 * Controller for Klink API (/klink/{ID}/{Action}) pages
 *
 * Handles the following actions:
 * - preview: shows the document preview
 * - thumbnail: return the document thumbnail
 * - document: now behave like the preview action
 * - download: trigger the file download
 */
class KlinkApiController extends Controller
{
    private $thumbnails = null;
    
    private $previewService = null;
    
    private $documentsService = null;

    /**
     * Initialize the controller instance
     */
    public function __construct(
        ThumbnailsService $thumbService,
        PreviewService $preview,
        DocumentsService $documentsService
    ) {
        $this->thumbnails = $thumbService;
        $this->previewService = $preview;
        $this->documentsService = $documentsService;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @param  string $action the type of action to perform on the given rseource: thumbnail or document
     * @return Response
     */
    public function show(Request $request, $id, $action, $version = null)
    {
        $doc = DocumentDescriptor::withTrashed()->where('local_document_id', $id)->with('file')->first();

        if (($action==='document' || $action==='preview')) {
            return redirect()->to(route('documents.preview', ['uuid' => $doc->uuid, 'versionUuid' => $version]));
        } elseif ($action==='download') {
            return redirect()->to(route('documents.download', ['uuid' => $doc->uuid, 'versionUuid' => $version]));
        } elseif ($action==='thumbnail') {
            return redirect()->to(route('documents.thumbnail', ['uuid' => $doc->uuid, 'versionUuid' => $version]));
        }

        abort(404);
    }
}
