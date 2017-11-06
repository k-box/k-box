<?php

namespace KlinkDMS\Http\Controllers;

use KlinkDMS\DocumentDescriptor;
use KlinkDMS\File;
use Illuminate\Http\Request;
use Content\Services\ThumbnailsService;
use Content\Services\PreviewService;
use Klink\DmsDocuments\DocumentsService;
use Content\Preview\Exception\UnsupportedFileException;
use Content\Preview\Exception\PreviewGenerationException;
use Klink\DmsAdapter\KlinkDocumentUtils;

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
            DocumentsService $documentsService)
    {
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
    public function show(Request $request, $id, $action)
    {

        // no need for input syntax validation, already performed by Laravel when invoking this controller

        $doc = DocumentDescriptor::withTrashed()->where('local_document_id', $id)->with('file')->first();

        if (is_null($doc) || (! is_null($doc) && ! $doc->isMine())) {
            \App::abort(404, trans('errors.document_not_found'));
        }
        
        $user = $request->user();

        if (! ($doc->isPublished() || $doc->hasPendingPublications() || $doc->hasPublicLink()) && is_null($user)) {
            \Log::warning('KlinkApiController, requested a document that is not public and user is not authenticated', ['url' => $request->url()]);

            session()->put('url.dms.intended', $request->url());

            return redirect()->to(route('frontpage'));
        } elseif ($doc->trashed()) {
            \App::abort(404, trans('errors.document_not_found'));
        }

        $collections = $doc->groups;
        $is_in_collection = false;

        if (! is_null($collections) && ! $collections->isEmpty() && ! is_null($user)) {
            $serv = $this->documentsService;

            $filtered = $collections->filter(function ($c) use ($serv, $user) {
                return $serv->isCollectionAccessible($user, $c);
            });
            
            $is_in_collection = ! $filtered->isEmpty();
        }

        $is_shared = $doc->hasPublicLink() ? true : (! is_null($user) ? $doc->shares()->sharedWithMe($user)->count() > 0 : false);

        $owner = ! is_null($user) && ! is_null($doc->owner) ? $doc->owner->id === $user->id || $user->isContentManager() : (is_null($doc->owner) ? true : false);

        if (! ($is_in_collection || $is_shared || $doc->isPublic() || $owner)) {
            return view('errors.403', ['reason' => 'ForbiddenException: not shared, not in collection, not public or private of the user']);
        }

        if ($action==='document' || $action==='preview') {
            return $this->getPreview($request, $doc);
        } elseif ($action==='download') {
            return $this->getDocument($request, $doc);
        } elseif ($action==='thumbnail') {
            return $this->getThumbnail($request, $doc);
        }

        return view('errors.403', ['reason' => 'WrongAction']);
    }

    /**
     * Get (or build) the thumbnail of a Document Descriptor
     *
     * @param Request $request the original HTTP request
     * @param DocumentDescriptor $doc the descriptor to build the thumbnail for
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function getThumbnail(Request $request, DocumentDescriptor $doc)
    {
        if (! $doc->isFileUploadComplete()) {
            $response = response()->make();
            $response->setLastModified(\Carbon\Carbon::now());
            
            $response->setContent('<svg xmlns="http://www.w3.org/2000/svg" id="svg" width="300" height="300" viewport="0 0 100 100" version="1.1"><path d="M18 32h12V20h8L24 6 10 20h8zm-8 4h28v4H10z"/></svg>');

            $response->header('Content-Type', 'image/svg+xml');

            return $response;
        }

        /* File */ $file = $doc->file;
        
        if (is_null($file)) {
            $file = File::withTrashed()->findOrFail($doc->file_id);
        }

        $response = response()->make();

        // mark the response as either public or private
        $response->setPublic();

        // set the private or shared max age
        $response->setMaxAge(3600);
        $response->setSharedMaxAge(3600);

        $response->setETag(substr($file->hash, 0, 32));
        $response->setLastModified($file->updated_at);

        // Set response as public. Otherwise it will be private by default.
        $response->setPublic();

        // Check that the Response is not modified for the given Request
        if ($response->isNotModified($request)) {
            // return the 304 Response immediately
            return $response;
        }
        
        if (! $doc->isPublic() && is_null($request->user())) {
            $response->setContent(file_get_contents(public_path('images/document.png')));
        } else {
            if (empty($file->absolute_thumbnail_path)) {
                $t_path = $this->thumbnails->generate($file);

                $response->setContent(file_get_contents($t_path));
            } elseif (@is_file($file->absolute_thumbnail_path)) {
                $response->setContent(file_get_contents($file->absolute_thumbnail_path));
            } else {
                $response->setContent(file_get_contents(public_path('images/document.png')));
            }
        }

        $response->header('Content-Type', 'image/png');

        return $response;
    }

    /**
     * Get the Document Descriptor file download
     *
     * @param Request $request the original HTTP request
     * @param DocumentDescriptor $doc the descriptor whose file needs to be downloaded
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    private function getDocument(Request $request, DocumentDescriptor $doc)
    {
        if ($doc->trashed()) {
            \App::abort(404, trans('errors.document_not_found'));
        }

        /* File */ $file = $doc->file;

        $embed = $request->input('embed', false);
        
        $headers = [
            'Content-Type' => $file->mime_type
        ];

        $response = new \Symfony\Component\HttpFoundation\BinaryFileResponse($file->absolute_path, 200, $headers, true, null);
        $name = $file->name;
        if (! is_null($name)) {
            return $response->setContentDisposition((! $embed ? 'attachment' : 'inline'), $name, str_replace('%', '', \Illuminate\Support\Str::ascii($name)));
        }

        return $response;
    }
    
    /**
     * The preview of a Document Descriptor
     *
     * @param Request $request the original HTTP request
     * @param DocumentDescriptor $doc the descriptor to build the preview for
     * @return Illuminate\View\View the documents.preview view
     */
    private function getPreview(Request $request, DocumentDescriptor $doc)
    {
        if ($doc->trashed()) {
            \App::abort(404, trans('errors.document_not_found'));
        }

        /* File */ $file = $doc->file;
            
        $extension = KlinkDocumentUtils::getExtensionFromMimeType($file->mime_type);

        $render = null;
        $preview = null;

        try {
            if ($doc->isFileUploadComplete()) {
                $preview = $this->previewService->load($file->absolute_path, $extension);

                $properties = $preview->properties();

                $render = $preview->html();
            }
        } catch (UnsupportedFileException $pex) {
        } catch (PreviewGenerationException $pex) {
            \Log::error('KlinkApiController - Preview Generation, using PreviewService, failure', ['error' => $pex, 'file' => $file]);
        }

        // return view('preview::preview-full', compact('properties', 'render', 'documentTitle'));
        
        return view('documents.preview', [
            'document' => $doc,
            'file' => $file,
            'type' =>  $doc->document_type,
            'render' => $render,
            'extension' => $extension,
            'body_classes' => 'preview '.$doc->document_type,
            'pagetitle' => trans('documents.preview.page_title', ['document' => $doc->title]),
        ]);
    }
}
