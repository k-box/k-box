<?php

namespace Klink\DmsAdapter;

/**
* Describe a single facet.
* 
* This class also contain one constant for each known facets.
* 
*/
final class KlinkFacet
{
	/**
	 * Define the facet name for the @see KlinkDocumentDescriptor::$documentType field
	 */
	const DOCUMENT_TYPE = 'documentType';

	/**
	 * Define the facet name for the @see KlinkDocumentDescriptor::$language field
	 */
	const LANGUAGE = 'language';

	/**
	 * Define the facet name for the @see KlinkDocumentDescriptor::$institutionId field
	 */
	const INSTITUTION_ID = 'institutionId';

	/**
	 * Define the facet name for the @see KlinkDocumentDescriptor::$documentGroups field
	 */
	const DOCUMENT_GROUPS = 'documentGroups';

	/**
	 * Define the facet name for the @see KlinkDocumentDescriptor::$localDocumentID field
	 */
	const LOCAL_DOCUMENT_ID = 'localDocumentId';

	/**
	 * Define the facet name for the @see KlinkDocumentDescriptor::getKlinkId() field
	 */
	const DOCUMENT_ID = 'documentId';
	
	/**
	 * Define the facet name for the @see KlinkDocumentDescriptor::$locationsString field
	 */
	const LOCATIONS_STRING = 'locationsString';

    /**
     * Define the filter name for the @see KlinkDocumentDescriptor::$hash field
     */
    const DOCUMENT_HASH = 'documentHash';

    /**
     * Define the facet name for the DocumentDescriptor
     *
     * @see KlinkDocumentDescriptor::$projectId
     */
    const PROJECT_ID = 'projectId';

    /**
     * Defines fields used as filter only
     *
     * @var array
     */
	public static $ONLY_FILTER = array(self::LOCAL_DOCUMENT_ID, self::DOCUMENT_ID, self::DOCUMENT_HASH);
	


	/**
	 * [$name description]
	 * @var string
	 */
	public $name = null;


	/**
	 * KlinkFacetItem[]
	 * @var KlinkFacetItem[]
	 */
	public $items = null;


	protected $min = 2;

	protected $count = 10;

	protected $filter = null;

	protected $prefix = null;

	/**
	 * @internal reserved for deserialization
	 */
	function __construct()
	{

	}


	/**
	 * Create a new facet instance.
	 * 
	 * For the facet name plase refer to @see KlinkFacet class constants
	 * 
	 * @param string $name   the name of the facet, see the constants defined in this class
	 * @param int $min Specify the minimun frequency for the facet-term to be return for the given, default 2
	 * @param string $prefix retrieve the facet items that have such prefix in the text 
	 * @param int $count  configure the number of terms to return for the given facet
	 * @param string $filter specify the filtering value to applied to the search for the given facet
	 * 
	 * @throws InvalidArgumentException If $name if not a valid facet name @see KlinkFacet
	 */
	public static function create($name, $min = 2, $prefix = null, $count = 10, $filter = null)
	{

		$constant_name = strtoupper( snake_case($name) );

		if (!defined("self::$name") && !defined("self::$constant_name")) {
            throw new InvalidArgumentException("Unknown facet name ($name or $constant_name)");
        }

		$ret = new self;

		$ret->name = $name;
		$ret->min = $min;
		$ret->count = $count;
		$ret->filter = !empty($filter) ? $filter : null;
		$ret->prefix = $prefix;

		return $ret;
	}

	public function getName(){
		return $this->name;
	}




	public function getMin(){
		return $this->min;
	}

	public function getCount(){
		return $this->count;
	}

	public function getFilter(){
		return $this->filter;
	}

	public function getPrefix(){
		return $this->prefix;
	}

	public function setMin($value){
		$this->min = $value;
		return $this;
	}

	public function setCount($value){
		$this->count = $value;
		return $this;
	}

	public function setFilter($value){
		$this->filter = !empty($value) ? $value : null;
		return $this;
	}

	public function setPrefix($value){
		$this->prefix = $value;
		return $this;
	}

	/**
	 * [getItems description]
	 * @return KlinkFacetItems[]
	 */
	public function getItems()
	{
		return $this->items;
	}

	/**
	 * Convert the K-Link Core understandable parameters
	 * @return array
	 * @internal
	 */
	public function toKlinkParameter()
	{

		$ser = array();

		if(!in_array($this->getName(), KlinkFacet::$ONLY_FILTER)){
			$ser = array_merge(array(
				"facets" => $this->getName(),
				'facet_'.$this->getName().'_count' => $this->getCount(),
				'facet_'.$this->getName().'_mincount' => $this->getMin(),
			), $ser);
		}

		if(!is_null($this->getFilter()) && !empty($this->getFilter())){
			$ser['filter_'.$this->getName()] = $this->getFilter();
		}

		if(!is_null($this->getPrefix()) && !in_array($this->getName(), KlinkFacet::$ONLY_FILTER) ){
			$ser['facet_'.$this->getName().'_prefix'] = $this->getPrefix();
		}

		return $ser;

	}

}
