<?php namespace KlinkDMS\Http\Controllers;

use KlinkDMS\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Contracts\Auth\Guard;

class SearchController extends Controller {

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
	public function index(Guard $auth, \Request $request)
	{

		$search_terms = $request::input('s', '');

		$page = $request::input('page', 1);
		$limit = \Config::get('dms.items_per_page');
		$visibility = $auth->check() ? $request::input('visibility', 'private') : 'public';

		$grand_total = $this->service->getTotalIndexedDocuments($visibility);


		$facets = array();

		$default_facets_names = ['documentType', 'institutionId', 'language'];

		$fs_builder = \KlinkFacetsBuilder::create();

		$current_filters = null;

		$param_fs = $request::input('fs', null);
		$empty_fs = empty($param_fs);

		if($request::has('fs') && !$empty_fs){
			$fs_names = \KlinkFacetsBuilder::allNames(); //check what we have in the parameters

			// also parameter validation

			$current_names = [];

			$param_inner_fs = null;
			$empty_inner_fs = true;
			foreach ($fs_names as $fs) {
				$param_inner_fs = $request::input($fs, null);
				$empty_inner_fs = empty($param_inner_fs);
				if($request::has($fs) && !$empty_inner_fs){
					$current_names[] = $fs;
					// ok valid facet

					$filter_value = $request::input($fs);

					$fs_builder->{$fs}($filter_value, $grand_total, 0);

					$current_filters[$fs] = explode(',', $filter_value);
				}
			}

			$default_facets_names = array_diff($default_facets_names, $current_names);
		}

		// what default facets are missing? we need to add it
		foreach ($default_facets_names as $fs) {
			
			$fs_builder->{$fs}(0);
			
		}

		

		// >>>> only to support the elastic list given the fact that don't use the facets api
		// $all = !!$request::input('all', false);

		// if($all){
		// 	$limit = $grand_total;
		// }

		// <<<< 
		$fake = false;
		if(empty($search_terms)){
			// showing search page without query, let's request only the facets
			$search_terms = '*';
			$limit=0;
			$fake = true;
		}


		$default_facets = \KlinkFacetsBuilder::create()->institution()->documentType()->language()->build(); // usefull for the elastic list, so we setup always that

		$facets_to_apply = $fs_builder->build();
		
		$test = $this->service->search( $search_terms, $limit * ($page - 1), $limit, $visibility, $facets_to_apply );
		

		if ($request::wantsJson())
		{

			if(!is_null($test)){
				return response()->json($test);
			}
			else {
				return response('Error', 500);
			}
   
		}

		if($fake){
			$search_terms = '';
		}


		$pagination = new Paginator(is_null($test) ? array(): $test->getResults(), 
			is_null($test) ? 0 : $test->getTotalResults(), 
			10, $page, [
            	'path'  => \Request::url(),
            	'query' => \Request::query(),
        	]);

		$result_facets = array();

		// $filters_for = \Session::get('dms.search.facets.for', '');

		if(!is_null($test) /* && $filters_for != $search_terms . $visibility */){

			$result_facets = $this->service->limitFacets($test->getFacets());

		// 	\Session::put('dms.search.facets.for', $search_terms . $visibility);
		// 	\Session::put('dms.search.facets.value', $result_facets);

		}
		// else if(!is_null($test) && $filters_for == $search_terms . $visibility) {
			
		// 	$result_facets = \Session::get('dms.search.facets.value');
			
		// }

		return view('search', [
			'classes' => 'page search',
			'pagetitle' => trans('search.page_title'),
			'search_error' => is_null($test),
			'search_terms' => $search_terms,
			'results' => is_null($test) ?: $test->getResults(),
			'total_results' => is_null($test) ?: $test->getTotalResults(),
			'search_time' => is_null($test) ?: $test->getSearchTime(),
			'pagination' => $pagination,
			'klink_indexed_documents_count' => $grand_total,
			'current_visibility' => $visibility,
			'filters' => $current_filters,
			'facets' => $result_facets,
			'only_facets' => $fake,
			]);
	}

	/**
	 * Ajax based route for getting the autocomplete for a search query while the user is typing
	 * @return [type] [description]
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
	public function recent(Guard $auth){

		$recents = $auth->user()->searches()->get();

		if (\Request::wantsJson())
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
