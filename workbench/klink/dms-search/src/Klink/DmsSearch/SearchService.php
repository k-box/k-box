<?php namespace Klink\DmsSearch;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Auth\Guard;
use KlinkDMS\RecentSearch;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Starred;
use KlinkDMS\Pagination\SearchResultsPaginator as Paginator;
use Illuminate\Support\Collection;

class SearchService {

	/**
	 * The Guard implementation.
	 *
	 * @var Guard
	 */
	protected $auth;

	/**
	 * [$adapter description]
	 * @var \Klink\DmsAdapter\KlinkAdapter
	 */
	private $adapter = null;

	/**
	 * Create a new SearchService instance.
	 *
	 * @return void
	 */
	public function __construct(Guard $auth, \Klink\DmsAdapter\KlinkAdapter $adapter)
	{
		$this->auth = $auth;

		$this->adapter = $adapter;
	}


	/**
	 * Make a K-Link Search
	 * @param  string  $terms the terms to search for
	 * @param  integer $offset the offset to start showing results from (for pagination)
	 * @param  integer $resultsPerPage the number of results per page
	 * @param  string  $visibility the visibility scoper of the search
	 * @param KlinkFacet[] $facets The facets that needs to be retrieved or what will be retrieved. Default null, no facets will be calculated or filtered.
	 * @return \KlinkSearchResult [description]
	 */
	private function _search( $terms, $offset = 0, $resultsPerPage = 10, $visibility = 'public', array $facets = null)
	{
		// dd(compact('terms', 'offset', 'resultsPerPage'));
		try{
			
			if( !is_null($this->auth) && $this->auth->check() && !empty($terms) && $terms !== '*'){
				// TODO: make this an event -> raise event and handle event for saving
				$rc = RecentSearch::firstOrNew(array('terms' => trim($terms), 'user_id' => $this->auth->user()->id));
				
				$saved = null;

				if($rc->exists){

					$rc->times = $rc->times+1;

					$saved = $rc->save();
				}
				else {
					$rc->times = 0;
					$saved = $this->auth->user()->searches()->save($rc);
				}

				if(!$saved){
					\Log::warning('Recent search not saved', array('context' => 'SearchService::search', 'param' => func_get_args(), 'user' => $this->auth->user()));
				}
				
			} 

			// save the query and the action performed

			$results_from_the_core = $this->adapter->getConnection()->search( $terms, $visibility, $resultsPerPage, $offset, $facets );

			$is_starrable = $this->auth->check();

			$current_user = !$is_starrable ?: $this->auth->user();

			

			//TODO: when we will have faceting this part could be speed-up so we could have all the details of the instituions at once
			// TODO: always enable institution facets to speedup the ID to name conversion for all the results in the response (and for the query)

			// TODO: convert foreach into array_map
			
			foreach ($results_from_the_core->items as $res) {
				

				$res->isStarred = false; //default value

				$res->isStarrable = $is_starrable;
				
				$institution = $this->adapter->getInstitution( $res->institutionID );

				if($is_starrable && !is_string($institution)){

					// $institution = $this->adapter->getInstitution( $res->institutionID );

					$cachedDoc = DocumentDescriptor::findByInstitutionAndDocumentId($institution->id, $res->getLocalDocumentID());

					if(!is_null($cachedDoc)){

						$exists = Starred::getByDocumentAndUserId($cachedDoc->id, $current_user->id);

						if(!is_null($exists)){

							$res->isStarred = true;

							$res->starId = $exists->id;
						}
					}
				}

				$res->institutionName = !is_string($institution) ? $institution->name : $institution;

				$res->creationDate = \Carbon\Carbon::createFromFormat( \DateTime::RFC3339, $res->creationDate)->formatLocalized('%A %d %B %Y');

				$res->klink_id = $res->getKlinkId();
			}
			
			// TODO: language code to human understandable name expansion
// dd(compact('terms', 'offset', 'resultsPerPage', 'results_from_the_core'));
			return $results_from_the_core;

		}catch(\KlinkException $ex){

			\Log::error('KlinkException when searching on K-Link', array('context' => 'SearchService::search', 'param' => func_get_args(), 'exception' => $ex));

			return null;
		}catch(\Exception $ex){

			\Log::error('Error searching on K-Link', array('context' => 'SearchService::search', 'param' => func_get_args(), 'exception' => $ex));

			return null;
		}
	}

	/**
	 * [autocomplete description]
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function autocomplete($value='')
	{
		// TODO: autocomplete
		// a mix of recent search and what?
	}


	/**
	 * [getRecentSearches description]
	 * @return [type] [description]
	 */
	public function getRecentSearches()
	{
		
		if( !is_null($this->auth) && $this->auth->check() ){
			return $this->auth->user()->searches();
		} 
		return null;
	}

	/**
	 * Returns the number of indexed documents with the respect to the visibility.
	 *
	 * Public visibility -> all documents inside the K-Link Network
	 *
	 * private visibility -> documents inside institution K-Link Core
	 *
	 * This method uses caching, so be aware that the results you receive might be older than real time
	 * 
	 * @param  string $visibility the visibility (if nothing is specified, a 'public' visibility is considered)
	 * @return integer            the amount of documents indexed
	 */
	public function getTotalIndexedDocuments($visibility = 'public', $force = false)
	{		
		if($visibility === 'private' && !$force){
			return DocumentDescriptor::local()->private()->count();
		}	
		else {
			return $this->adapter->getDocumentsCount($visibility);
		} 
	}
	
	/**
	 * Perform a search using the search engine
	 */
	public function search(SearchRequest $request){
		
		// 1. get the parameters to invoke the adapter search
		// 2. request a count search to get the total number of results (to be used in pagination)
		// 3. perform the real search to get the results
		// 4. return everything that must be returned

		\Log::info('Search Request', ['request' => (string)$request]);

		$results = $this->_search( $request->term, $request->limit * ($request->page - 1), $request->limit, $request->visibility, $request->facets_and_filters->build() );
		
		// $results = $default($filtered_ids, $limit, $page, $total);
		
		// dd($results);
		
		if($results instanceof \KlinkSearchResult){
			
			$total = property_exists($results, 'total_results') ? $results->total_results : property_exists($results, 'numFound') ? $results->numFound : $this->getTotalIndexedDocuments($request->visibility) ; 
			
			$pagination = new Paginator(
				$results->query === '*' ? '' : $results->query,
				$results->items, 
				$this->overcomeCoreBuginFilters($results->filters),
				$this->limitFacets( $results->facets ),
				$total, 
				$request->limit, $request->page, [
					'path'  => $request->url,
					'query' => $request->query,
				]);
			
			return $pagination;
		}
		else {
			\Log::error('Unexpected search results response', ['class' => get_class($results), 'results' => $results]);
		}

		return null;
		
	}
	
	
	
// 	public function searchForId(\Request $request, $visibility = 'private', $idFilter = array(), $groupFilter = array()){
// 		
// 		//idFilter use documentId() on facets buildesr
// 		
// 		if($this->hasSearchRequest($request)){
// 			
// 			
// 			$search_terms = $request::input('s', '*');
// 
// 			$page = $request::input('page', 1);
// 			$limit = 1; //\Config::get('dms.items_per_page');
// 	
// 			$grand_total = $this->getTotalIndexedDocuments($visibility);
// 	
// 	
// 			$facets = array();
// 	
// 			$default_facets_names = ['documentType', 'language'];
// 			
// 			if($visibility=='public'){
// 				$default_facets_names[] = 'institutionId';
// 			}
// 			
// 			if(empty($groupFilter) && $visibility=='private'){
// 				$default_facets_names[] = 'documentGroups';
// 			}
// 	
// 			$fs_builder = \KlinkFacetsBuilder::create();
// 	
// 			$current_filters = null;
// 	
// 			$param_fs = $request::input('fs', null);
// 			$empty_fs = empty($param_fs);
// 			
// 			
// 	
// 			if($request::has('fs') && !$empty_fs){
// 				$fs_names = \KlinkFacetsBuilder::allNames(); //check what we have in the parameters
// 	
// 				// also parameter validation
// 	
// 				$current_names = [];
// 	
// 				$param_inner_fs = null;
// 				$empty_inner_fs = true;
// 				foreach ($fs_names as $fs) {
// 					$param_inner_fs = $request::input($fs, null);
// 					$empty_inner_fs = empty($param_inner_fs);
// 					if($request::has($fs) && !$empty_inner_fs){
// 						$current_names[] = $fs;
// 						// ok valid facet
// 	
// 						$filter_value = $request::input($fs);
// 	
// 						$fs_builder->{$fs}($filter_value, $grand_total, 0);
// 	
// 						$current_filters[$fs] = explode(',', $filter_value);
// 					}
// 				}
// 	
// 				$default_facets_names = array_diff($default_facets_names, $current_names);
// 			}
// 	
// 			// what default facets are missing? we need to add it
// 			foreach ($default_facets_names as $fs) {
// 				
// 				$fs_builder->{$fs}(0);
// 				
// 			}
// 			
// 			if(!empty($idFilter)){
// 				$fs_builder->localDocumentId(implode(',', $idFilter));
// 			}
// 			
// 			if(!empty($groupFilter)){
// 				$fs_builder->documentGroups(implode(',', $groupFilter));
// 			}
// 			
// 			
// 	
// 			
// 	
// 			// >>>> only to support the elastic list given the fact that don't use the facets api
// 			// $all = !!$request::input('all', false);
// 	
// 			// if($all){
// 			// 	$limit = $grand_total;
// 			// }
// 	
// 			// <<<< 
// 			$fake = false;
// 			if(empty($search_terms)){
// 				// showing search page without query, let's request only the facets
// 				$search_terms = '*';
// 				$limit=0;
// 				$fake = true;
// 			}
// 	
// 			// TODO: se ho sia public che private devo fare le due ricerche e poi mettere i risultati separati
// //			$default_facets = \KlinkFacetsBuilder::create()->institution()->documentType()->language()->build(); // usefull for the elastic list, so we setup always that
// 	
// 			$facets_to_apply = $fs_builder->build();
// 			
// 			$test = $this->_search( $search_terms, $limit * ($page - 1), $limit, $visibility, $facets_to_apply );
// 			
// 			if(!is_null($test) && $test->getTotalResults() > 0){
// 				// we have some results
// 				
// 				$mapped = array_map(function($r){
// 					return $r->hash;
// 				}, $test->getResults());
// 				
// //				dd($mapped);
// 
// //var_dump($test->getFacets());
// 				
// 				$return_obj = new \stdClass;
// 				$return_obj->ids = $mapped;
// 				$return_obj->term = $search_terms;
// 				$return_obj->total_results = $test->getTotalResults();
// 				$return_obj->filters = $current_filters;
// 				$return_obj->facets = $test->getFacets();
// 				$return_obj->page = $page;
// 				$return_obj->facet_params = $facets_to_apply;
// 				
// 				return $return_obj;
// 			}
// 			else {
// 				$return_obj = new \stdClass;
// 				$return_obj->ids = array();
// 				$return_obj->term = $search_terms;
// 				$return_obj->total_results = 0;
// 				$return_obj->filters = $current_filters;
// 				$return_obj->facets = $this->defaultFacets($visibility);
// 				$return_obj->page = $page;
// 				$return_obj->facet_params = null;
// 				return $return_obj;
// 			}
// 		}
// 		else {
// 			return false;
// 		}
// 		
// 	}
	
	public function hasSearchRequest(\Request $request){
		return !!$request::input('s', false) || !!$request::input('fs', false);
	}
	
	
	
	
	/*
	 * 
	 *
	 * @param  \Closure  $callback
	 * @return mixed
	 *
	 * @throws \Exception
	*/
// 	public function searchAction(\Request $request, $visibility = 'private', \Closure $filterCallback, \Closure $default, $ignorePublic = false){
// 		
// 		$filtered_ids = false;
// 		$pagination = null;
// 		$limit = 1; \Config::get('dms.items_per_page');
// 		$page = $request::input('page', 1);
// 		$total = $this->getTotalIndexedDocuments($visibility);
// 		
// 		\Log::info('search action', ['request' => $request::all(), 'visibility' => $visibility, 'ignorePublic' => $ignorePublic]);
// 		
// 		$filters_callback_result = (!is_null($filterCallback) && $filterCallback) ? $filterCallback($request, $visibility) : array();
// 		
// 		if($filters_callback_result !== false){
// 			// dd($filters_callback_result);
// 			$total = count($filters_callback_result);
// 		}
// 		
// 		// extract the facets and filters from the request
// 
// 		if($this->hasSearchRequest($request)/* && !($visibility === 'public' && !$ignorePublic)*/){
// 			
// 			
// 			
// 			$filtered_ids = $this->searchForId($request, $visibility, $filters_callback_result);
// 			
// 			$pagination = new Paginator($filtered_ids->ids, 
// 			$filtered_ids->total_results, 
// 			$limit, $filtered_ids->page, [
//             	'path'  => $request::url(),
//             	'query' => $request::query(),
//         	]);
// 			
// 			
// 			$page = $filtered_ids->page;
// 		}
// //		else if($visibility === 'public' /*&& $ignorePublic*/){
// //			$filtered_ids = new \stdClass;
// //			$filtered_ids->ids = array();
// //			$filtered_ids->term = $request::input('s', '*');
// //			$filtered_ids->total_results = 0;
// //			$filtered_ids->filters = null;
// //			$filtered_ids->facets = $this->defaultFacets($visibility);
// //			$filtered_ids->page = $page;
// //		}
// 		
// 		
// 		$queried = $default($filtered_ids, $limit, $page, $total);
// 		
// 		// dd($queried);
// 		
// 		if($queried instanceof \KlinkSearchResult){
// 			
// 			$total = property_exists($queried, 'total_results') ? $queried->total_results : property_exists($queried, 'numFound') ? $queried->numFound : $total ; 
// 			
// 			$pagination = new Paginator($queried->items, 
// 			$total, 
// 			$limit, $page, [
//             	'path'  => $request::url(),
//             	'query' => $request::query(),
//         	]);
// 			
// 			$return_obj = new \stdClass;
// 			$return_obj->documents = new Collection($queried->items);
// 			$return_obj->term = $queried->query === '*' ? '' : $queried->query;
// 			$return_obj->total_results = $queried->itemCount;
// 			$return_obj->filters = $this->overcomeCoreBuginFilters($queried->filters);
// 			$return_obj->facets = $this->limitFacets( $queried->facets );
// 			$return_obj->page = $page;
// 			$return_obj->pagination = $pagination;
// 		}
// 		else {
// 			$pagination = new Paginator($queried,
// 			$filtered_ids ? $filtered_ids->total_results : $total, //$queried->count(), 
// 			$limit, $page, [
//             	'path'  => $request::url(),
//             	'query' => $request::query(),
//         	]);
// 			
// 			$return_obj = new \stdClass;
// 			$return_obj->documents = $queried;
// 			$return_obj->term = $filtered_ids ? $filtered_ids->term : '';
// 			$return_obj->total_results = $filtered_ids ? $filtered_ids->total_results : $queried->count();
// 			$return_obj->filters = $filtered_ids ? $this->overcomeCoreBuginFilters($filtered_ids->filters) : array();
// 			$return_obj->facets = $this->limitFacets( $filtered_ids ? $filtered_ids->facets : $this->defaultFacets($visibility) );
// 			$return_obj->page = $filtered_ids ? $filtered_ids->page : 0;
// 			$return_obj->pagination = $pagination;	
// 		}
// 
// 		return $return_obj;
// 	}
	
	
	public function defaultFacets($visibility='private'){
		// per risolvere il problema del fatto che non posso mettere facets se non c'è ricerca'
		
		return \Cache::remember('dms_default_facets_'.$visibility, 200, function() use($visibility) {
			
			$default_array = \KlinkFacetsBuilder::all();
			
			return $this->limitFacets($this->adapter->getConnection()->facets($default_array, $visibility));
		});
	}
	
	
	public function limitFacets($facets){
		$config = \Config::get('dms.limit_languages_to', false);
		
		if($config !== false && is_string($config)){
			$langs = explode(',', $config);
			
			$lang_facet = $value = array_first($facets, function($key, $value)
			{
			    return $value->name === \KlinkFacet::LANGUAGE;
			}, null);
				
			if(is_null($lang_facet)){
				return $facets;
			}
			
			$items_to_keep = array();
			
			foreach($lang_facet->items as $item){
				if(in_array($item->term, $langs)){
					$items_to_keep[] = $item;
				}
			}
			
			$lang_facet->items = $items_to_keep;
		}
		
		return $facets;
	}
	
	
	private function overcomeCoreBuginFilters($filters){
        
		if(is_null($filters)){
			return $filters;
		}

		//sometimes the filters array is populated with filters raw data and not processed data

		$ints = array_filter(array_keys($filters), function($i){
			return is_int($i);
		});
		
		if(!empty($ints)){
			//let's apply the hack
			$hacked=array();
			foreach($filters as $filter){
				
				$filter = is_object($filter) ? (array) $filter : $filter;
				
				if(isset($filter['options'])){
					
					$key = is_object($filter['options']) ? $filter['options']->key : $filter['options']['key'];
					
		            $vals = str_replace('\\', '', str_replace($filter['field'].':', '', $filter['query']));
		            
		            if(starts_with($vals, '(')){
		                $vals = str_replace('OR', '', str_replace('(', '', str_replace(')', '', $vals)));
						$vals = array_filter(explode(' ', $vals));
		            }
		            else {
		                $vals = array($vals);
		            }

		            $hacked[$key] = $vals;
		        }
			}

			return $hacked;
		}

        return $filters;
    }

}
