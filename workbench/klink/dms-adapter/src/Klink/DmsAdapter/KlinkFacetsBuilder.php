<?php

namespace Klink\DmsAdapter;

use ReflectionClass;
use BadMethodCallException;
use InvalidArgumentException;
use Klink\DmsAdapter\KlinkFacet;
use Klink\DmsAdapter\KlinkDocumentUtils;

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

	const DEFAULT_MINCOUNT = 1;

	const DEFAULT_COUNT = 10;

	/**
	 * Define facets and filters only available for K-Core version 2.2 or above
	 *
     * @var array
	 */
	private static $ONLY_CORE_TWO_TWO = array(KlinkFacet::DOCUMENT_HASH, KlinkFacet::PROJECT_ID);

	/**
	 * Cache of known facets
	 */
	private $known_constants = null;

	/**
	 * Array of names of the facets already builded for check in case of the same facet is required to be builded two or more times
	 */
	private $already_builded = null; // array of constant names already used to test if they can initialize a facet twice

	/**
	 * The facets the where builded
	 * 
	 * @var KlinkFacet[]
	 */
	private $facets = array(); // the array of facets parameters

	
	function __construct()
	{
		$oClass = new ReflectionClass(KlinkFacet::class);

        $this->known_constants = $oClass->getConstants();

        $this->already_builded = array();

        $this->facets = array();
	}


	

	// A method for each facet type ---------------------------------------------------------------
	
	/**
	 * Facet for the document type
	 * - TO BE DOCUMENTED -
	 * 
	 * 
	 * @throws BadMethodCallException if called two or more times on the same builder
	 */
	public function documentType()
	{

		$isStatic = !(isset($this) && get_class($this) == __CLASS__); //This check is caused by the non-sense of PHP 5.6 that call the same method not considering the static modifier

		if(!$isStatic){
			$instance = $this;
		}
		else {
			$instance = new KlinkFacetsBuilder;
		}

		if(in_array(KlinkFacet::DOCUMENT_TYPE, $instance->already_builded)){
			throw new BadMethodCallException("The document type facet has been already added", 1);
		}

		$builded_params = call_user_func_array(array($instance, '_handle_facet_parameters'), func_get_args());

		$facet = null;

		if(is_null($builded_params)){
			$facet = KlinkFacet::create(KlinkFacet::DOCUMENT_TYPE, 1);
		}
		else {

			if(!is_null($builded_params['filter'])){

				$exploded = explode(',', $builded_params['filter']);

				$valids = KlinkDocumentUtils::getDocumentTypes();

				foreach ($exploded as $single_filter) {
					if(!in_array($single_filter, $valids)){
						throw new InvalidArgumentException("Invalid document type ". $single_filter ."for filter", 2);
					}
				}


			}

			$facet = KlinkFacet::create(KlinkFacet::DOCUMENT_TYPE, 
						$builded_params['mincount'], 
						$builded_params['prefix'], 
						$builded_params['count'], 
						$builded_params['filter']);
		}

		$instance->facets[] = $facet;
		$instance->already_builded[] = KlinkFacet::DOCUMENT_TYPE;

		return $instance;
	}

	/**
	 * Facets for the document groups
	 * @return KlinkFacetsBuilder
	 * @throws BadMethodCallException if called two or more times on the same builder
	 */
	public function documentGroups()
	{

		$isStatic = !(isset($this) && get_class($this) == __CLASS__); //This check is caused by the non-sense of PHP 5.6 that call the same method not considering the static modifier

		if(!$isStatic){
			$instance = $this;
		}
		else {
			$instance = new KlinkFacetsBuilder;
		}

		if(in_array(KlinkFacet::DOCUMENT_GROUPS, $instance->already_builded)){
			throw new BadMethodCallException("The document group facet has been already added", 1);
		}

		$builded_params = call_user_func_array(array($instance, '_handle_facet_parameters'), func_get_args());

		$facet = null;

		if(is_null($builded_params)){
			$facet = KlinkFacet::create(KlinkFacet::DOCUMENT_GROUPS, 1);
		}
		else {

			$facet = KlinkFacet::create(KlinkFacet::DOCUMENT_GROUPS, 
						$builded_params['mincount'], 
						$builded_params['prefix'], 
						$builded_params['count'], 
						$builded_params['filter']);
		}

		$instance->facets[] = $facet;
		$instance->already_builded[] = KlinkFacet::DOCUMENT_GROUPS;

		return $instance;
	}

	/**
	 * Filter for the localDocumentId.
	 *
	 * This is not a facet and so you cannot use mincount, prefix, count.
	 *
	 * If no value is passed for $filter parameter the filter will not be applied on the request to the Core.
	 *
	 * @param string|array $filter the value to use for filtering. String or array, default null.
	 * @return KlinkFacetsBuilder
	 * @throws BadMethodCallException if called two or more times on the same builder
	 */
	public function localDocumentId($filter)
	{

		$isStatic = !(isset($this) && get_class($this) == __CLASS__); //This check is caused by the non-sense of PHP 5.6 that call the same method not considering the static modifier

		if(!$isStatic){
			$instance = $this;
		}
		else {
			$instance = new KlinkFacetsBuilder;
		}

		if(in_array(KlinkFacet::LOCAL_DOCUMENT_ID, $instance->already_builded)){
			throw new BadMethodCallException("The local document id filter has been already added", 1);
		}

		// $builded_params = call_user_func_array(array($instance, '_handle_facet_parameters'), func_get_args());

		$facet = null;

		if(empty($filter)){
			throw new InvalidArgumentException("The filter value cannot be empty", 2);
		}
		else {

			$value = is_array($filter) ? implode(',', $filter) : $filter;

			$facet = KlinkFacet::create(KlinkFacet::LOCAL_DOCUMENT_ID, 
						self::DEFAULT_MINCOUNT, 
						null, 
						self::DEFAULT_COUNT, 
						$value);
		}

		$instance->facets[] = $facet;
		$instance->already_builded[] = KlinkFacet::LOCAL_DOCUMENT_ID;

		return $instance;
	}

	/**
	 * Filter for the documentId.
	 *
	 * This is not a facet and so you cannot use mincount, prefix, count.
	 *
	 * If no value is passed for $filter parameter the filter will not be applied on the request to the Core.
	 *
	 * @param string|array $filter the value to use for filtering. String or array, default null.
	 * @return KlinkFacetsBuilder
	 * @throws BadMethodCallException if called two or more times on the same builder
	 */
	public function documentId($filter)
	{

		$isStatic = !(isset($this) && get_class($this) == __CLASS__); //This check is caused by the non-sense of PHP 5.6 that call the same method not considering the static modifier

		if(!$isStatic){
			$instance = $this;
		}
		else {
			$instance = new KlinkFacetsBuilder;
		}

		if(in_array(KlinkFacet::DOCUMENT_ID, $instance->already_builded)){
			throw new BadMethodCallException("The document id filter has been already added", 1);
		}

		// $builded_params = call_user_func_array(array($instance, '_handle_facet_parameters'), func_get_args());

		$facet = null;

		if(empty($filter)){
			throw new InvalidArgumentException("The filter value cannot be empty", 2);
		}
		else {

			$value = is_array($filter) ? implode(',', $filter) : $filter;

			$facet = KlinkFacet::create(KlinkFacet::DOCUMENT_ID, 
						self::DEFAULT_MINCOUNT, 
						null, 
						self::DEFAULT_COUNT, 
						$value);
		}

		$instance->facets[] = $facet;
		$instance->already_builded[] = KlinkFacet::DOCUMENT_ID;

		return $instance;
	}

	/**
	 * Facet for the document language
	 * - TO BE DOCUMENTED -
	 * 
	 * @throws BadMethodCallException if called two or more times on the same builder
	 */
	public function language()
	{

		$isStatic = !(isset($this) && get_class($this) == __CLASS__); //This check is caused by the non-sense of PHP 5.6 that call the same method not considering the static modifier

		if(!$isStatic){
			$instance = $this;
		}
		else {
			$instance = new static;
		}

		if(in_array(KlinkFacet::LANGUAGE, $instance->already_builded)){
			throw new BadMethodCallException("The language facet has been already added", 1);
		}

		$builded_params = call_user_func_array(array($instance, '_handle_facet_parameters'), func_get_args());

		$facet = null;

		if(is_null($builded_params)){
			$facet = KlinkFacet::create(KlinkFacet::LANGUAGE, 1);
		}
		else {

			$facet = KlinkFacet::create(KlinkFacet::LANGUAGE, 
						$builded_params['mincount'], 
						$builded_params['prefix'], 
						$builded_params['count'], 
						$builded_params['filter']);
		}

		$instance->facets[] = $facet;
		$instance->already_builded[] = KlinkFacet::LANGUAGE;

		return $instance;
	}

	/**
	 * Facet for the institution id
	 * - TO BE DOCUMENTED -
	 * 
	 * variable number of parameters
	 * 
	 * if NONE   => enable the facet will 
	 * if one string => the filter (check if is a valid institution id)
	 * if one int => number of items to return for the facet (count)
	 * if two ints => 1: count, 2: mincount
	 * if 3 => 1: filter, 2: count, 3: mincount
	 * 
	 * @throws BadMethodCallException if called two or more times on the same builder
	 */
	public function institution()
	{
		$isStatic = !(isset($this) && get_class($this) == __CLASS__); //This check is caused by the non-sense of PHP 5.6 that call the same method not considering the static modifier

		if(!$isStatic){
			$instance = $this;
		}
		else {
			$instance = new KlinkFacetsBuilder;
		}

		if(in_array(KlinkFacet::INSTITUTION_ID, $instance->already_builded)){
			throw new BadMethodCallException("The institution facet has been already added", 1);
		}

		$builded_params = call_user_func_array(array($instance, '_handle_facet_parameters'), func_get_args());

		$facet = null;

		if(is_null($builded_params)){
			$facet = KlinkFacet::create(KlinkFacet::INSTITUTION_ID, 1);
		}
		else {

			if(!is_null($builded_params['filter'])){
				$exploded = explode(',', $builded_params['filter']);
			}

			$facet = KlinkFacet::create(KlinkFacet::INSTITUTION_ID, 
						$builded_params['mincount'], 
						$builded_params['prefix'], 
						$builded_params['count'], 
						$builded_params['filter']);
		}

		$instance->facets[] = $facet;
		$instance->already_builded[] = KlinkFacet::INSTITUTION_ID;

		return $instance;
	}

	/**
	 * @see institution()
	 */
	public function institutionId(){

		$isStatic = !(isset($this) && get_class($this) == __CLASS__); //This check is caused by the non-sense of PHP 5.6 that call the same method not considering the static modifier

		if(!$isStatic){
			$instance = $this;
		}
		else {
			$instance = new KlinkFacetsBuilder;
		}

		return call_user_func_array(array($instance, 'institution'), func_get_args());
	}

    /**
     * Filter for the documentHash.
     *
     * This is not a facet and so you cannot use mincount, prefix, count.
     *
     * If no value is passed for $filter parameter the filter will not be applied on the request to the Core.
     *
     * @param string $filter the value to use for filtering
     * @return KlinkFacetsBuilder
     * @throws BadMethodCallException if called two or more times on the same builder
     */
    public function documentHash($filter){
        $isStatic = !(isset($this) && get_class($this) == __CLASS__); //This check is caused by the non-sense of PHP 5.6 that call the same method not considering the static modifier

        if(!$isStatic){
            $instance = $this;
        }
        else {
            $instance = new KlinkFacetsBuilder;
        }

        if(in_array(KlinkFacet::DOCUMENT_HASH, $instance->already_builded)){
            throw new BadMethodCallException("The documentHash filter has been already added", 1);
        }

        if(empty($filter)){
            throw new InvalidArgumentException("The filter value cannot be empty", 2);
        }

        if (is_array($filter)) {
            throw new InvalidArgumentException("The documentHash value cannot be an array", 2);
        }

        $facet = KlinkFacet::create(KlinkFacet::DOCUMENT_HASH,
            self::DEFAULT_MINCOUNT,
            null,
            self::DEFAULT_COUNT,
            $filter);

        $instance->facets[] = $facet;
        $instance->already_builded[] = KlinkFacet::DOCUMENT_HASH;

        return $instance;
    }

	
	/**
	 * Facet for the locations city/country names
	 * 
	 * variable number of parameters
	 * 
	 * if NONE   => enable the facet will 
	 * if one string => the filter (check if is a valid institution id)
	 * if one int => number of items to return for the facet (count)
	 * if two ints => 1: count, 2: mincount
	 * if 3 => 1: filter, 2: count, 3: mincount
	 * 
	 * @throws BadMethodCallException if called two or more times on the same builder
	 */
	public function locations()
	{
		$isStatic = !(isset($this) && get_class($this) == __CLASS__); //This check is caused by the non-sense of PHP 5.6 that call the same method not considering the static modifier

		if(!$isStatic){
			$instance = $this;
		}
		else {
			$instance = new KlinkFacetsBuilder;
		}

		if(in_array(KlinkFacet::LOCATIONS_STRING, $instance->already_builded)){
			throw new BadMethodCallException("The locations facet has been already added", 1);
		}

		$builded_params = call_user_func_array(array($instance, '_handle_facet_parameters'), func_get_args());

		$facet = null;

		if(is_null($builded_params)){
			$facet = KlinkFacet::create(KlinkFacet::LOCATIONS_STRING, 1);
		}
		else {

			$facet = KlinkFacet::create(KlinkFacet::LOCATIONS_STRING, 
						$builded_params['mincount'], 
						$builded_params['prefix'], 
						$builded_params['count'], 
						$builded_params['filter']);
		}

		$instance->facets[] = $facet;
		$instance->already_builded[] = KlinkFacet::LOCATIONS_STRING;

		return $instance;
	}
	/**
	 * Facet for the projectId names
	 * 
	 * variable number of parameters
	 * 
	 * if NONE   => enable the facet will 
	 * if one string => the filter (check if is a valid project_id )
	 * if one int => number of items to return for the facet (count)
	 * if two ints => 1: count, 2: mincount
	 * if 3 => 1: filter, 2: count, 3: mincount
	 * 
	 * @throws BadMethodCallException if called two or more times on the same builder
	 */
	public function projectId()
	{
		$isStatic = !(isset($this) && get_class($this) == __CLASS__); //This check is caused by the non-sense of PHP 5.6 that call the same method not considering the static modifier

		if(!$isStatic){
			$instance = $this;
		}
		else {
			$instance = new KlinkFacetsBuilder;
		}

		if(in_array(KlinkFacet::PROJECT_ID, $instance->already_builded)){
			throw new BadMethodCallException("The project_id facet has been already added", 1);
		}

		$builded_params = call_user_func_array(array($instance, '_handle_facet_parameters'), func_get_args());

		$facet = null;

		if(is_null($builded_params)){
			$facet = KlinkFacet::create(KlinkFacet::PROJECT_ID, 1);
		}
		else {

			$facet = KlinkFacet::create(KlinkFacet::PROJECT_ID,
						$builded_params['mincount'], 
						$builded_params['prefix'], 
						$builded_params['count'], 
						$builded_params['filter']);
		}

		$instance->facets[] = $facet;
		$instance->already_builded[] = KlinkFacet::PROJECT_ID;

		return $instance;
	}
	
	/**
	 * Facet for the locations string
	 * @throws BadMethodCallException if called two or more times on the same builder
	 */
	public function locationsString()
	{
		$isStatic = !(isset($this) && get_class($this) == __CLASS__); //This check is caused by the non-sense of PHP 5.6 that call the same method not considering the static modifier

		if(!$isStatic){
			$instance = $this;
		}
		else {
			$instance = new KlinkFacetsBuilder;
		}

		return call_user_func_array(array($instance, 'locations'), func_get_args());
	}


	// Final Build --------------------------------------------------------------------------------


	/**
	 * The final method. Builds the facets parameters to pass to search or specific functions that requires an array of KlinkFacet
	 * 
	 * @return KlinkFacet[] the array of facets
	 */
	public function build()
	{
		return array_filter($this->facets);
	}



	// Helpers ------------------------------------------------------------------------------------

	/**
	 * if NONE   => return null
	 * if one string => the filter (check if is a valid institution id)
	 * if one int => number of items to return for the facet (count)
	 * if two ints => 1: count, 2: mincount
	 * if 3 => 1: filter, 2: count, 3: mincount
	 */
	private function _handle_facet_parameters()
	{

		$default = array('filter' => null, 'mincount' => self::DEFAULT_MINCOUNT, 'count' => self::DEFAULT_COUNT, 'prefix' => null);

	    if (func_num_args() == 0) {

	    	return null;

	    }

	    $num_args = func_num_args();

	    if(func_num_args() == 1 && empty($num_args)){
	    	return null;
	    }

	    if (func_num_args() == 1 && is_string(func_get_arg(0))) {

	    	return array_merge( $default, array('filter' => func_get_arg(0)) );

	    }
		else if (func_num_args() == 1 && is_array(func_get_arg(0))) {

	    	return array_merge( $default, array('filter' => implode(',', func_get_arg(0)) ));

	    }
	    else if (func_num_args() == 1 && is_integer(func_get_arg(0))) {

	    	return array_merge( $default, array('mincount' => func_get_arg(0)) );
	    	
	    }
	    else if(func_num_args() == 2 && func_get_args() === array_filter(func_get_args(), 'is_int')){
	    	// only ints

	    	return array_merge( $default, array('count' => func_get_arg(0), 'mincount' => func_get_arg(1)) );
	    }
	    else if(func_num_args() == 3){

	    	$args = func_get_args();
	    	$splice = array_splice($args, 1);

	    	if(is_string(func_get_arg(0)) && $splice === array_filter($splice, 'is_int')) {
		    	return array_merge( $default, array('filter' => func_get_arg(0), 'count' => func_get_arg(1), 'mincount' => func_get_arg(2)) );	
		    }
            else if(is_array(func_get_arg(0)) && $splice === array_filter($splice, 'is_int')) {
		    	return array_merge( $default, array('filter' => implode(',', func_get_arg(0)), 'count' => func_get_arg(1), 'mincount' => func_get_arg(2)) );	
		    }

	    }

	    throw new BadMethodCallException("Bad parameters " . var_export(func_get_args(), true), -42);
	}


	protected function _all($apiVersion = '3.0')
	{

		$instance = $this;

		$known = $this->_allNames($apiVersion);

		foreach ($known as $facetName) {

			$instance = call_user_func_array(array($this, $facetName), array());

		}

		return $this->build();
	}


	protected function _allNames($apiVersion = '3.0')
	{
		$names = array_values(array_filter( array_values( $this->known_constants ), array($this, '_filterConstantValues')));

		if( $apiVersion !== '3.0' ){

			// remove the facet names that cause error if used on API version < 2.2

			$names = array_filter($names, function($el){
				return !in_array($el, self::$ONLY_CORE_TWO_TWO);
			});			
			
		}

		return $names;
	}


	protected function _filterConstantValues($el){
		return !in_array($el, KlinkFacet::$ONLY_FILTER);
	}


	// Static facilities for start building -------------------------------------------------------

	/**
	 * Enable the first static call for each facet method available on an instance of KlinkFacetsBuilder
	 * 
	 * example
	 * 
	 * KlinkFacetsBuilder::documentType() will create an instance of KlinkFacetsBuilder and call documentType()
	 * 
	 * 
	 * @throws BadMethodCallException if the builder don't have a facet specific instance method
	 */
	public static function __callStatic($method, $arguments)
	{

		$s = new KlinkFacetsBuilder();

		if(method_exists($s, $method)){

			return call_user_func_array(array($s, $method), $arguments);

		}

		throw new BadMethodCallException("Call to undefined method KlinkFacetsBuilder::{$method}()");

	}


	/**
	 * Return all the Klink supported facets with the default configuration
	 * 
	 * @param string $apiVersion The API version you want facets for. Default @see \KlinkCoreClient::DEFAULT_KCORE_API_VERSION  
	 * @return KlinkFacet[] array of the available KlinkFacet
	 */
	public static function all($apiVersion = '3.0'){

		$s = new KlinkFacetsBuilder();

		return $s->_all($apiVersion);

	}

	/**
	 * Create a new instance of KlinkFacetsBuilder from a static context.
	 * 
	 * This is here only to cope with PHP 5.6 that consider a method without static modifier equal to a method with static modifier
	 * 
	 * @return KlinkFacetsBuilder
	 */
	public static function instance(){

		return new KlinkFacetsBuilder();

	}

	/**
	 * Create a new instance of KlinkFacetsBuilder from a static context.
	 * 
	 * This is here only to cope with PHP 5.6 that consider a method without static modifier equal to a method with static modifier
	 * 
	 * @return KlinkFacetsBuilder
	 */
	public static function create(){

		return new KlinkFacetsBuilder();

	}

	/**
	 * Create a new instance of KlinkFacetsBuilder from a static context.
	 * 
	 * This is here only to cope with PHP 5.6 that consider a method without static modifier equal to a method with static modifier
	 * 
	 * @return KlinkFacetsBuilder
	 */
	public static function i(){

		return new KlinkFacetsBuilder();

	}

	/**
	 * Return the names of the currently supported facets
	 * 
	 * @param string $apiVersion The API version you want facets for. Default @see \KlinkCoreClient::DEFAULT_KCORE_API_VERSION  
	 * @return array array of strings
	 */
	public static function allNames($apiVersion = '3.0'){

		$s = new KlinkFacetsBuilder();

		return $s->_allNames($apiVersion);

	}
}
