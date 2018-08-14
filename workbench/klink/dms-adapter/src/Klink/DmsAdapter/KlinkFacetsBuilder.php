<?php

namespace Klink\DmsAdapter;

use ReflectionClass;
use BadMethodCallException;
use InvalidArgumentException;
use Klink\DmsAdapter\KlinkFacets;
use Klink\DmsAdapter\KlinkFilters;
use KBox\Documents\KlinkDocumentUtils;
use KSearchClient\Model\Search\Aggregation;

/**
* Helps you constructing the facets search parameter with a fluent chainable api.
* 
* 
* To help the autocomplete a method is defined for all the supported facets.
* 
* You cannot call the same facet twice!
* 
* Remember to call @see build() at the end of the construction
* 
* PHP 5.6 non-sense warning why I would do that if I was able to put two version of the same method one for static and the other for instance
* if you have a call like this KlinkFacetsBuilder::documentType() and a warning/error like this:
* 	Non-static method KlinkFacetsBuilder::documentType() should not be called statically, assuming $this from incompatible context
* 
* you can do (new KlinkFacetsBuilder)->documentType() or KlinkFacetsBuilder::instance()->documentType 
* //this strange thing is caused by a particular way of using static inside PHP 5.6
*/
final class KlinkFacetsBuilder
{
	/**
	 * Maximum amount of aggregations to output.
	 * 
	 * The number is set to the maximum allowed by the K-Search to reduce 
	 * the chances that some expected values are not returned. This is 
	 * especially true for collections where there might be items 
	 * that are only inside one collection and the user expects 
	 * to see it
	 * 
	 * @var int
	 */
	const DEFAULT_LIMIT = 100;
	
	/**
	 * Calculate aggregations after or before applying filter.
	 * 
	 * Default true, the aggregations will be calculated after 
	 * filters are applied to the set of matching Data entries
	 * 
	 * @var bool
	 */
	const DEFAULT_COUNTS_FILTERED = true;


	/**
	 * Cache of known facets
	 */
	private $known_constants = null;

	/**
	 * Array of names of the facets already builded for check in case of the same facet is required to be builded two or more times
	 */
	private $already_builded = null; // array of constant names already used to test if they can initialize a facet twice

	/**
	 * The aggregations to activate
	 * 
	 * @var array
	 */
	private $facets = [];

	
	function __construct($activeAggregations = [])
	{
        $this->known_constants = KlinkFacets::enums();
        $this->facets = $activeAggregations ?? [];
	}

	/**
	 * Create a builder instance where the specified aggregations, via name, are activated with the default options
	 * 
	 * @param array $aggregations the Facet names to activate
	 * @return KlinkFacetsBuilder
	 */
	public static function aggregate(array $aggregations)
	{
		return new self($aggregations);
	}

	/**
	 * Build the aggregations to activate on the search
	 * 
	 * @return array the array of Aggregations to be enabled
	 */
	public function buildAggregations()
	{
		$aggregations = [];

		foreach (array_filter($this->facets) as $facet) {
			$aggregations[$facet] = tap(new Aggregation(), function($aggregation) {
				$aggregation->limit = self::DEFAULT_LIMIT;
				$aggregation->countsFiltered = self::DEFAULT_COUNTS_FILTERED;
			});
		}

		return $aggregations;
	}


	protected function _filterConstantValues($el){
		return !in_array($el, KlinkFilters::enums());
	}



	/**
	 * Return all the Klink supported facets with the default configuration
	 * 
	 * @param string $apiVersion The API version you want facets for. Default @see \KlinkCoreClient::DEFAULT_KCORE_API_VERSION  
	 * @return Aggregations[] array of the available KlinkFacet
	 */
	public static function all($apiVersion = '3.0'){

		$s = new KlinkFacetsBuilder(KlinkFacets::enums());

		return $s->buildAggregations();

	}

	/**
	 * Create a new instance of KlinkFacetsBuilder from a static context.
	 * 
	 * This is here only to cope with PHP 5.6 that consider a method without static modifier equal to a method with static modifier
	 * 
	 * @return KlinkFacetsBuilder
	 */
	public static function make(){

		return new KlinkFacetsBuilder();

	}

	/**
	 * Return the names of the currently supported facets
	 * 
	 * @param string $apiVersion The API version you want facets for. Default @see \KlinkCoreClient::DEFAULT_KCORE_API_VERSION  
	 * @return array array of strings
	 */
	public static function allNames(){
		return array_values(KlinkFacets::enums());

	}
}
