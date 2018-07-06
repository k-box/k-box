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

class DocumentPreviewController extends DocumentAccessController
{
    /**
     * @var Content\Services\PreviewService
     */
    private $previewService = null;

    public function __construct(PreviewService $preview, DocumentsService $documentsService)
    {
        $this->previewService = $preview;
        parent::__construct($documentsService);
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

}
