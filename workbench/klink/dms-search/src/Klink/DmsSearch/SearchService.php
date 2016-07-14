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

			

			// TODO: when we will have faceting this part could be speed-up so we could have all the details of the instituions at once
			// TODO: always enable institution facets to speedup the ID to name conversion for all the results in the response (and for the query)
			
			foreach ($results_from_the_core->items as $res) {
				

				$res->isStarred = false; //default value

				$res->isStarrable = $is_starrable;
				
				$institution = $this->adapter->getInstitution( $res->institutionID );

				if($is_starrable && !is_string($institution)){

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

				$res->creationDate = localized_date_short(\Carbon\Carbon::createFromFormat( \DateTime::RFC3339, $res->creationDate));

				$res->klink_id = $res->getKlinkId();
			}
			
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
		
		// dd($results->facets);
		
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
	
	

	/**
     * Check if a request has search parameters in inputs.
     *
     * Check if has `s` or `fs` parameter
     *
     * @param \Request $request the request
     * @return boolean true if has search parameters, false otherwise
     */
	public function hasSearchRequest(\Request $request){
		return !!$request::input('s', false) || !!$request::input('fs', false);
	}
	
	
	public function defaultFacets($visibility='private'){
		// per risolvere il problema del fatto che non posso mettere facets se non c'è ricerca'
		
		return \Cache::remember('dms_default_facets_'.$visibility, 200, function() use($visibility) {
			
			$default_array = \KlinkFacetsBuilder::all();
			
			return $this->limitFacets($this->adapter->getConnection()->facets($default_array, $visibility));
		});
	}
	
	/**
     * Limits the language facets based on the configuration `dms.limit_languages_to`
     */
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
