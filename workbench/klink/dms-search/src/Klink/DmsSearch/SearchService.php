<?php

namespace Klink\DmsSearch;

use Illuminate\Contracts\Auth\Guard;
use KlinkDMS\RecentSearch;
use KlinkDMS\Capability;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Starred;
use KlinkDMS\Pagination\SearchResultsPaginator as Paginator;
use Illuminate\Support\Collection;
use Klink\DmsAdapter\KlinkFacetsBuilder;
use Klink\DmsAdapter\Exceptions\KlinkException;
use Klink\DmsAdapter\KlinkFacets;
use Klink\DmsAdapter\KlinkVisibilityType;
use Klink\DmsAdapter\KlinkSearchRequest;
use Klink\DmsAdapter\KlinkSearchResults;
use Exception;
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

    public static $defaultFacets = [
        'public' => [
            KlinkFacets::LANGUAGE,
            KlinkFacets::MIME_TYPE,
            KlinkFacets::UPLOADER,
        ],
        'private' => [
            KlinkFacets::LANGUAGE,
            KlinkFacets::MIME_TYPE,
            KlinkFacets::COLLECTIONS,
            KlinkFacets::PROJECTS,
        ],
    ];

    /**
     * Create a new SearchService instance.
     *
     * @return void
     */
    public function __construct(Guard $auth, \Klink\DmsAdapter\Contracts\KlinkAdapter $adapter)
    {
        $this->auth = $auth;

        $this->adapter = $adapter;
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
        Log::info('Search Request', ['request' => (string)$request]);

        $this->trackSearch($request->term);

        /**
         * @var KlinkSearchResults $results
         */
        $results = null;

        $can_star_documents = $this->auth->check() && $this->auth->user()->can(Capability::MAKE_SEARCH);

        $current_user = $this->auth->user();

        try {

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
                $items = $results->getResults()->map(function($result){
    
                    // TODO: preload the starred relation if this document is starred by the logged in user

                    return DocumentDescriptor::whereUuid($result->uuid)->first();
    
                });

            }

            $pagination = new Paginator(
                $results->getTerms() === '*' ? '' : $results->getTerms(),
                $items,
                $request->explicit_filters, // $results->getFilters()
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
        // TODO: this can be simplified by calling directly the KlinkAdapter::facets method
        try {
            if (! $request->is_facets_forced && ! $request->isSearchRequested() && $request->isPageRequested()) {
                return $this->defaultFacets($request->visibility);
            }
            
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
     * Limits the language facets based on the configuration `dms.limit_languages_to`
     */
    public function limitFacets($facets)
    {
        $config = \Config::get('dms.limit_languages_to', false);
        
        if ($config !== false && is_string($config) && ! is_null($facets)) {
            $langs = explode(',', $config);
            
            $lang_facet = $value = array_first($facets, function ($value, $key) {
                return $value->name === KlinkFacets::LANGUAGE;
            }, null);
                
            if (is_null($lang_facet)) {
                return $facets;
            }
            
            $items_to_keep = [];
            
            foreach ($lang_facet->items as $item) {
                if (in_array($item->term, $langs)) {
                    $items_to_keep[] = $item;
                }
            }
            
            $lang_facet->items = $items_to_keep;
        }
        
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
    
    
    
}
