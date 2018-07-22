<?php

namespace KBox\Http\Controllers\Document;

use KBox\DocumentDescriptor;
use KBox\Jobs\ReindexDocument;
use KBox\Http\Controllers\Controller;
use Klink\DmsAdapter\KlinkDocumentUtils;
use Klink\DmsAdapter\KlinkVisibilityType;

class DocumentVersionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('capabilities');
    }

    /**
     * Display the specified document version.
     *
     * Currently it redirects to the DocumentPreviewController route for file preview
     *
     * @param  string $document_id The identifier of the document
     * @param  string  $version_uuid The uuid of the file version to show
     * @return \Illuminate\Http\Response
     */
    public function show($document_id, $version)
    {
        $document = DocumentDescriptor::findOrFail($document_id);
        
        return redirect()->route('documents.preview', [
            'uuid' => $document->uuid,
            'versionUuid' => $version
        ]);
    }

    /**
     * Remove the specified version. The related file is permanently deleted.
     *
     * @param  string  $document The document to which the version pertains
     * @param  string  $version_uuid the file UUID that corresponds to the version that needs to be removed
     * @return \Illuminate\Http\Response
     */
    public function destroy($document_id, $version_uuid)
    {
        $document = DocumentDescriptor::findOrFail($document_id);

        $versions = $document->fileVersions()->keyBy('uuid');
        $versions_index_to_uuid = $versions->keys();
        $versions_uuid_to_index = $versions_index_to_uuid->flip();

        if (! $versions->has($version_uuid)) {
            \abort(404, trans('errors.document_not_found'));
        }

        if ($versions->count() === 1) {
            // todo: return a custom error string
            \abort(404, trans('errors.document_not_found'));
        }

        $version_to_remove = $versions->get($version_uuid);
        $version_to_remove_index = $versions_uuid_to_index->get($version_uuid);

        // if revision_of is null it was the first version, so the version after revision_of must be set to null

        $version_to_apply = null;
        $requires_reindex = false; // if reindexing of the document descriptor is required

        if ($version_to_remove_index === 0) {
            // wants to delete the last revision
            // grab the first previous revision and set it as last
            // trigger the reindex as the file content changed
            $uuid_of_the_version_to_apply = $versions_index_to_uuid[$version_to_remove_index+1];
            $version_to_apply = $versions->get($uuid_of_the_version_to_apply);
            $requires_reindex = true;
        } elseif ($version_to_remove_index === $versions_index_to_uuid->count()-1) {
            // wants to delete the oldest revision
            // grab the parent one and set the revision_of field to null
            $uuid_of_the_parent_version = $versions_index_to_uuid[$version_to_remove_index-1];
            $version_to_apply = $versions->get($uuid_of_the_parent_version);
            $version_to_apply->revision_of = null;
            $version_to_apply->save();
        } else {
            // the version to delete is in the middle
            // grab previous + next and link them together
            $uuid_of_the_parent_version = $versions_index_to_uuid[$version_to_remove_index-1];
            $uuid_of_the_next_version = $versions_index_to_uuid[$version_to_remove_index+1];
            $parent_version = $versions->get($uuid_of_the_parent_version);
            $parent_version->revision_of = $versions->get($uuid_of_the_next_version)->id;
            $parent_version->save();
        }

        if ($version_to_apply) {
            // updating the document descriptor information
            $document->file_id = $version_to_apply->id;
            $document->mime_type = $version_to_apply->mime_type;
            $document->document_type = KlinkDocumentUtils::documentTypeFromMimeType($version_to_apply->mime_type);
            $document->hash = $version_to_apply->hash;
            $document->save();

            if ($requires_reindex) {
                dispatch(new ReindexDocument($document->fresh(), KlinkVisibilityType::KLINK_PRIVATE));
            }
        }
            
        $version_to_remove->forceDelete();

        return redirect()->route('documents.edit', $document->id)->with([
            'flash_message' => trans('administration.documentlicenses.available.saved')
        ]);
    }
}
