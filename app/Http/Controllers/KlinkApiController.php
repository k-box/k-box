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

		$doc = DocumentDescriptor::withTrashed()->fromKlinkId(Institution::current()->id, $id)->with('file')->first();

		if(is_null($doc)){
			\App::abort(404, trans('errors.document_not_found'));
		}


		if($action==='document'){

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
			
			$extension = \KlinkDocumentUtils::getExtensionFromMimeType($doc->mime_type);

			$render = $this->previewService->render($file);
// dd(compact('extension', 'render'));
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

	    $response->setContent( file_get_contents( $file->path ) );
	    $response->header('Content-Type', $file->mime_type);
		
		$embed = $request->input('embed', false);
		
		if(!$embed){
	    	$response->header("content-disposition", "attachment; filename='" + $file->name +"'");
		}

	    return $response;

	}


}







		    //     else if( 'thumbnail' === $output ){

		    //     	//if is a post or a page and has a featured image, use the featured image
		    //     	//otherwise a screenshot of the page might be used

		    //     	if( has_post_thumbnail( $post->ID) ){

		    //     		$post_thumbnail_id = get_post_thumbnail_id( $post->ID );

		    //     		$real_file = get_attached_file( $post_thumbnail_id );

		    //     		//get the last-modified-date of this very file
						// $lastModified=filemtime($real_file);
						// //get a unique hash of this file (etag)
						// $etagFile = md5_file($real_file);
						// //get the HTTP_IF_MODIFIED_SINCE header if set
						// $ifModifiedSince=(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false);
						// //get the HTTP_IF_NONE_MATCH header if set (etag: unique file hash)
						// $etagHeader=(isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);

						// //set last-modified header
						// @header("Last-Modified: ".gmdate("D, d M Y H:i:s", $lastModified)." GMT");
						// //set etag-header
						// @header("Etag: $etagFile");
						// //make sure caching is turned on
						// @header('Cache-Control: public');

						// //check if page has changed. If not, send 304 and exit
						// if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])==$lastModified || $etagHeader == $etagFile)
						// {
						//        @header("HTTP/1.1 304 Not Modified");
						//        die();
						// }

		    //     		@header( 'Content-Type: '. get_post_mime_type( $post_thumbnail_id ) .'; ' );
		    //     		echo file_get_contents( $real_file );

		    //     	}
		    //     	else {

		    //     		$real_file = get_attached_file( $post->ID );

		    //     		try{

		    //     			$thumbFolder = Klink_Adapter::get_folders('thumbs');

		    //     			$real_thumb_file = $thumbFolder . '/' . $kid . '.png';

		    //     			if( !file_exists( $real_thumb_file ) ){

		    //     				KlinkWordpressUtils::generateThumbnail( $real_file, $real_thumb_file );

		    //     				//get the last-modified-date of this very file
						// 		$lastModified=filemtime($real_thumb_file);
						// 		//get a unique hash of this file (etag)
						// 		$etagFile = md5_file($real_thumb_file);


		    //     				$image = wp_get_image_editor( $real_thumb_file ); // Return an implementation that extends <tt>WP_Image_Editor</tt>

						// 		if ( ! is_wp_error( $image ) ) {
						// 		    $image->resize( 300, 500, false );
						// 		    $image->save( $real_thumb_file );
						// 		}

						// 		//set last-modified header
						// 		@header("Last-Modified: ".gmdate("D, d M Y H:i:s", $lastModified)." GMT");
						// 		//set etag-header
						// 		@header("Etag: $etagFile");
						// 		@header( "Cache-Control: max-age=2592000, public" );
			   //      			@header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
			   //      			@header('Pragma: public');

		    //     			}
		    //     			else {

		    //     				//get the last-modified-date of this very file
						// 		$lastModified=filemtime($real_thumb_file);
						// 		//get a unique hash of this file (etag)
						// 		$etagFile = md5_file($real_thumb_file);
						// 		//get the HTTP_IF_MODIFIED_SINCE header if set
						// 		$ifModifiedSince=(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false);
						// 		//get the HTTP_IF_NONE_MATCH header if set (etag: unique file hash)
						// 		$etagHeader=(isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);

						// 		//set last-modified header
						// 		@header("Last-Modified: ".gmdate("D, d M Y H:i:s", $lastModified)." GMT");
						// 		//set etag-header
						// 		@header("Etag: $etagFile");
						// 		//make sure caching is turned on
						// 		@header('Cache-Control: public');

						// 		//check if page has changed. If not, send 304 and exit
						// 		if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])==$lastModified || $etagHeader == $etagFile)
						// 		{
						// 		       @header("HTTP/1.1 304 Not Modified");
						// 		       die();
						// 		}


		    //     			}

		        			

		    //     		}catch(KlinkException $kex ){

		    //     			error_log( 'Thumbnail generation error ' . $kex->getMessage() );

		    //     			$real_thumb_file = plugin_dir_path( __FILE__ ) . 'images/klink_default_thumbnail.png';

		    //     		}catch(Exception $kex ){

		    //     			error_log( 'Thumbnail generation error ' . $kex->getMessage() );

		    //     			$real_thumb_file = plugin_dir_path( __FILE__ ) . 'images/klink_default_thumbnail.png';

		    //     		}

		    //     		@header( 'Content-Type: image/png; ' );

		    //     		echo file_get_contents( $real_thumb_file );

		    //     	}

		        	
		    //     	die();
		    //     }