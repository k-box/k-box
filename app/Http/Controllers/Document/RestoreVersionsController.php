<?php

namespace KBox\Http\Controllers\Document;

use Log;
use Illuminate\Http\Request;
use KBox\DocumentDescriptor;
use KBox\Jobs\ReindexDocument;
use Illuminate\Http\JsonResponse;
use KBox\Http\Controllers\Controller;
use Klink\DmsAdapter\KlinkDocumentUtils;
use Klink\DmsAdapter\KlinkVisibilityType;

class RestoreVersionsController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');

        // $this->middleware('capabilities');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($document_id, $version_uuid)
    {
        $document = DocumentDescriptor::findOrFail($document_id);

        $versions = $document->fileVersions()->keyBy('uuid');
        $versions_index_to_uuid = $versions->keys();
        $versions_uuid_to_index = $versions_index_to_uuid->flip();

        if (! $versions->has($version_uuid)) {
            Log::warning("Version restore: requested version {$version_uuid} cannot be found for document {$document_id}");
            \abort(404, trans('errors.document_not_found'));
        }

        if ($versions->count() === 1) {
            Log::warning("Version restore: document {$document_id} has only one version");

            if (request()->wantsJson()) {
                $status = ['status' => 'error', 'message' => trans('documents.restore.restore_version_error_only_one_version')];
                return new JsonResponse($status, 200);
            }

            \abort(404, trans('errors.document_not_found'));
        }

        $version_to_restore = $versions->get($version_uuid);
        $version_to_restore_index = $versions_uuid_to_index->get($version_uuid);

        // if revision_of is null it was the first version, so the version after revision_of must be set to null

        $versions_to_delete = null;

        if ($version_to_restore_index > 0) {
            // the version to delete is in the middle
            // grab previous + next and link them together
            $uuid_of_versions_to_delete = $versions_index_to_uuid->splice(0, $version_to_restore_index);

            $versions_to_delete = $versions->whereIn('uuid', $uuid_of_versions_to_delete);
        }

        // updating the document descriptor information
        $document->file_id = $version_to_restore->id;
        $document->mime_type = $version_to_restore->mime_type;
        $document->document_type = KlinkDocumentUtils::documentTypeFromMimeType($version_to_restore->mime_type);
        $document->hash = $version_to_restore->hash;
        $document->save();
        
        dispatch(new ReindexDocument($document->fresh(), KlinkVisibilityType::KLINK_PRIVATE));
        
        if ($versions_to_delete && ! $versions_to_delete->isEmpty()) {
            $versions_to_delete->each->forceDelete();
        }

        if (request()->wantsJson()) {
            $status = ['status' => 'ok', 'message' => ''];
            return new JsonResponse($status, 200);
        }

        return redirect()->route('documents.edit', $document->id)->with([
            'flash_message' => trans('administration.documentlicenses.available.saved')
        ]);
    }
}
