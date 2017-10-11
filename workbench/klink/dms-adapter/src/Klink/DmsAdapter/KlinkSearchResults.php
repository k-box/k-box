<?php

namespace Klink\DmsAdapter;

use KSearchClient\Model\Data\SearchResults;
use \Klink\DmsAdapter\KlinkSearchResultItem;

/**
 * Wrapper around SearchResults
 */
final class KlinkSearchResults
{

	/**
	 * @var \KSearchClient\Model\Data\SearchResults
	 */
	private $searchResults = null;


	/**
	 * The original query given at API invocation
	 * @var string
	 */
	private $query;

	/**
	 * visibility
	 * @var KlinkVisibilityType
	 * @preferset
	 */
	private $visibility;

	/**
	 * numFound
	 * @var int
	 */
	private $total;

	/**
	 * The current list of Results
	 * @var KlinkSearchResultItem[]
	 */
	private $items;

	/**
	 * queryTime
	 * @var float
	 */
	private $queryTime;

	/**
	 * The number of results to retrieve per each page
	 * @var int
	 */
	private $resultsPerPage;

	/**
	 * 
	 * @var int
	 */
	private $offset;


	/**
	 * Store the activated filters for this search
	 * @internal
	 */
	private $filters = [];
	
	/**
	 * Store the available facets starting from this search
	 * @internal
	 * @var KlinkFacet[]
	 */
	private $facets = [];
	
	/**
	 * getQuery
	 * @return string
	 */
	public function getTerms() {
		return $this->query;
	}

	/**
	 * The performed search visibility (public or private)
	 * @return KlinkVisibilityType
	 */
	public function getVisibility() {
		return $this->visibility;
	}

	/**
	 * The grand total of results matched by the query
	 * @return int
	 */
	public function getTotalResults() {
		return $this->total;
	}

	/**
	 * Search execution time (in milliseconds)
	 * @return int
	 */
	public function getSearchTime() {
		return $this->queryTime;
	}

	/**
	 * The number of items per page
	 * 
	 * @return int
	 */
	public function getResultsPerPage() {
		return $this->resultsPerPage;
	}

	/**
	 * specify the first result to return from the complete set of retrieved set, the value is 0-based; the default value is 0
	 * @return int
	 */
	public function getOffset() {
		return $this->offset;
	}

	/**
	 * The number of results returned by this invocation
	 * @return int
	 */
	public function getCurrentResultCount() {
		return count($this->items);
	}

	/**
	 * The current list of Results
	 * @return \Illuminate\Support\Collection of KlinkSearchResultItem
	 */
	public function getResults() {
		return collect($this->items);
	}

	/**
	 * Return the facets for this search
	 * @return KlinkFacet[]|false the array of facets if any, false if no facets where requested on the search
	 */
	public function getFacets()
	{
		return empty($this->facets) ? [] : $this->facets;
	}

	public function getFilters()
	{
		return $this->filters ?? [];
	}


	/**
	 * @internal no one can create an instance of this class and remain alive
	 */
	public function __construct($visibility, $query, $queryTime, $items, $resultsPerPage = 10, $offset = 0, $totalResults = 20, $facets = [], $filters = null)
	{
		$this->resultsPerPage = 10;
		$this->offset = 0;
		$this->visibility = $visibility;
		$this->query = $query;
		$this->queryTime = $queryTime;
		$this->total = $totalResults;
		$this->items = array_map(function($item){
			return new KlinkSearchResultItem($item);
		}, $items);
		$this->facets = $facets;
		$this->filters = $filters;
	}

	/**
	 * Parse the filter string into a structure that can be used to check if a filter was used and for what values
	 */
	public static function parseFilterString($filters)
	{
		return $filters;
	}

	/**
	 * Create a KlinkSearchResults instance from the SearchResults data received from the K-Search
	 */
	public static function make(SearchResults $results, $visibility)
	{
		return new self($visibility, 
			$results->query->search, 
			$results->query_time ?? 0, 
			$results->items, 
			$results->query->limit, 
			$results->query->offset, 
			$results->total_matches,
			$results->aggregations,
			static::parseFilterString($results->query->filters)
		);
	}

	/**
	 * Creates a Fake KlinkSearchResults instance for testing purposes
	 */
	public static function fake($attributes, $visibility)
	{

		$items = array_get($attributes, 'items', []);

		return new self($visibility, 
		array_get($attributes, 'query.search', '*'), 
		array_get($attributes, 'query_search', 3), 
		$items, 
		array_get($attributes, 'query.limit', 10), 
		array_get($attributes, 'query.offset', 0), 
		array_get($attributes, 'total_matches', count($items)),
		array_get($attributes, 'aggregations', []),
		static::parseFilterString(array_get($attributes, 'query.filters', ''))
	);
	}
}