<?php

namespace Klink\DmsAdapter;

/**
* Describe a single facet
*/
final class KlinkFacetItem
{
	
	/**
	 * The term
	 * @var string
	 */
	public $term = null;


	/**
	 * The number of documents with @see $term
	 * @var int
	 */
	public $count = null;


	/**
	 * @internal
	 */
	function __construct()
	{
		
	}


	public function getTerm()
	{
		return $this->term;
	}

	/**
	 * The number of documents that has the @see $term
	 * @return [type] [description]
	 */
	public function getOccurrenceCount()
	{
		return $this->count;
	}


	public function getCount()
	{
		return $this->count;
	}
}