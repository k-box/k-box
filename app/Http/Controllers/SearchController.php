<?php namespace KlinkDMS\Http\Controllers;

use KlinkDMS\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use KlinkDMS\Traits\Searchable;
use KlinkDMS\Exceptions\ForbiddenException;
use KlinkDMS\Option;

class SearchController extends Controller {
	
	use Searchable;

	/*
	|--------------------------------------------------------------------------
	| Search Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "search results page".
	|
	*/


	/**
	 * [$adapter description]
	 * @var \Klink\DmsAdapter\KlinkAdapter
	 */
	private $service = null;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(\Klink\DmsSearch\SearchService $searchService)
	{
		$this->middleware('auth', ['only' => ['autocomplete', 'recent']]);

		$this->service = $searchService;
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index(Guard $auth, Request $request)
	{

		$is_klink_public_enabled = !!Option::option(Option::PUBLIC_CORE_ENABLED, false);

		if(!$is_klink_public_enabled)
		{
			throw new ForbiddenException('Public search disabled');
		}

		$req = $this->searchRequestCreate($request);
		
		
		$req->visibility( \KlinkVisibilityType::KLINK_PUBLIC );
		

		$grand_total = $this->service->getTotalIndexedDocuments($req->visibility);

		$test = $all = $this->search($req);
		

		if ($request->wantsJson())
		{

			if(!is_null($test)){
				return response()->json($test);
			}
			else {
				return response('Error', 500);
			}
   
		}

		$result_facets = array();

		if(!is_null($test)){

			$result_facets = $test->facets();

		}

		return view('search', [
			'classes' => 'page search',
			'pagetitle' => trans('search.page_title'),
			'search_error' => is_null($test),
			'search_terms' => $req->term,
			'results' => $test->items(),
			'total_results' => $test->total(),
			'pagination' => $test,
			'klink_indexed_documents_count' => $grand_total,
			'current_visibility' => $req->visibility,
			'filters' => $test->filters(),
			'filter' => network_name(),
			'facets' => $result_facets,
			'only_facets' => false,
			]);
	}

	/**
	 * Ajax based route for getting the autocomplete for a search query while the user is typing
	 * @return Response
	 */
	public function autocomplete(Guard $auth)
	{
		
		$search_terms = e(\Request::input('s', null));

		$recent = null;
		$starred = null;

		$uid = $auth->user()->id;

		if(is_null($search_terms)){
			
			$recent = \KlinkDMS\RecentSearch::ofUser($uid)->take(5)->orderBy('updated_at', 'desc')->get();

		}
		else {

			$recent = \KlinkDMS\RecentSearch::ofUser($uid)->thatContains($search_terms)->take(5)->orderBy('updated_at', 'desc')->get();

		}


		if(!is_null($search_terms)){
			
			$starred = $auth->user()->starred()->whereHas('document', function($query) use ($search_terms)
			{
			    $query->where('title', 'like', '%'. $search_terms .'%');

			})->take(2)->orderBy('created_at', 'desc')->with('document')->get();

		}

		return response()->json([ 'recent' => $recent, 'starred' => $starred]);
	}


	/**
	 * Retrieve all the searches made by the logged-in user
	 * @param  Guard  $auth [description]
	 * @return RecentSearch[]       [description]
	 */
	public function recent(Guard $auth, Request $request){

		$recents = $auth->user()->searches()->get();

		if ($request->wantsJson())
		{

			if(!is_null($recents)){
				return response()->json($recents);
			}
			else {
				return response('Error', 500);
			}

		    
		}
		else {
			dd($recents);
		}

	}


}
