<?php

namespace KBox\Http\Controllers\Document;

use Log;
use Exception;
use Throwable;
use KBox\File;
use KBox\DocumentDescriptor;
use Illuminate\Http\Request;
use KBox\Documents\Services\PreviewService;
use KBox\Exceptions\ForbiddenException;
use KBox\Documents\Services\DocumentsService;
use Illuminate\Auth\AuthenticationException;
use KBox\Documents\Preview\Exception\UnsupportedFileException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use KBox\Documents\Preview\Exception\PreviewGenerationException;

class DocumentEmbedController extends DocumentAccessController
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

            return $this->embedDocument($document, $file);
        } catch (AuthenticationException $ex) {
            return view('errors.login');
        } catch (ForbiddenException $ex) {
            return view('errors.403', ['reason' => 'ForbiddenException: '.$ex->getMessage()]);
        } catch (ModelNotFoundException $ex) {
            return view('errors.404', ['reason' => trans('errors.document_not_found')]);
        }
    }

    private function embedDocument(DocumentDescriptor $doc, File $version = null)
    {
        /* File */ $file = $version ?? $doc->file;

        $render = null;
        $preview = null;
        $preview_errors = null;

        $view_data = [
            'document' => $doc,
            'file' => $file,
            'version' => $version,
            'filename_for_download' => $version ? $version->name : $doc->title,
            'pagetitle' => trans('documents.preview.page_title', ['document' => $doc->title]),
            'body_classes' => "preview",
        ];

        try {
            if ($doc->isFileUploadComplete() && $doc->status === DocumentDescriptor::STATUS_COMPLETED) {
                $preview = $this->previewService->preview($file);

                if (method_exists($preview, 'with')) {
                    $preview->with($view_data);
                }

                return view('documents.embed', array_merge($view_data, [
                    'previewable' => $preview,
                    'filename_for_download' => $version ? $version->name : $doc->title,
                    ]));
            }
                
            return view('documents.embed', array_merge($view_data, [
                'preview_errors' => trans('documents.preview.file_not_ready')
            ]));
        } catch (UnsupportedFileException $pex) {
            $preview_errors = trans('documents.preview.not_supported');
        } catch (PreviewGenerationException $pex) {
            Log::error('Preview Generation, failure', ['error' => $pex, 'file' => $file]);
            $preview_errors = trans('documents.preview.error', ['document' => $doc->title]);
        } catch (Exception $pex) {
            Log::error('Preview Generation, failure', ['error' => $pex, 'file' => $file]);
            $preview_errors = trans('documents.preview.error', ['document' => $doc->title]);
        } catch (Throwable $pex) {
            Log::error('Preview Generation, failure', ['error' => $pex, 'file' => $file]);
            $preview_errors = trans('documents.preview.error', ['document' => $doc->title]);
        }

        return view('documents.embed', array_merge($view_data, [
            'preview_errors' => $preview_errors,
        ]));
    }
}
