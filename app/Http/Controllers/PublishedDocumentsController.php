<?php

namespace KBox\Http\Controllers;

use Exception;
use KBox\Publication;
use Illuminate\Http\Request;
use KBox\DocumentDescriptor;
use KBox\Exceptions\PublishingOperationInProgressException;

class PublishedDocumentsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('capabilities');
    }
 
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $document = DocumentDescriptor::findOrFail($request->input('document_id'));

            if ($document->hasPendingPublications()) {
                throw new PublishingOperationInProgressException(trans('share.dialog.publish_already_in_progress'));
            }

            $document->publish($request->user());

            return response()->json(['descriptor' => $document, 'publication' => $document->publication()]);
        } catch (Exception $ex) {
            return response()->json(['status'=> 'error', 'error' => $ex->getMessage()]);
        }
    }

 
    /**
     * Remove the specified resource from storage.
     *
     * @param  \KBox\DocumentDescriptor  $documentDescriptor
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $document = DocumentDescriptor::findOrFail($id);
            
            if ($document->hasPendingPublications()) {
                throw new PublishingOperationInProgressException(trans('share.dialog.publish_already_in_progress'));
            }
        
            $document->unpublish(request()->user());
            
            return response()->json(['descriptor' => $document, 'publication' => $document->publication()]);
        } catch (Exception $ex) {
            return response()->json(['status'=> 'error', 'error' => $ex->getMessage()]);
        }
    }
}
