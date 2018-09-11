<?php

namespace KBox\Http\Controllers\Document;

use Log;
use Exception;
use Throwable;
use KBox\File;
use KBox\DocumentDescriptor;
use Illuminate\Http\Request;
use KBox\Documents\Facades\Files;
use KBox\Documents\Services\PreviewService;
use KBox\Exceptions\ForbiddenException;
use KBox\Documents\Services\DocumentsService;
use Illuminate\Auth\AuthenticationException;
use KBox\Documents\Preview\Exception\UnsupportedFileException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use KBox\Documents\Preview\Exception\PreviewGenerationException;

class DocumentPreviewController extends DocumentAccessController
{
    /**
     * @var KBox\Documents\Services\PreviewService
     */
    private $previewService = null;

    public function __construct(PreviewService $preview, DocumentsService $documentsService)
    {
        $this->previewService = $preview;
        parent::__construct($documentsService);
    }

    public function show(Request $request, $uuid, $versionUuid = null)
    {
        try {
            list($document, $file) = $this->getDocument($request, $uuid, $versionUuid);
            
            $klinkWantsDownload = ($document->isPublished() || $document->hasPendingPublications()) && $request->isKlinkRequest();
            
            if ($klinkWantsDownload) {
                return $this->downloadDocument($request, $document, $file);
            }
            
            return $this->previewDocument($document, $file);
        } catch (AuthenticationException $ex) {
            Log::warning('KlinkApiController, requested a document that is not public and user is not authenticated', ['url' => $request->url()]);

            session()->put('url.dms.intended', $request->url());

            return redirect()->to(route('frontpage'));
        } catch (ForbiddenException $ex) {
            return view('errors.403', ['reason' => 'ForbiddenException: '.$ex->getMessage()]);
        } catch (ModelNotFoundException $ex) {
            return view('errors.404', ['reason' => trans('errors.document_not_found')]);
        }
    }

    private function previewDocument(DocumentDescriptor $doc, File $version = null)
    {
        /* File */ $file = $version ?? $doc->file;
            
        $extension = Files::extensionFromType($file->mime_type, $file->document_type);

        $render = null;
        $preview = null;

        try {
            if ($doc->isFileUploadComplete() && $doc->status === DocumentDescriptor::STATUS_COMPLETED) {
                $preview = $this->previewService->preview($file);

                $render = $preview->render();
            }
        } catch (UnsupportedFileException $pex) {
        } catch (PreviewGenerationException $pex) {
            Log::error('Preview Generation, failure', ['error' => $pex, 'file' => $file]);
        } catch (Exception $pex) {
            Log::error('Preview Generation, failure', ['error' => $pex, 'file' => $file]);
        } catch (Throwable $pex) {
            Log::error('Preview Generation, failure', ['error' => $pex, 'file' => $file]);
        }

        return view('documents.preview', [
            'document' => $doc,
            'file' => $file,
            'version' => $version,
            'type' =>  $file->document_type,
            'mime_type' =>  $file->mime_type,
            'render' => $render,
            'previewable' => $preview,
            'extension' => $extension,
            'body_classes' => "preview $file->mime_type",
            'filename_for_download' => $version ? $version->name : $doc->title,
            'pagetitle' => trans('documents.preview.page_title', ['document' => $doc->title]),
        ]);
    }
}
