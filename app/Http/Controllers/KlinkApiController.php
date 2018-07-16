<?php

namespace KBox\Http\Controllers;

use Exception;
use Throwable;
use KBox\DocumentDescriptor;
use KBox\File;
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

        // $file_version = null; // will contain the file instance to use in case $version is not null, null means the latest

        // if (is_null($doc->file)) {
        //     \App::abort(404, trans('errors.document_not_found'));
        // }

        // $versions = $doc->file->versions();

        // if (! is_null($version) && $doc->file->uuid !== $version && $versions->contains('uuid', $version)) {
        //     $file_version = $versions->where('uuid', $version)->first();
        // }

        // // if version is requested, the file must be a version of the same document descriptor is referenced in $id

        // if (is_null($doc) || (! is_null($doc) && ! $doc->isMine())) {
        //     \App::abort(404, trans('errors.document_not_found'));
        // }
        
        // $user = $request->user();

        // if (! ($doc->isPublished() || $doc->hasPendingPublications() || $doc->hasPublicLink()) && is_null($user)) {
        //     \Log::warning('KlinkApiController, requested a document that is not public and user is not authenticated', ['url' => $request->url()]);

        //     session()->put('url.dms.intended', $request->url());

        //     return redirect()->to(route('frontpage'));
        // } elseif ($doc->trashed()) {
        //     \App::abort(404, trans('errors.document_not_found'));
        // }

        // $collections = $doc->groups;
        // $is_in_collection = false;

        // if (! is_null($collections) && ! $collections->isEmpty() && ! is_null($user)) {
        //     $serv = $this->documentsService;

        //     $filtered = $collections->filter(function ($c) use ($serv, $user) {
        //         return $serv->isCollectionAccessible($user, $c);
        //     });
            
        //     $is_in_collection = ! $filtered->isEmpty();
        // }

        // $is_shared = $doc->hasPublicLink() ? true : (! is_null($user) ? $doc->shares()->sharedWithMe($user)->count() > 0 : false);

        // $owner = ! is_null($user) && ! is_null($doc->owner) ? $doc->owner->id === $user->id || $user->isContentManager() : (is_null($doc->owner) ? true : false);

        // if (! ($is_in_collection || $is_shared || $doc->isPublic() || $owner || $doc->hasPendingPublications())) {
        //     return view('errors.403', ['reason' => 'ForbiddenException: not shared, not in collection, not public or private of the user']);
        // }

        // // transforming into a download, if request is made using Guzzle.
        // // This is a way of identifying that the request is coming from the K-Search, as, thanks to the proxy,
        // // the real host and IP addresses are not available
        // $isKSearchRequest = ($doc->isPublished() || $doc->hasPendingPublications()) && network_enabled() && str_contains(strtolower($request->userAgent()), 'guzzlehttp');

        // if (! $isKSearchRequest && ($action==='document' || $action==='preview')) {
        //     return $this->getPreview($request, $doc, $file_version);
        // } elseif ($isKSearchRequest || $action==='download') {
        //     return $this->getDocument($request, $doc, $file_version);
        // } elseif ($action==='thumbnail') {
        //     return $this->getThumbnail($request, $doc, $file_version);
        // }

        // return view('errors.403', ['reason' => 'WrongAction']);
    }

    /**
     * Get (or build) the thumbnail of a Document Descriptor
     *
     * @param Request $request the original HTTP request
     * @param DocumentDescriptor $doc the descriptor to build the thumbnail for
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function getThumbnail(Request $request, DocumentDescriptor $doc, File $version = null)
    {
        if (! $doc->isFileUploadComplete()) {
            $response = response()->make();
            $response->setLastModified(\Carbon\Carbon::now());
            
            $response->setContent('<svg xmlns="http://www.w3.org/2000/svg" id="svg" width="300" height="300" viewport="0 0 100 100" version="1.1"><path d="M18 32h12V20h8L24 6 10 20h8zm-8 4h28v4H10z"/></svg>');

            $response->header('Content-Type', 'image/svg+xml');

            return $response;
        }

        /* File */ $file = $version ?? $doc->file;
        
        if (is_null($file)) {
            $file = File::withTrashed()->findOrFail($version ?? $doc->file_id);
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
    private function getDocument(Request $request, DocumentDescriptor $doc, File $version = null)
    {
        if ($doc->trashed()) {
            \App::abort(404, trans('errors.document_not_found'));
        }

        /* File */ $file = $version ?? $doc->file;

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
    private function getPreview(Request $request, DocumentDescriptor $doc, File $version = null)
    {
        if ($doc->trashed()) {
            \App::abort(404, trans('errors.document_not_found'));
        }

        /* File */ $file = $version ?? $doc->file;
            
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
        } catch (Exception $pex) {
            \Log::error('KlinkApiController - Preview Generation, using PreviewService, failure', ['error' => $pex, 'file' => $file]);
        } catch (Throwable $pex) {
            \Log::error('KlinkApiController - Preview Generation, using PreviewService, failure', ['error' => $pex, 'file' => $file]);
        }

        return view('documents.preview', [
            'document' => $doc,
            'file' => $file,
            'version' => $version,
            'type' =>  KlinkDocumentUtils::documentTypeFromMimeType($file->mime_type),
            'mime_type' =>  $file->mime_type,
            'render' => $render,
            'extension' => $extension,
            'body_classes' => 'preview '.$file->mime_type,
            'filename_for_download' => $version ? $version->name : $doc->title,
            'pagetitle' => trans('documents.preview.page_title', ['document' => $doc->title]),
        ]);
    }
}
