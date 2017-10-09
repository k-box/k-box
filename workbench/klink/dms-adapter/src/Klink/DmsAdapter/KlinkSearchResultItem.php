<?php

namespace Klink\DmsAdapter;

/**
 * Describe a single search result
 * 
 * @package Klink
 * @since 0.2
 */
final class KlinkSearchResultItem
{

	/**
	 * The result item score.
	 * 
	 * @var float
	 */

	public $score;

	/**
	 * The score of the result
	 * @return float
	 */
	public function getScore() {
		return $this->score;
	}


	/**
	 * The document descriptor that describe the result
	 * @var KlinkDocumentDescriptor
	 */
	public $document_descriptor;

	/**
	 * The document descriptor that describe the result
	 * @return KlinkDocumentDescriptor
	 */
	public function getDescriptor() {
		return $this->document_descriptor;
	}

	/**
	 * If you want access also to the internal KlinkDocumentDescriptor's public properties ;)
	 * @param  [type] $property [description]
	 * @return [type]           [description]
	 */
	public function __get($property) {
        if (property_exists($this->document_descriptor, $property)) {
            return $this->document_descriptor->$property;
        }
		// @codeCoverageIgnoreStart
		return $this->$property;
		// @codeCoverageIgnoreEnd
    }


    public function __call($method, $parameters) {


        if (method_exists($this->document_descriptor, $method))
		{
			return call_user_func_array(array($this->document_descriptor, $method), $parameters);
		}
		// @codeCoverageIgnoreStart
		return call_user_func_array(array($this, $method), $parameters);
		// @codeCoverageIgnoreEnd
    }




	/**
	 * @internal
	 */
	function __construct()
	{
		$this->score = 0;
		$this->document_descriptor = null;
	}




}