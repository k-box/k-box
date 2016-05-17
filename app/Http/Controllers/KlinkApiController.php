<?php namespace KlinkDMS\Http\Controllers;

use KlinkDMS\Http\Requests;
use KlinkDMS\Http\Controllers\Controller;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Institution;
use KlinkDMS\File;
use Illuminate\Http\Request;

/**
 * Controller for Klink API (/klink/*) pages
 */
class KlinkApiController extends Controller {

	private $documentService = null;
	
	private $previewService = null;

	function __construct(\Klink\DmsDocuments\DocumentsService $adapterService, \Klink\DmsPreviews\PreviewsService $preview) {
		$this->documentService = $adapterService;
		$this->previewService = $preview;
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @param  string $action the type of action to perform on the given rseource: thumbnail or document
	 * @return Response
	 */
	public function show(Request $request, $id, $action)
	{

		// no need for input syntax validation, already performed by Laravel when invoking this controller

		$doc = DocumentDescriptor::withTrashed()->where('local_document_id', $id)->with('file')->first();

		if(is_null($doc) || (!is_null($doc) && !$doc->isMine())){
			\App::abort(404, trans('errors.document_not_found'));
		}
        
       
        


		if($action==='document'){
            
            if( $doc->trashed() ){
                \App::abort(404, trans('errors.document_not_found'));
            }
            else if( !$doc->isPublic() && is_null( $request->user() ) ){
                return redirect()->guest('/auth/login');
            }

			return $this->getDocument($request, $doc);

		}
		else if($action==='thumbnail'){

			return $this->getThumbnail($request, $doc);

		}

		return response('Forbidden.', 403);
	}



	private function getThumbnail(Request $request, DocumentDescriptor $doc){
	
		/* File */ $file = $doc->file;
		
		if(is_null($file)){
			$file = File::withTrashed()->findOrFail($doc->file_id);
		}

		$response = response()->make();

		// mark the response as either public or private
		$response->setPublic();

		// set the private or shared max age
		$response->setMaxAge(3600);
		$response->setSharedMaxAge(3600);


		$response->setETag(substr($file->hash, 0, 32));
		$response->setLastModified($file->updated_at);

	    // Set response as public. Otherwise it will be private by default.
	    $response->setPublic();

	    // Check that the Response is not modified for the given Request
	    if ($response->isNotModified($request)) {
	        // return the 304 Response immediately
	        return $response;
	    }
        
        if( !$doc->isPublic() && is_null( $request->user() ) ){
            $response->setContent( file_get_contents( public_path('images/document.png') ) );
        }
        else {

            if(empty($file->thumbnail_path)){

                $is_webpage = $doc->isRemoteWebPage();
                
                $t_path = $this->documentService->generateThumbnail($file, 'default', true, $is_webpage);

                $response->setContent( file_get_contents( $t_path ) );
                
            }
            else if(@is_file($file->thumbnail_path)){
                $response->setContent( file_get_contents( $file->thumbnail_path ) );
            }
            else {
                $response->setContent( file_get_contents( public_path('images/document.png') ) );
            }
        }

	    $response->header('Content-Type', 'image/png');

	    return $response;

	}


	private function getDocument(Request $request, DocumentDescriptor $doc)
	{
		
		if($doc->trashed()){
			\App::abort(404, trans('errors.document_not_found'));
		}

		/* File */ $file = $doc->file;

		if($request->has('preview')){
			
			$extension = \KlinkDocumentUtils::getExtensionFromMimeType($file->mime_type);

			$render = $this->previewService->render($file);

			return view('documents.preview', [
				'document' => $doc, 
				'file' => $file,
				'type' =>  $doc->document_type,
				'render' => $render,
				'extension' => $extension,
				'body_classes' => 'preview ' . $doc->document_type,
				'pagetitle' => trans('documents.preview.page_title', ['document' => $doc->title]),
			]);
		}

        $embed = $request->input('embed', false);
		
        $headers = array(
            'Content-Type' => $file->mime_type
        );


        $response = new \Symfony\Component\HttpFoundation\BinaryFileResponse($file->path, 200, $headers, true, null);
        $name = $file->name;
        if (! is_null($name)) {
            return $response->setContentDisposition(( !$embed ? 'attachment' : 'inline' ) , $name, str_replace('%', '', \Illuminate\Support\Str::ascii($name)));
        }

        return $response;


        // return response()->download( $file->path, mb_convert_encoding( $file->name, 'ASCII', 'auto'), $headers, ( !$embed ? 'attachment' : 'inline' ) );

	}


}

