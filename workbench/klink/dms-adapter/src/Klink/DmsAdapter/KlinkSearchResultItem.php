<?php

namespace Klink\DmsAdapter;

use BadMethodCallException;
use KSearchClient\Model\Data\Data;

/**
 * Wrapper around Data
 */
final class KlinkSearchResultItem
{

	/**
	 * Tell if the result can be added to the starred documents
	 */
	private $isStarrable = false;
	
	/**
	 * 
	 */
	private $starId = null;

	public function __construct(Data $resultItem)
	{
		$this->document_descriptor = $resultItem;
	}

	/**
	 * The document descriptor that describe the result
	 * @var \KSearchClient\Model\Data\Data
	 */
	public $document_descriptor;

	/**
	 * The document descriptor that describe the result
	 * @return \KSearchClient\Model\Data\Data
	 */
	public function getDescriptor()
	{
		return $this->document_descriptor;
	}

	/**
	 * If you want access also to the internal KlinkDocumentDescriptor's public properties ;)
	 * @param  [type] $property [description]
	 * @return [type]           [description]
	 */
	public function __get($property)
	{
        if (property_exists($this->document_descriptor, $property)) {
            return $this->document_descriptor->$property;
        }
		
		if(property_exists($this, $property)){

			return $this->$property;
		}

		throw new BadMethodCallException("Property $property don't exists.");
    }


	public function __call($method, $parameters)
	{

        if (method_exists($this->document_descriptor, $method))
		{
			return call_user_func_array(array($this->document_descriptor, $method), $parameters);
		}

		if(method_exists($this, $method)){
			return call_user_func_array(array($this, $method), $parameters);
		}

		throw new BadMethodCallException("Method $method don't exists.");

    }

}