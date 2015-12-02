<?php namespace KlinkDMS\Http\Controllers\Document;

use KlinkDMS\Http\Requests\StarredRequest;
use KlinkDMS\Http\Controllers\Controller;
use KlinkDMS\Starred;
use KlinkDMS\DocumentDescriptor;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;
use KlinkDMS\Pagination\LengthAwarePaginator as Paginator;

class StarredDocumentsController extends Controller {

	// USER + DESCR ID (INST + LOCAL DOC ID)
	
	/**
	 * [$adapter description]
	 * @var \Klink\DmsAdapter\KlinkAdapter
	 */
	private $service = null;

	private $documentsService = null;
	
	private $searchService = null;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(\Klink\DmsAdapter\KlinkAdapter $adapterService, \Klink\DmsDocuments\DocumentsService $documentsService, \Klink\DmsSearch\SearchService $searchService)
	{

		$this->middleware('auth');

		$this->middleware('capabilities');

		$this->service = $adapterService;
		$this->documentsService = $documentsService;
		
		$this->searchService = $searchService;

	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Guard $auth, \Request $request)
	{
		
		$filtered_ids = false;
		
		$pagination = false;
		
		$limit = \Config::get('dms.items_per_page');
		$page = $request::input('page', 1);
		
		if($this->searchService->hasSearchRequest($request)){
			
			$id_set2 = Starred::with('document')->ofUser($auth->user()->id)->get()->fetch('document.local_document_id')->all();
			
			$filtered_private = $this->searchService->searchForId($request, 'private', $id_set2);
			$filtered_public = $this->searchService->searchForId($request, 'public', $id_set2);
			
			$filtered_ids = new \stdClass;
			$filtered_ids->ids = array_merge($filtered_private->ids, $filtered_public->ids);
			$filtered_ids->term = $filtered_private->term;
			$filtered_ids->total_results = max(array($filtered_private->total_results, $filtered_public->total_results));
			$filtered_ids->filters = null;
			$filtered_ids->facets = null;
			$filtered_ids->page = $filtered_private->page;
			
			// dd(compact('id_set2', 'filtered_private', 'filtered_public', 'filtered_ids'));
//			$filtered_ids->facet_params = $facets_to_apply;
		}
		
		if(!$filtered_ids){
			$total = $auth->user()->starred->count();
			$all_starred_by_me = $auth->user()->starred->forPage($page, $limit)->load('document')->sort(function($e){
				return $e->document->is_public;
			});
			// dd(compact('total', 'all_starred_by_me'));
			$pagination = new Paginator($all_starred_by_me, 
			$total, 
			$limit, $page, [
            	'path'  => $request::url(),
            	'query' => $request::query(),
        	]);
		}
		else {
			
			$all_query = DocumentDescriptor::whereIn('hash', $filtered_ids->ids)->get(array('id'))->fetch('id')->toArray();
			$total = Starred::whereIn('document_id', $all_query)->count();
			$all_starred_by_me = Starred::whereIn('document_id', $all_query)->forPage($page, $limit)->get();

			$pagination = new Paginator($filtered_ids->ids, 
			$total, 
			$limit, $page, [
            	'path'  => $request::url(),
            	'query' => $request::query(),
        	]);
		}

		

		if ($request::ajax() && $request::wantsJson())
		{
		    return response()->json($all_starred_by_me);
		}
		
		// dd($all_starred_by_me);

		return view('documents.starred', array(
			'pagetitle' => trans('starred.page_title'), 
			'filter' => trans('starred.page_title'), 
			'context' => 'starred', 
			'starred' => $all_starred_by_me, 
			'pagination' => ($pagination) ? $pagination : null,
			'search_terms' => ($filtered_ids) ? $filtered_ids->term : '',
			'empty_message' => ($filtered_ids && $all_starred_by_me->count()==0) ? 'Nothing found in stared for "' . $filtered_ids->term . '"'  : trans('starred.empty_message')
		));
	}


	public function show($id)
	{

		$star = Starred::with('document')->findOrFail($id);

		return view('panels.document', array('item' => $star->document));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Guard $auth, StarredRequest $starredRequest)
	{
		
		try{

			$doc = $this->documentsService->getDocument($starredRequest->institution, $starredRequest->descriptor, $starredRequest->visibility);

			$user_id = $auth->user()->id;

			if(!Starred::existsByDocumentAndUserId($doc->id, $user_id)){

				$newStar = Starred::firstOrCreate(array(
					'user_id' => $user_id,
					'document_id' => $doc->id
					));

				return new JsonResponse(array('status' => 'created', 'id' => $newStar->id), 201);

			}
			else {
				return response()->json(array('status' => trans('starred.already_exists')));
			}

			return response()->json();

		}catch(\InvalidArgumentException $ex){

			\Log::error('Error while starring a document', ['context' => 'StarredDocumentsController', 'params' => $starredRequest, 'exception' => $ex]);

			return new JsonResponse(array('error' => trans('starred.errors.invalidargumentexception', ['exception' => $ex->getMessage()])), 422);

		}
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{

		$star = Starred::findOrFail($id);

		$executed = $star->delete();

		if($executed){
			return response()->json( array('status' => 'ok'));
		}

		return response()->json( array('status' => 'error'));

	}

}
