<?php

namespace Klink\DmsSearch;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use BadMethodCallException;
use KlinkFacet;
use KlinkFacetsBuilder;
use KlinkVisibilityType;

/**
 * Search Request builder.
 *
 * Facilitate the construction of a search request tha comes from an {@see Illuminate\Http\Request} or is entirely created with code.
 *
 * To start creating a SearchRequest instance please use SearchRequest::create()
 */
class SearchRequest
{
    protected $term = '*';
    
    protected $visibility = KlinkVisibilityType::KLINK_PRIVATE;
    
    protected $is_search_request = false;
    
    protected $is_facets_forced = false;
    
    protected $page = 1;
    
    protected $limit = 12;
    
    protected $filters = null;
    /**
     * Tells what filters are added directly by the user
     */
    protected $explicit_filters = null;
    
    protected $facets = null;
    
    protected $on_collections = null;
    
    protected $in_documents = null;
    
    protected $in_projects = null;
    
    protected $url = null;
    protected $query = null;

    /**
     * The ID of the document to highlight in a collection/section
     * Only supported if search is not applied
     */
    protected $highlight = null;

    protected function __construct()
    {
        // nothing to see here ;)
    }

    /**
     * Creates a SearchRequest.
     * The SearchRequest could be seeded by an http Request to a controller
     *
     * Request recognized parameters:
     * `s`: the search term
     * `page`: the search result page to show (default 1)
     * `visibility`: if the search is for private or public documents (default \KlinkVisibilityType::KLINK_PRIVATE)
     * `fs`: active facets, comma separated
     *
     * @param \Request $request optional {@see Illuminate\Http\Request} to copy data from
     * @return SearchRequest
     */
    public static function create(Request $request = null)
    {
        $instance = new static();
        
        if (! is_null($request)) {
            $instance->request_url($request->url());
            $instance->request_query($request->query());
            
            $instance->search($request->input('s', '*'));
            
            $instance->page(intval($request->input('page', 1), 10));

            $instance->highlight(intval($request->input('highlight', null), 10));
            
            $instance->visibility($request->input('visibility', KlinkVisibilityType::KLINK_PRIVATE));
            
            $available_facet_names = KlinkFacetsBuilder::allNames();
            
            if ($request->has('fs')) {
                $instance->facets(explode(',', $request->input('fs', '')));
            }
            
            
            $filters = [];
        
            foreach ($available_facet_names as $f) {
                if ($request->has($f)) {
                    $filters[$f] = explode(',', $request->input($f, ''));
                }
            }

            $instance->setIsSearchRequest($request->has('fs') || $request->input('s') || ! empty($filters));
            
            if (! empty($filters)) {
                $instance->filters($filters);
            }
        }
    
        return $instance;
    }
    
    /**
     * Add a keyword/phrase to search for.
     * If invoked 2 or more times it will override the previous inserted search terms.
     *
     * @param string $term The term/keyword or phrase to search for.Fails silently if a non-string, empty or null string is used as argument
     * @return SearchRequest
     */
    public function search($term)
    {
        if (! is_null($term) && ! empty($term) && is_string($term)) {
            $this->term = $term;
            
            $this->setIsSearchRequest($term !== '*');
        }
        
        
        return $this;
    }
    
    /**
     * Set if the request is a full search request.
     * This is used for example when a search overrides the default search term.
     *
     * @param boolean $value
     */
    public function setIsSearchRequest($value)
    {
        $this->is_search_request = $value;
        return $this;
    }
    
    public function setForceFacetsRequest()
    {
        $this->is_facets_forced = true;
        return $this;
    }
    
    /**
     * Tell if the search requests wants all documents
     *
     * @return boolean
     */
    public function isAllRequested()
    {
        return $this->term === '*';
    }
    
    /**
     * Tell if the search wants a page of results without filters or facets and isAllRequested
     *
     * @return boolean
     */
    public function isPageRequested()
    {
        return $this->isAllRequested() && is_null($this->filters) && is_null($this->facets);
    }
    
    /**
     * Tell if the search has been categorized as full search request. In other words if can only be executed by invoking the K-Link Core.
     *
     * @return boolean
     */
    public function isSearchRequested()
    {
        return $this->is_search_request;
    }
    
    /**
     * Set the result page to return
     *
     * @throws \InvalidArgumentException
     *
     * @return SearchRequest
     */
    public function page($number)
    {
        if (! is_integer($number) || is_integer($number) && $number <= 0) {
            throw new \InvalidArgumentException(sprintf('page expects a positive non-zero number, given "%s".', $number));
        }
        
        $this->page = $number;
        return $this;
    }

    /**
     * Tell to highlight the element with a specified ID.
     *
     * This means showing the page that contains the element.
     *
     * This option can only be used if no search on KCore is performed.
     * During a search this parameter is ignored
     *
     * @return SearchRequest
     */
    public function highlight($id)
    {
        $this->highlight = $id;
        return $this;
    }
    
    /**
     * Set the number of results per page
     *
     * @throws \InvalidArgumentException
     *
     * @return SearchRequest
     */
    public function limit($limit)
    {
        if (! is_integer($limit) || is_integer($limit) && $limit < 0) {
            throw new \InvalidArgumentException(sprintf('limit expects a positive number, given "%s".', $limit));
        }
        
        $this->limit = $limit;
        return $this;
    }
    
    /**
     * Set the scope of the search to private or public documents
     *
     * @param string $visibiliy The visibility for the search to be invoked on. {@see \KlinkVisibilityType}
     * @return SearchRequest
     */
    public function visibility($visibility)
    {
        $this->visibility = KlinkVisibilityType::fromString($visibility);
        return $this;
    }
    
    /**
     * Set the search filters.
     * Subsequent calls will merge the filters new values with already added filters.
     *
     * ```
     * ['language' => ['en','ru'],
     *  'documentGroups' => ['0:10','1:11'],
     *  'documentType' => ['document']]
     * ```
     *
     * @param $fs array the key/value array of the filters to be added. Multiple values should be specified with arrays
     * @return SearchRequest
     */
    public function filters($fs)
    {
        $this->filters = is_null($this->filters) ? $fs : array_merge($this->filters, $fs);

        $this->explicit_filters = array_keys($this->filters);

        return $this;
    }
    
    /**
     * Activates specific facets.
     *
     * ['language','institutionId','documentGroups']
     *
     * @param $fs array Facet names to activate
     * @return SearchRequest
     */
    public function facets($fs)
    {
        $this->facets = is_null($this->facets) ? $fs : array_merge($this->facets, $fs);
        return $this;
    }
    
    /**
     * On collection: array of groups/collections.
     *
     * this will have impact on applied filters: the usage of `on` has always higher priority than the filter on collections specified using the `filters` method
     *
     * @return SearchRequest
     */
    public function on($collections)
    {
        if (is_array($collections)) {
            $collections = Collection::make($collections);
        }
        
        $this->on_collections = is_null($this->on_collections) ? $collections : $this->on_collections->merge($collections);
        
        return $this;
    }
    
    /**
     * In list of local documents
     *
     * this will have impact on facets and filters
     *
     * @return SearchRequest
     */
    public function in($document_set)
    {
        if (is_array($document_set)) {
            $document_set = Collection::make($document_set);
        }
        
        $this->in_documents = is_null($this->in_documents) ? $document_set : $this->in_documents->merge($document_set);
        
        return $this;
    }

    /**
     * In Project: array of project id
     *
     * Filter the document based on their categorization in a project
     * this will have impact on facets and filters
     *
     * @param array $project_set the project ids to limit the search on
     * @return SearchRequest
     */
    public function inProject($project_set)
    {
        if (is_array($project_set)) {
            $project_set = Collection::make($project_set);
        }
        
        $this->in_projects = is_null($this->in_projects) ? $project_set : $this->in_projects->merge($project_set);
        
        return $this;
    }
    
    /**
     * @return SearchRequest
     */
    public function request_url($url)
    {
        $this->url = $url;
        return $this;
    }
    
    /**
     * @return SearchRequest
     */
    public function request_query($query)
    {
        $this->query = $query;
        return $this;
    }
    
    /**
     * Convert the facets and filters for this request to a KlinkFacetsBuilder that can be used when invoking the search on the K-Link Core
     */
    private function _toFacetsBuilder()
    {
        $facets = [];

        $default_facets_names = [KlinkFacet::DOCUMENT_TYPE, KlinkFacet::LANGUAGE];

        $on_collection_used = false;
        $in_project_used = false;
        
        if ($this->visibility=='public') {
            $default_facets_names[] = KlinkFacet::INSTITUTION_ID;
        }
        
        if ($this->visibility=='private') {
            $default_facets_names[] = KlinkFacet::DOCUMENT_GROUPS;
            $default_facets_names[] = KlinkFacet::PROJECT_ID;
        }
        
        if (! is_null($this->facets)) {
            $default_facets_names = array_unique(array_merge($default_facets_names, $this->facets));
        }
        
        if (! is_null($this->on_collections) && $this->on_collections->count() > 0) {
            $on_collection_used = empty($this->filters[KlinkFacet::DOCUMENT_GROUPS]);
            $this->filters[KlinkFacet::DOCUMENT_GROUPS] = $on_collection_used ? $this->on_collections->all() : $this->filters[KlinkFacet::DOCUMENT_GROUPS];
        }

        if (! is_null($this->in_projects) && $this->in_projects->count() > 0) {
            $in_project_used = empty($this->filters[KlinkFacet::PROJECT_ID]);
            $this->filters[KlinkFacet::PROJECT_ID] = $in_project_used ? $this->in_projects->all() : $this->filters[KlinkFacet::PROJECT_ID];
        }

        $fs_builder = KlinkFacetsBuilder::create();
        
        // $current_filters = null;
    
        if (! empty($this->filters)) {
            $fs_names = KlinkFacetsBuilder::allNames(); //check what we have in the parameters

            // also parameter validation

            $current_names = [];

            foreach ($fs_names as $fs) {
                if (array_key_exists($fs, $this->filters)) {
                    $current_names[] = $fs;

                    $filter_value = implode(',', $this->filters[$fs]);
                    
                    try {
                        if (! empty($filter_value)) {
                            $fs_builder->{$fs}($filter_value, 999999999, 0);
                        }
                    } catch (BadMethodCallException $bmcex) {
                        throw new BadMethodCallException(sprintf('Bad Filter invocation for "%s": %s', $fs, var_export($filter_value, true)));
                    }
                }
            }

            $default_facets_names = array_diff($default_facets_names, $current_names);
        }
    
        //clean only filters from $default_facets_names \KlinkFacet::$ONLY_FILTER
        $default_facets_names = array_diff($default_facets_names, \KlinkFacet::$ONLY_FILTER);
    
        // what default facets are missing? we need to add it
        foreach ($default_facets_names as $fs) {
            $fs_builder->{$fs}(0);
        }
        
        // if there are a specific document set
        if (! is_null($this->in_documents) && $this->in_documents->count() > 0) {
            $fs_builder->localDocumentId($this->in_documents->all());
        }

        return $fs_builder;
    }
    
    
    // http://i.giphy.com/ujUdrdpX7Ok5W.gif
    
    /**
     * Let protected fields be accessible using simple attribute getters
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        } elseif ($name === 'facets_and_filters') {
            return $this->_toFacetsBuilder();
        }
        
        throw new BadMethodCallException(sprintf('No attribute can be get using "%s".', $name), 1);
    }
    
    
    public function __toString()
    {
        return json_encode([
            'term' => $this->term,
            'visibility' => $this->visibility,
            'is_search_request' => $this->is_search_request,
            'page' => $this->page,
            'limit' => $this->limit,
            'filters' => $this->filters,
            'facets' => $this->facets,
            'on_collections' => $this->on_collections,
            'in_documents' => $this->in_documents,
            'url' => $this->url,
            'query' => $this->query,
        ]);
    }
}
