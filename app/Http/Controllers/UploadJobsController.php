<?php

namespace KlinkDMS\Http\Controllers;

use Illuminate\Http\Request;
use KlinkDMS\File;
use Avvertix\TusUpload\TusUploadRepository;
use Avvertix\TusUpload\Http\Controllers\TusUploadQueueController;

class UploadJobsController extends TusUploadQueueController
{
    public function show(TusUploadRepository $uploads, Request $request, $id)
    {
        $upload_request = $uploads->findByUploadRequest($request->user()->id, $id);

        if (! $upload_request) {
            return redirect()->back();
        }

        $collection = $upload_request->metadata && isset($upload_request->metadata['collection']) ? $upload_request->metadata['collection'] : null;

        $file = File::where('request_id', $id)->first();

        if (! $file) {
            return redirect()->back();
        }

        if ($collection) {
            return redirect()->route('documents.groups.show', [ 'id' => $collection, 'highlight' => $file->document->id]);
        }

        return redirect()->route('documents.index', ['highlight' => $file->document->id]);
    }
}
