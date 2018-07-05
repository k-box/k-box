<?php

namespace KBox\Http\Controllers\Document;

use Log;
use Exception;
use Throwable;
use KBox\File;
use Illuminate\Support\Str;
use KBox\DocumentDescriptor;
use Illuminate\Http\Request;
use Content\Services\PreviewService;
use KBox\Http\Controllers\Controller;
use KBox\Exceptions\ForbiddenException;
use Klink\DmsAdapter\KlinkDocumentUtils;
use Klink\DmsDocuments\DocumentsService;
use Illuminate\Auth\AuthenticationException;
use Content\Preview\Exception\UnsupportedFileException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Content\Preview\Exception\PreviewGenerationException;

class DocumentPreviewController extends Controller
{

    /**
     * @var Content\Services\PreviewService
     */
    private $previewService = null;

    /**
     * @var Klink\DmsDocuments\DocumentsService
     */
    private $documentsService = null;

    
    public function __construct(PreviewService $preview, DocumentsService $documentsService)
    {
        $this->previewService = $preview;
        $this->documentsService = $documentsService;
    }

    public function show(Request $request, $uuid, $versionUuid = null)
    {
        try{
            
            list($document, $file) = $this->getDocument($request, $uuid, $versionUuid);
            
            $klinkWantsDownload = ($document->isPublished() || $document->hasPendingPublications()) && $request->isKlinkRequest();
            
            if ($klinkWantsDownload) {
                return $this->downloadDocument($request, $document, $file);
            }
            
            return $this->previewDocument($document, $file);

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



    private function getDocument($request, $uuid, $versionUuid)
    {
        $doc = DocumentDescriptor::withTrashed()->whereUuid($uuid)->with('file')->first();

        $file_version = null; // will contain the file instance to use in case $version is not null, null means the latest

        if (is_null($doc->file)) {
            throw new ModelNotFoundException();
        }

        $versions = $doc->file->versions();

        if (! is_null($versionUuid) && $doc->file->uuid !== $versionUuid && $versions->contains('uuid', $versionUuid)) {
            $file_version = $versions->where('uuid', $versionUuid)->first();
        }

        // if version is requested, the file must be a version of the same document descriptor is referenced in $id

        if (is_null($doc) || (! is_null($doc) && ! $doc->isMine())) {
            throw new ModelNotFoundException();
        }
        
        $user = $request->user();

        if (! ($doc->isPublished() || $doc->hasPendingPublications() || $doc->hasPublicLink()) && is_null($user)) {
            Log::warning('KlinkApiController, requested a document that is not public and user is not authenticated', ['url' => $request->url()]);

            throw new AuthenticationException();
        }
        if ($doc->trashed()) {
            throw new ModelNotFoundException();
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

        if (! ($is_in_collection || $is_shared || $doc->isPublic() || $owner || $doc->hasPendingPublications())) {
            throw new ForbiddenException('not shared, not in collection, not public or private of the user');
        }

        return [$doc, $file_version];
    }

    private function previewDocument(DocumentDescriptor $doc, File $version = null)
    {
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

    private function downloadDocument(Request $request, DocumentDescriptor $doc, File $version = null)
    {
        /* File */ $file = $version ?? $doc->file;

        $embed = $request->input('embed', false);
        
        $headers = [
            'Content-Type' => $file->mime_type
        ];

        $response = new BinaryFileResponse($file->absolute_path, 200, $headers, true, null);
        $name = $file->name;
        if (! is_null($name)) {
            return $response->setContentDisposition((! $embed ? 'attachment' : 'inline'), $name, str_replace('%', '', Str::ascii($name)));
        }

        return $response;
    }
}
