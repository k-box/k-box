<?php

namespace Klink\DmsSearch;

use Illuminate\Contracts\Auth\Guard;
use KBox\RecentSearch;
use KBox\Capability;
use KBox\DocumentDescriptor;
use KBox\Starred;
use KBox\Pagination\SearchResultsPaginator as Paginator;
use Illuminate\Support\Collection;
use Klink\DmsAdapter\KlinkFacetsBuilder;
use Klink\DmsAdapter\Exceptions\KlinkException;
use Klink\DmsAdapter\KlinkFacets;
use Klink\DmsAdapter\KlinkVisibilityType;
use Klink\DmsAdapter\KlinkSearchRequest;
use Klink\DmsAdapter\KlinkSearchResults;
use Exception;
use Illuminate\Support\Arr;
use KBox\Documents\Services\DocumentsService;
use KBox\Group;
use KBox\Project;
use Klink\DmsAdapter\KlinkFilters;
use Log;

class SearchService
{

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
     * 
     * @var \KBox\Documents\Services\DocumentsService
     */
    private $documentsService = null;

    public static $defaultFacets = [
        'public' => [
            KlinkFacets::LANGUAGE,
            KlinkFacets::MIME_TYPE,
            KlinkFacets::UPLOADER,
            KlinkFacets::COPYRIGHT_USAGE_SHORT,
        ],
        'private' => [
            KlinkFacets::LANGUAGE,
            KlinkFacets::MIME_TYPE,
            KlinkFacets::COLLECTIONS,
            KlinkFacets::TAGS,
        ],
    ];

    /**
     * Create a new SearchService instance.
     *
     * @return void
     */
    public function __construct(Guard $auth, \Klink\DmsAdapter\Contracts\KlinkAdapter $adapter, DocumentsService $documentsService)
    {
        $this->auth = $auth;

        $this->adapter = $adapter;
        
        $this->documentsService = $documentsService;
    }

    /**
     * [getRecentSearches description]
     * @return Collection|null The collection of {@see RecentSearch} if user is authenticated, null otherwise
     */
    public function getRecentSearches()
    {
        if (! is_null($this->auth) && $this->auth->check()) {
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
        if ($visibility === 'private' && ! $force) {
            return DocumentDescriptor::local()->private()->count();
        } else {
            return $this->adapter->getDocumentsCount($visibility);
        }
    }

    private function trackSearch($terms)
    {
        try{

            if (! is_null($this->auth) && $this->auth->check() && ! empty($terms) && $terms !== '*') {
                
                // todo: probably is more elegant to use an event and a listener
                $rc = RecentSearch::firstOrNew(['terms' => trim($terms), 'user_id' => $this->auth->user()->id]);
                
                $saved = null;
                
                if ($rc->exists) {
                    $rc->times = $rc->times+1;
                    
                    $saved = $rc->save();
                } else {
                    $rc->times = 0;
                    $saved = $this->auth->user()->searches()->save($rc);
                }
                
                if (! $saved) {
                    Log::warning('Recent search not saved', ['context' => 'SearchService::search', 'param' => func_get_args(), 'user' => $this->auth->user()]);
                }
                
            }
        }catch(Exception $ex){
            Log::error('Error saving recent search for user', ['error'=> $ex]);
        }

    }
    
    /**
     * Perform a search using the search engine
     */
    public function search(SearchRequest $request)
    {   
        Log::info('Search Request', ['request' => $request]);

        $this->trackSearch($request->term);

        

        // merge the default facets for the visibility of the request

        $request->facets(static::$defaultFacets[$request->visibility]);

        /**
         * @var KlinkSearchResults $results
         */
        $results = null;

        $can_star_documents = $this->auth->check() && $this->auth->user()->can(Capability::MAKE_SEARCH);

        $current_user = $this->auth->user();

        try {

            $this->validateCollectionFilters($request->getFilter(KlinkFilters::COLLECTIONS));
            $this->validateProjectFilters($request->getFilter(KlinkFilters::TAGS));

            $results = $this->adapter->search(KlinkSearchRequest::from($request));
            
        } catch (KlinkException $ex) {
            Log::error('KlinkException when searching on K-Link', ['context' => 'SearchService::search', 'param' => func_get_args(), 'exception' => $ex]);
        } catch (\Exception $ex) {
            Log::error('Error searching on K-Link', ['context' => 'SearchService::search', 'param' => func_get_args(), 'exception' => $ex]);
        }

        if ($results && $results instanceof KlinkSearchResults) {

            $items = $results->getResults();

            if($request->visibility === KlinkVisibilityType::KLINK_PRIVATE){
                
                // we are interested in DocumentDescriptor instances as we are serving private results
                $items = $results->getResults()->map(function($result) use($current_user){

                    return DocumentDescriptor::whereUuid($result->uuid)->with(['stars' => function ($query) use($current_user) {
                        $query->where('user_id', $current_user->id);
                    }])->first();
    
                });

            }

            $pagination = new Paginator(
                $results->getTerms() === '*' ? '' : $results->getTerms(),
                $items,
                $request->filters,
                $this->limitFacets($results->getFacets()),
                $results->getTotalResults(),
                $request->limit, $request->page, [
                    'path'  => $request->url,
                    'query' => $request->query,
                ]);
                
            return $pagination;
        }
        
        \Log::error('Unexpected search results response', ['class' => get_class($results), 'results' => $results]);

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
    public function hasSearchRequest(\Request $request)
    {
        return ! ! $request::input('s', false) || ! ! $request::input('fs', false);
    }
    
    
    public function defaultFacets($visibility='private')
    {
        return \Cache::remember('dms_default_facets_'.$visibility, 200, function () use ($visibility) {
            $default_array = static::$defaultFacets[$visibility];

            return $this->limitFacets($this->adapter->facets($default_array, $visibility));
        });
    }

    /**
     * Retrieve aggregations (aka facets) for the specified request
     */
    public function aggregations(SearchRequest $request)
    {
        try {
            // dump($request);

            // TODO: in some cases I want the facets to be bound 
            // to a filter to reduce the case that I see facets 
            // for all documents, but I'm in the starred section 
            // and no document is starred

            // if (! $request->isSearchRequested() && $request->isPageRequested()) {
            //     return $this->defaultFacets($request->visibility);
            // }

            $this->validateCollectionFilters($request->getFilter(KlinkFilters::COLLECTIONS));
            $this->validateProjectFilters($request->getFilter(KlinkFilters::TAGS));
            
            $ft_response = $this->search($request);
            
            if (is_null($ft_response)) {
                Log::error('Null search response for aggregations calculation.', ['request' => $request]);
                return [];
            }

            return $ft_response->facets();
        } catch (Exception $ex) {
            Log::error('Error while calculating aggregations.', ['request' => $request, 'error' => $ex]);
            return [];
        }
    }
    
    /**
     * Limits the language facets based on the configured whitelist
     */
    public function limitFacets($facets)
    {
        $whitelist = array_merge(config('dms.language_whitelist', []), ['__']);
        
        $lang_facets = Arr::first($facets, function ($value, $key) {
            return $key === KlinkFacets::LANGUAGE;
        }, null);
    
        if (empty($lang_facets)) {
            return $facets;
        }
        
        $items_to_keep = [];
        
        foreach ($lang_facets as $item) {
            if (in_array($item->value, $whitelist)) {
                $items_to_keep[] = $item;
            }
        }

        $facets[KlinkFacets::LANGUAGE] = $items_to_keep;
        
        return $facets;
    }

    /**
     * Clean-up the returned filter from the search, based on
     * "was the filter applied by the user?"
     * if the user didn't apply filter, the returning filters will be none
     * if the user applied some filters, the return will only contain those filters
     */
    private function removeFilters($returned, $applied_by_user)
    {
        if (empty($applied_by_user)) {
            return [];
        }

        $cloned = [];

        foreach ($returned as $key => $value) {
            if (in_array($key, $applied_by_user)) {
                // unset($returned[$key]);
                $cloned[$key] = $value;
            }
        }

        return $cloned;
    }
    
    private function validateCollectionFilters($collections)
    {
        foreach ($collections as $collection_id) {
            $collection = Group::find($collection_id);

            if(!is_null($collection) && ! $this->documentsService->isCollectionAccessible($this->auth->user(), $collection)){
                throw new Exception("Collection filter not acceptable");
            }
        }

    }
    
    private function validateProjectFilters($projects)
    {
        foreach ($projects as $project_id) {
            $project = Project::find($project_id);

            if(!is_null($project) && ! Project::isAccessibleBy($project, $this->auth->user())){
                throw new Exception("Project filter not acceptable");
            }
        }

    }
    
    
}
