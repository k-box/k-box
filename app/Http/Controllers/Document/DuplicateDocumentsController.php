<?php

namespace KBox\Http\Controllers\Document;

use DB;
use Log;
use Exception;
use KBox\DuplicateDocument;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use KBox\Http\Controllers\Controller;
use Klink\DmsDocuments\DocumentsService;
use Illuminate\Contracts\Auth\Guard as AuthGuard;

class DuplicateDocumentsController extends Controller
{
    private $service;

    public function __construct(DocumentsService $documentsService)
    {
        $this->middleware('auth');

        $this->middleware('capabilities');

        $this->service = $documentsService;
    }

    /**
     * Resolve the duplicate document conflict by trashing the new upload
     * and add the existing one to the collections where the new upload
     * was added
     */
    public function destroy(AuthGuard $auth, Request $request, $id)
    {
        $user = $auth->user();

        $duplicate = DuplicateDocument::of($user)->find($id);

        if (is_null($duplicate)) {
            if ($request->wantsJson()) {
                $status = ['status' => 'error', 'message' => 'Forbidden'];
                return new JsonResponse($status, 403);
            }
    
            return redirect()->back();
        }

        if ($duplicate->resolved) {
            if ($request->wantsJson()) {
                $status = ['status' => 'error', 'message' => trans('documents.duplicates.errors.already_resolved')];
                return new JsonResponse($status, 400);
            }
    
            return redirect()->back();
        }
        
        if ($duplicate->document->trashed()) {
            if ($request->wantsJson()) {
                $status = ['status' => 'error', 'message' => trans('documents.duplicates.errors.resolve_with_trashed_document')];
                return new JsonResponse($status, 400);
            }
    
            return redirect()->back();
        }

        try {
            $collections = $this->service->getDocumentCollections($duplicate->document, $user);

            if (! $collections->isEmpty()) {
                DB::transaction(function () use ($collections, $user, $duplicate) {
                    $collections->each(function ($c) use ($user, $duplicate) {
                        $this->service->addDocumentToGroup($user, $duplicate->duplicateOf, $c, false);
                    });
                    try {
                        $this->service->triggerReindex($duplicate->duplicateOf);
                    } catch (Exception $ex) {
                        Log::warning('Reindex not triggered or failed after duplicates handling.');
                    }
                });
            }
            
            $this->service->deleteDocument($user, $duplicate->document);

            $duplicate->resolved = true;
            $duplicate->save();

            if ($request->wantsJson()) {
                $status = ['status' => 'ok', 'message' => ''];
                return new JsonResponse($status, 200);
            }

            return redirect()->route('documents.edit', $duplicate->duplicateOf->id);
        } catch (Exception $ex) {
            Log::error('Duplicate resolution error', ['error' => $ex, 'duplicate' => $id]);

            if ($request->wantsJson()) {
                $status = ['status' => 'error', 'message' => $ex->getMessage()];
                return new JsonResponse($status, 400);
            }

            return redirect()->route('documents.edit', $duplicate->document->id);
        }
    }
}
