<?php

namespace Klink\DmsAdapter;

use KSearchClient\Model\Data\SearchParams;
use Klink\DmsSearch\SearchRequest;

/**
 * Convert a \Klink\DmsSearch\SearchRequest to a \KSearchClient\Model\Data\SearchParams
 */
class KlinkSearchRequest
{
	/**
	 * @var \Klink\DmsSearch\SearchRequest
	 */
	private $request = null;

	public function __construct(SearchRequest $request)
	{
		$this->request = $request;
	}

	public function visibility()
	{
		return $this->request->visibility;
	}

	/**
	 * @param array $filters
	 * @return string
	 */
	private function convertToSolrFilterQuery($filters)
	{
		if(empty($filters)){
			return '';
		}


		$filters_collection = collect($filters);

		$compacted_filters = $filters_collection->map(function ($filter_values, $filter_name) {

			if(is_string($filter_values)){
				return "{$filter_name}:{$filter_values}";
			}

			$mapped = array_filter(array_map(function($val) use($filter_name){
				return "{$filter_name}:{$val}";
			}, $filter_values));

			return implode(' OR ', $mapped);
		});

		$filter_string = implode(' AND ', $compacted_filters->values()->toArray());

		return $filter_string;
	}


	public function toSearchParams()
	{
        $params = new SearchParams();
		$params->search = $this->request->term;
		$params->offset = $this->request->limit * ($this->request->page - 1);
		$params->limit = $this->request->limit;
		$params->filters = $this->convertToSolrFilterQuery($this->request->buildFilters());
		$params->aggregations = $this->request->buildAggregations();
		return $params;
	}


	/**
	 * Convert a SearchRequest into a KlinkSearchRequest
	 */
	public static function from(SearchRequest $request)
	{
		return new self($request);
	}

	/**
	 * Generate a KlinkSearchRequest from the underlying SearchRequest components
	 */
	public static function build($terms, $visibility, $page, $itemsPerPage, $aggregations = [], $filters = [])
	{
		return static::from(
			(new SearchRequest())
				->search($terms)
				->visibility($visibility)
				->page($page)
				->limit($itemsPerPage)
				->facets($aggregations)
				->filters($filters)
		);
	}
}