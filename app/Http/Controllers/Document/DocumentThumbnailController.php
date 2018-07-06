<?php

namespace KBox\Http\Controllers\Document;

use Log;
use Exception;
use Throwable;
use KBox\File;
use KBox\DocumentDescriptor;
use Illuminate\Http\Request;
use KBox\Http\Controllers\Controller;
use KBox\Exceptions\ForbiddenException;
use Content\Services\ThumbnailsService;
use Klink\DmsAdapter\KlinkDocumentUtils;
use Klink\DmsDocuments\DocumentsService;
use Illuminate\Auth\AuthenticationException;
use Content\Preview\Exception\UnsupportedFileException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DocumentThumbnailController extends DocumentAccessController
{
    /**
     * @var Content\Services\ThumbnailsService
     */
    private $thumbnails = null;

    public function __construct(ThumbnailsService $thumbnailService, DocumentsService $documentsService)
    {
        $this->thumbnails = $thumbnailService;
        parent::__construct($documentsService);
    }

    public function show(Request $request, $uuid, $versionUuid = null)
    {
        try{
            
            list($document, $file) = $this->getDocument($request, $uuid, $versionUuid);

            return $this->getThumbnail($request, $document, $file);

        }
        catch(AuthenticationException $ex){
            Log::warning('KlinkApiController, requested a document that is not public and user is not authenticated', ['url' => $request->url()]);

            session()->put('url.dms.intended', $request->url());

            return redirect()->to(route('frontpage'));
        }
        catch(ForbiddenException $ex){
            return view('errors.403', ['reason' => 'ForbiddenException: ' . $ex->getMessage()]);
        }
        catch(ModelNotFoundException $ex){
            return view('errors.404', ['reason' => trans('errors.document_not_found')]);
        }
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

        
        $response->setLastModified($file->updated_at);

        // Set response as public. Otherwise it will be private by default.
        $response->setPublic();

        // Check that the Response is not modified for the given Request
        if ($response->isNotModified($request)) {
            // return the 304 Response immediately
            return $response;
        }

        $etag_suffix = '';
        
        if (! $doc->isPublic() && is_null($request->user())) {
            $response->setContent(file_get_contents(public_path('images/document.png')));
            $etag_suffix = '1';
        } else {
            if (empty($file->absolute_thumbnail_path)) {
                $t_path = $this->thumbnails->generate($file);

                $response->setContent(file_get_contents($t_path));
            } elseif (@is_file($file->absolute_thumbnail_path)) {
                $response->setContent(file_get_contents($file->absolute_thumbnail_path));
            } else {
                $response->setContent(file_get_contents(public_path('images/document.png')));
                $etag_suffix = '2';
            }
        }

        $response->header('ETag', $file->hash . $etag_suffix);

        $response->header('Content-Type', 'image/png');

        return $response;
    }

}
